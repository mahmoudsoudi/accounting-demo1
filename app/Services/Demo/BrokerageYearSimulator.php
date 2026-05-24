<?php

namespace App\Services\Demo;

use App\Enums\PaymentMethod;
use App\Models\Account;
use App\Models\Agent;
use App\Models\Client;
use App\Models\Commission;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Listing;
use App\Models\Payment;
use App\Models\Property;
use App\Models\TransactionTemplate;
use App\Models\User;
use App\Services\Accounting\TransactionTemplateService;
use App\Services\Calculators\CommissionCalculatorService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BrokerageYearSimulator
{
    /** @var array<int, int> */
    protected array $monthlyDealCounts = [
        1 => 1, 2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 4,
        7 => 3, 8 => 3, 9 => 2, 10 => 2, 11 => 1, 12 => 1,
    ];

    /** @var array<string> */
    protected array $expenseVendors = [
        'Home Depot', 'Rona', 'Staples', 'Bell Canada', 'Hydro-Quebec',
        'Desjardins Insurance', 'Royal LePage Marketing', 'Google Ads',
        'QuickBooks', 'Montreal Cleaning Co.',
    ];

    /** @var array<string> */
    protected array $expenseCategories = [
        'maintenance', 'utilities', 'marketing', 'insurance', 'office', 'supplies',
    ];

    protected Collection $agents;

    protected Collection $properties;

    protected Collection $rentalProperties;

    protected int $entrySequence = 0;

    protected int $invoiceSequence = 0;

    protected int $paymentSequence = 0;

    protected int $expenseSequence = 0;

    protected ?Account $revenueAccount = null;

    protected ?Account $maintenanceAccount = null;

    public function __construct(
        protected TransactionTemplateService $templateService,
        protected CommissionCalculatorService $commissionCalculator,
    ) {}

    public function run(User $user, ?int $year = null): array
    {
        $year ??= now()->subYear()->year;
        $this->entrySequence = 0;
        $this->invoiceSequence = 0;
        $this->paymentSequence = 0;
        $this->expenseSequence = 0;

        $this->revenueAccount = Account::where('code', '4010')->first();
        $this->maintenanceAccount = Account::where('code', '6050')->first();

        $this->seedPeopleAndProperties();

        $stats = [
            'year' => $year,
            'agents' => $this->agents->count(),
            'clients' => Client::where('email', 'like', 'demo-%')->count(),
            'properties' => $this->properties->count(),
            'deals_closed' => 0,
            'invoices' => 0,
            'payments' => 0,
            'expenses' => 0,
            'journal_entries' => 0,
        ];

        $stats['journal_entries'] += $this->simulateCommissionDeals($user, $year, $stats);
        $stats['journal_entries'] += $this->simulateServiceInvoices($user, $year, $stats);
        $stats['journal_entries'] += $this->simulateRentalIncome($user, $year);
        $stats['journal_entries'] += $this->simulateOperatingExpenses($user, $year, $stats);
        $stats['journal_entries'] += $this->simulateQuarterlyTaxInstallments($user, $year);

        return $stats;
    }

    protected function demoReference(string $prefix, Carbon $date): string
    {
        $this->entrySequence++;

        return sprintf('DEMO-%s-%s-%04d', $prefix, $date->format('Ymd'), $this->entrySequence);
    }

    protected function demoInvoiceNumber(int $year): string
    {
        $this->invoiceSequence++;

        return sprintf('DEMO-INV-%d-%04d', $year, $this->invoiceSequence);
    }

    protected function demoPaymentNumber(int $year): string
    {
        $this->paymentSequence++;

        return sprintf('DEMO-PAY-%d-%04d', $year, $this->paymentSequence);
    }

    protected function demoExpenseNumber(int $year): string
    {
        $this->expenseSequence++;

        return sprintf('DEMO-EXP-%d-%04d', $year, $this->expenseSequence);
    }

    protected function seedPeopleAndProperties(): void
    {
        $this->agents = collect([
            ['first_name' => 'Sarah', 'last_name' => 'Chen', 'email' => 'demo-sarah.chen@brokerage.test', 'commission_rate' => 0.60],
            ['first_name' => 'Marc', 'last_name' => 'Tremblay', 'email' => 'demo-marc.tremblay@brokerage.test', 'commission_rate' => 0.65],
            ['first_name' => 'Aisha', 'last_name' => 'Patel', 'email' => 'demo-aisha.patel@brokerage.test', 'commission_rate' => 0.70],
            ['first_name' => 'James', 'last_name' => 'OBrien', 'email' => 'demo-james.obrien@brokerage.test', 'commission_rate' => 0.75],
        ])->map(fn (array $data) => Agent::create([
            ...$data,
            'phone' => '514-555-0100',
            'license_number' => 'DEMO-'.strtoupper(substr($data['last_name'], 0, 4)).'-'.random_int(1000, 9999),
            'is_active' => true,
        ]));

        $clients = [
            ['first_name' => 'Emily', 'last_name' => 'Wong', 'city' => 'Montreal', 'province' => 'QC'],
            ['first_name' => 'David', 'last_name' => 'Gagnon', 'city' => 'Quebec City', 'province' => 'QC'],
            ['first_name' => 'Fatima', 'last_name' => 'Hassan', 'city' => 'Laval', 'province' => 'QC'],
            ['first_name' => 'Robert', 'last_name' => 'MacDonald', 'city' => 'Ottawa', 'province' => 'ON'],
            ['first_name' => 'Sophie', 'last_name' => 'Roy', 'city' => 'Gatineau', 'province' => 'QC'],
            ['first_name' => 'Michael', 'last_name' => 'Singh', 'city' => 'Toronto', 'province' => 'ON'],
            ['first_name' => 'Isabelle', 'last_name' => 'Fortin', 'city' => 'Sherbrooke', 'province' => 'QC'],
            ['first_name' => 'Kevin', 'last_name' => 'Nguyen', 'city' => 'Brossard', 'province' => 'QC'],
            ['first_name' => 'Laura', 'last_name' => 'Bergeron', 'city' => 'Longueuil', 'province' => 'QC'],
            ['first_name' => 'Thomas', 'last_name' => 'Clark', 'city' => 'Kingston', 'province' => 'ON'],
            ['first_name' => 'Nadia', 'last_name' => 'Khalil', 'city' => 'Montreal', 'province' => 'QC'],
            ['first_name' => 'Philippe', 'last_name' => 'Dubois', 'city' => 'Trois-Rivieres', 'province' => 'QC'],
        ];

        $streets = [
            '1247 Rue Sherbrooke', '88 Avenue du Parc', '452 Maple Street', '19 Lakeshore Drive',
            '3300 Boul. Saint-Laurent', '56 King Street West', '901 Cedar Lane', '14 Oak Avenue',
            '2200 Rue Sainte-Catherine', '73 Wellington Street', '615 Pine Road', '28 Elm Court',
            '1050 Rue Peel', '412 Main Street', '77 Riverside Blvd', '3 Summit Place',
        ];

        $this->properties = collect();
        $this->rentalProperties = collect();

        foreach ($clients as $index => $clientData) {
            $client = Client::create([
                ...$clientData,
                'email' => 'demo-'.strtolower($clientData['last_name']).'@client.test',
                'phone' => '514-555-'.str_pad((string) (2000 + $index), 4, '0', STR_PAD_LEFT),
                'address' => $streets[$index],
                'postal_code' => 'H'.random_int(1, 9).'A '.random_int(1, 9).'B'.random_int(1, 9),
                'is_active' => true,
            ]);

            $salePrice = random_int(350, 1200) * 1000;
            $property = Property::create([
                'address' => $streets[$index],
                'city' => $clientData['city'],
                'province' => $clientData['province'],
                'postal_code' => $client->postal_code,
                'property_type' => $index % 3 === 0 ? 'condo' : 'house',
                'client_id' => $client->id,
                'purchase_price' => $salePrice,
                'current_value' => $salePrice,
                'is_active' => true,
            ]);

            $this->properties->push($property);

            if ($index < 4) {
                $this->rentalProperties->push($property);
            }
        }
    }

    protected function simulateCommissionDeals(User $user, int $year, array &$stats): int
    {
        $incomeTemplate = TransactionTemplate::where('slug', 'brokerage_commission_income')->firstOrFail();
        $payoutTemplate = TransactionTemplate::where('slug', 'agent_commission_payout')->firstOrFail();
        $paymentTemplate = TransactionTemplate::where('slug', 'invoice_payment_bank')->firstOrFail();

        $entriesCreated = 0;
        $propertyIndex = 0;

        foreach ($this->monthlyDealCounts as $month => $dealCount) {
            for ($deal = 0; $deal < $dealCount; $deal++) {
                $agent = $this->agents->random();
                $property = $this->properties[$propertyIndex % $this->properties->count()];
                $propertyIndex++;

                $client = $property->client;
                $salePrice = (float) ($property->current_value ?? random_int(400, 900) * 1000);
                $agentSplitPercent = (float) $agent->commission_rate * 100;

                $commission = $this->commissionCalculator->calculate(
                    $salePrice,
                    5.0,
                    $agentSplitPercent,
                );

                $closeDate = Carbon::create($year, $month, random_int(1, 28));
                $listedAt = $closeDate->copy()->subDays(random_int(30, 120));

                $listing = Listing::create([
                    'property_id' => $property->id,
                    'agent_id' => $agent->id,
                    'listing_type' => 'sale',
                    'list_price' => $salePrice,
                    'status' => 'sold',
                    'listed_at' => $listedAt,
                    'sold_at' => $closeDate,
                ]);

                $totalCommission = $commission['total_commission'];
                $taxAmount = round($totalCommission * 0.05, 2);
                $invoiceTotal = $totalCommission + $taxAmount;

                $incomeEntry = $this->templateService->apply($incomeTemplate, [
                    'amount' => $totalCommission,
                    'agent_amount' => $commission['agent_amount'],
                    'commission_rate' => $commission['agent_amount'] / max($totalCommission, 1),
                    'agent' => $agent->full_name,
                    'property' => $property->address,
                    'client' => $client->full_name,
                    'transaction_date' => $closeDate->toDateString(),
                    'notes' => "Demo deal closed — {$property->address}",
                ], $user, $this->demoReference('COM', $closeDate));

                $invoice = $this->createInvoice(
                    user: $user,
                    year: $year,
                    client: $client,
                    property: $property,
                    agent: $agent,
                    invoiceDate: $closeDate,
                    dueDate: $closeDate->copy()->addDays(30),
                    subtotal: $totalCommission,
                    taxAmount: $taxAmount,
                    total: $invoiceTotal,
                    description: "Brokerage commission — {$property->address}",
                    journalEntryId: $incomeEntry->id,
                    status: 'sent',
                    notes: "Commission invoice for closed sale at {$property->address}",
                );
                $stats['invoices']++;

                Commission::create([
                    'property_id' => $property->id,
                    'agent_id' => $agent->id,
                    'listing_id' => $listing->id,
                    'sale_price' => $commission['sale_price'],
                    'commission_rate' => 5.0,
                    'brokerage_amount' => $commission['brokerage_amount'],
                    'agent_amount' => $commission['agent_amount'],
                    'status' => 'recorded',
                    'journal_entry_id' => $incomeEntry->id,
                ]);

                $entriesCreated++;
                $stats['deals_closed']++;

                if (random_int(0, 100) > 15) {
                    $paymentDate = $closeDate->copy()->addDays(random_int(14, 30));
                    $paymentEntry = $this->templateService->apply($paymentTemplate, [
                        'amount' => $invoiceTotal,
                        'client' => $client->full_name,
                        'transaction_date' => $paymentDate->toDateString(),
                        'notes' => "Commission receivable collected — {$client->full_name}",
                    ], $user, $this->demoReference('PAY', $paymentDate));

                    $this->createPayment(
                        user: $user,
                        year: $year,
                        invoice: $invoice,
                        paymentDate: $paymentDate,
                        amount: $invoiceTotal,
                        method: PaymentMethod::Bank,
                        journalEntryId: $paymentEntry->id,
                        reference: 'EFT-'.str_pad((string) $stats['payments'] + 1, 5, '0', STR_PAD_LEFT),
                        notes: "Bank transfer for {$invoice->invoice_number}",
                    );

                    $invoice->update(['status' => 'paid']);
                    $stats['payments']++;
                    $entriesCreated++;
                } else {
                    $invoice->update(['status' => random_int(0, 1) ? 'sent' : 'overdue']);
                }

                if (random_int(0, 100) > 20) {
                    $payoutDate = $closeDate->copy()->addDays(random_int(30, 75));
                    $this->templateService->apply($payoutTemplate, [
                        'amount' => $commission['agent_amount'],
                        'agent' => $agent->full_name,
                        'commission_rate' => $agent->commission_rate,
                        'transaction_date' => $payoutDate->toDateString(),
                        'notes' => "Agent commission payout — {$agent->full_name}",
                    ], $user, $this->demoReference('POUT', $payoutDate));
                    $entriesCreated++;
                }
            }
        }

        return $entriesCreated;
    }

    protected function simulateServiceInvoices(User $user, int $year, array &$stats): int
    {
        $invoiceTemplate = TransactionTemplate::where('slug', 'invoice_creation')->firstOrFail();
        $paymentMethods = [
            PaymentMethod::Bank->value => 'invoice_payment_bank',
            PaymentMethod::Stripe->value => 'invoice_payment_stripe',
            PaymentMethod::Cash->value => 'invoice_payment_cash',
        ];

        $entriesCreated = 0;
        $serviceDescriptions = [
            'Listing photography package',
            'Virtual staging services',
            'Marketing campaign — social media',
            'Open house coordination',
            'Comparative market analysis',
        ];

        foreach (range(1, 12) as $month) {
            $invoiceCount = random_int(1, 2);

            for ($i = 0; $i < $invoiceCount; $i++) {
                $client = Client::where('email', 'like', 'demo-%')->inRandomOrder()->first();
                $property = $this->properties->random();
                $agent = $this->agents->random();
                $invoiceDate = Carbon::create($year, $month, random_int(1, 25));
                $subtotal = random_int(250, 2500);
                $taxAmount = round($subtotal * 0.05, 2);
                $total = $subtotal + $taxAmount;
                $description = $serviceDescriptions[array_rand($serviceDescriptions)];

                $entry = $this->templateService->apply($invoiceTemplate, [
                    'amount' => $total,
                    'tax' => $taxAmount,
                    'client' => $client->full_name,
                    'property' => $property->address,
                    'transaction_date' => $invoiceDate->toDateString(),
                    'notes' => "Service invoice — {$description}",
                ], $user, $this->demoReference('SINV', $invoiceDate));

                $invoice = $this->createInvoice(
                    user: $user,
                    year: $year,
                    client: $client,
                    property: $property,
                    agent: $agent,
                    invoiceDate: $invoiceDate,
                    dueDate: $invoiceDate->copy()->addDays(30),
                    subtotal: $subtotal,
                    taxAmount: $taxAmount,
                    total: $total,
                    description: $description,
                    journalEntryId: $entry->id,
                    status: 'sent',
                );
                $stats['invoices']++;
                $entriesCreated++;

                if (random_int(0, 100) > 25) {
                    $methodValue = array_rand($paymentMethods);
                    $paymentDate = $invoiceDate->copy()->addDays(random_int(5, 45));
                    $paymentTemplate = TransactionTemplate::where('slug', $paymentMethods[$methodValue])->firstOrFail();

                    $paymentEntry = $this->templateService->apply($paymentTemplate, [
                        'amount' => $total,
                        'client' => $client->full_name,
                        'transaction_date' => $paymentDate->toDateString(),
                        'notes' => "Payment for {$invoice->invoice_number}",
                    ], $user, $this->demoReference('SPAY', $paymentDate));

                    $this->createPayment(
                        user: $user,
                        year: $year,
                        invoice: $invoice,
                        paymentDate: $paymentDate,
                        amount: $total,
                        method: PaymentMethod::from($methodValue),
                        journalEntryId: $paymentEntry->id,
                        notes: "Payment for service invoice {$invoice->invoice_number}",
                    );

                    $invoice->update(['status' => 'paid']);
                    $stats['payments']++;
                    $entriesCreated++;
                }
            }
        }

        return $entriesCreated;
    }

    protected function simulateRentalIncome(User $user, int $year): int
    {
        $template = TransactionTemplate::where('slug', 'rental_income')->firstOrFail();
        $entriesCreated = 0;

        foreach ($this->rentalProperties as $property) {
            for ($month = 1; $month <= 12; $month++) {
                $rentAmount = random_int(18, 35) * 100;
                $rentDate = Carbon::create($year, $month, random_int(1, 5));

                $this->templateService->apply($template, [
                    'amount' => $rentAmount,
                    'property' => $property->address,
                    'client' => $property->client?->full_name ?? 'Tenant',
                    'transaction_date' => $rentDate->toDateString(),
                    'notes' => "Monthly rental income — {$property->address}",
                ], $user, $this->demoReference('RENT', $rentDate));

                $entriesCreated++;
            }
        }

        return $entriesCreated;
    }

    protected function simulateOperatingExpenses(User $user, int $year, array &$stats): int
    {
        $expenseTemplates = [
            PaymentMethod::Bank->value => 'expense_paid_bank',
            PaymentMethod::CreditCard->value => 'expense_paid_credit_card',
            PaymentMethod::Cash->value => 'expense_paid_cash',
        ];
        $insuranceTemplate = TransactionTemplate::where('slug', 'insurance_payment')->firstOrFail();
        $entriesCreated = 0;

        foreach ($this->properties->random(min(8, $this->properties->count())) as $property) {
            for ($month = 1; $month <= 12; $month += random_int(2, 4)) {
                $expenseDate = Carbon::create($year, $month, random_int(5, 25));
                $amount = random_int(150, 900);
                $taxAmount = round($amount * 0.05, 2);
                $total = $amount + $taxAmount;
                $methodValue = array_rand($expenseTemplates);
                $method = PaymentMethod::from($methodValue);
                $template = TransactionTemplate::where('slug', $expenseTemplates[$methodValue])->firstOrFail();

                $entry = $this->templateService->apply($template, [
                    'amount' => $total,
                    'property' => $property->address,
                    'transaction_date' => $expenseDate->toDateString(),
                    'notes' => "Property maintenance — {$property->address}",
                ], $user, $this->demoReference('EXP', $expenseDate));

                $this->createExpense(
                    user: $user,
                    year: $year,
                    property: $property,
                    expenseDate: $expenseDate,
                    amount: $amount,
                    taxAmount: $taxAmount,
                    total: $total,
                    category: 'maintenance',
                    paymentMethod: $method,
                    journalEntryId: $entry->id,
                    approved: random_int(0, 100) > 10,
                );
                $stats['expenses']++;
                $entriesCreated++;
            }
        }

        foreach ($this->rentalProperties as $property) {
            $insuranceDate = Carbon::create($year, random_int(1, 12), random_int(1, 28));
            $amount = random_int(1200, 2800);
            $taxAmount = round($amount * 0.05, 2);
            $total = $amount + $taxAmount;

            $entry = $this->templateService->apply($insuranceTemplate, [
                'amount' => $total,
                'property' => $property->address,
                'transaction_date' => $insuranceDate->toDateString(),
                'notes' => "Annual insurance premium — {$property->address}",
            ], $user, $this->demoReference('INS', $insuranceDate));

            $this->createExpense(
                user: $user,
                year: $year,
                property: $property,
                expenseDate: $insuranceDate,
                amount: $amount,
                taxAmount: $taxAmount,
                total: $total,
                category: 'insurance',
                paymentMethod: PaymentMethod::Bank,
                journalEntryId: $entry->id,
                vendorName: 'Desjardins Insurance',
                approved: true,
            );
            $stats['expenses']++;
            $entriesCreated++;
        }

        foreach (range(1, 12) as $month) {
            if (random_int(0, 100) > 40) {
                continue;
            }

            $expenseDate = Carbon::create($year, $month, random_int(1, 28));
            $amount = random_int(80, 600);
            $taxAmount = round($amount * 0.05, 2);
            $total = $amount + $taxAmount;
            $method = PaymentMethod::Bank;
            $template = TransactionTemplate::where('slug', 'expense_paid_bank')->firstOrFail();

            $entry = $this->templateService->apply($template, [
                'amount' => $total,
                'property' => 'Office',
                'transaction_date' => $expenseDate->toDateString(),
                'notes' => 'General office expense',
            ], $user, $this->demoReference('OEXP', $expenseDate));

            $this->createExpense(
                user: $user,
                year: $year,
                property: null,
                expenseDate: $expenseDate,
                amount: $amount,
                taxAmount: $taxAmount,
                total: $total,
                category: $this->expenseCategories[array_rand($this->expenseCategories)],
                paymentMethod: $method,
                journalEntryId: $entry->id,
                vendorName: $this->expenseVendors[array_rand($this->expenseVendors)],
                approved: random_int(0, 100) > 20,
            );
            $stats['expenses']++;
            $entriesCreated++;
        }

        return $entriesCreated;
    }

    protected function simulateQuarterlyTaxInstallments(User $user, int $year): int
    {
        $template = TransactionTemplate::where('slug', 'income_tax_installment')->firstOrFail();
        $entriesCreated = 0;

        foreach ([3, 6, 9, 12] as $month) {
            $this->templateService->apply($template, [
                'amount' => random_int(4000, 9000),
                'fiscal_year' => (string) $year,
                'transaction_date' => Carbon::create($year, $month, 15)->toDateString(),
                'notes' => "Corporate tax installment — FY {$year}",
            ], $user, $this->demoReference('TAX', Carbon::create($year, $month, 15)));

            $entriesCreated++;
        }

        return $entriesCreated;
    }

    protected function createInvoice(
        User $user,
        int $year,
        Client $client,
        Property $property,
        Agent $agent,
        Carbon $invoiceDate,
        Carbon $dueDate,
        float $subtotal,
        float $taxAmount,
        float $total,
        string $description,
        int $journalEntryId,
        string $status = 'sent',
        ?string $notes = null,
    ): Invoice {
        $invoice = Invoice::create([
            'invoice_number' => $this->demoInvoiceNumber($year),
            'client_id' => $client->id,
            'property_id' => $property->id,
            'agent_id' => $agent->id,
            'invoice_date' => $invoiceDate,
            'due_date' => $dueDate,
            'status' => $status,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'notes' => $notes,
            'journal_entry_id' => $journalEntryId,
            'created_by' => $user->id,
        ]);

        $invoice->lines()->create([
            'description' => $description,
            'quantity' => 1,
            'unit_price' => $subtotal,
            'amount' => $subtotal,
            'account_id' => $this->revenueAccount?->id,
        ]);

        return $invoice;
    }

    protected function createPayment(
        User $user,
        int $year,
        Invoice $invoice,
        Carbon $paymentDate,
        float $amount,
        PaymentMethod $method,
        int $journalEntryId,
        ?string $reference = null,
        ?string $notes = null,
    ): Payment {
        return Payment::create([
            'payment_number' => $this->demoPaymentNumber($year),
            'payable_type' => Invoice::class,
            'payable_id' => $invoice->id,
            'payment_date' => $paymentDate,
            'amount' => $amount,
            'payment_method' => $method,
            'reference' => $reference,
            'notes' => $notes,
            'journal_entry_id' => $journalEntryId,
            'created_by' => $user->id,
        ]);
    }

    protected function createExpense(
        User $user,
        int $year,
        ?Property $property,
        Carbon $expenseDate,
        float $amount,
        float $taxAmount,
        float $total,
        string $category,
        PaymentMethod $paymentMethod,
        int $journalEntryId,
        ?string $vendorName = null,
        bool $approved = false,
    ): Expense {
        return Expense::create([
            'expense_number' => $this->demoExpenseNumber($year),
            'vendor_name' => $vendorName ?? $this->expenseVendors[array_rand($this->expenseVendors)],
            'property_id' => $property?->id,
            'agent_id' => $property ? $this->agents->random()->id : null,
            'expense_date' => $expenseDate,
            'category' => $category,
            'status' => $approved ? 'approved' : 'pending',
            'amount' => $amount,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'payment_method' => $paymentMethod,
            'account_id' => $this->maintenanceAccount?->id,
            'notes' => $property ? "Demo expense — {$property->address}" : 'Demo office expense',
            'journal_entry_id' => $journalEntryId,
            'created_by' => $user->id,
            'approved_by' => $approved ? $user->id : null,
            'approved_at' => $approved ? $expenseDate->copy()->addDays(random_int(1, 5)) : null,
        ]);
    }
}
