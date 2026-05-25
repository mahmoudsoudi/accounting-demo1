<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Enums\NormalBalance;
use App\Models\Account;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // Assets
            ['1000', 'Assets', AccountType::Asset, null, NormalBalance::Debit, 'Top-level asset group', [
                'fr' => 'Actifs',
                'ar' => 'الأصول',
            ], [
                'fr' => 'Groupe d\'actifs de premier niveau',
                'ar' => 'مجموعة الأصول الرئيسية',
            ]],
            ['1010', 'Cash on Hand', AccountType::Asset, '1000', NormalBalance::Debit, 'Petty cash and cash drawers', [
                'fr' => 'Caisse',
                'ar' => 'النقد في الصندوق',
            ], [
                'fr' => 'Petite caisse et tiroirs-caisses',
                'ar' => 'الصندوق الصغير وأدراج النقد',
            ]],
            ['1020', 'Operating Bank Account', AccountType::Asset, '1000', NormalBalance::Debit, 'Primary operating chequing account', [
                'fr' => 'Compte bancaire d\'exploitation',
                'ar' => 'الحساب البنكي التشغيلي',
            ], [
                'fr' => 'Compte chèques d\'exploitation principal',
                'ar' => 'الحساب الجاري التشغيلي الرئيسي',
            ]],
            ['1030', 'Trust / Client Funds', AccountType::Asset, '1000', NormalBalance::Debit, 'Segregated client trust funds', [
                'fr' => 'Fonds fiduciaires / clients',
                'ar' => 'أموال العملاء / الائتمان',
            ], [
                'fr' => 'Fonds fiduciaires clients séparés',
                'ar' => 'أموال ائتمان العملاء المنفصلة',
            ]],
            ['1040', 'Stripe Clearing', AccountType::Asset, '1000', NormalBalance::Debit, 'Stripe payment clearing account', [
                'fr' => 'Compensation Stripe',
                'ar' => 'حساب تسوية Stripe',
            ], [
                'fr' => 'Compte de compensation des paiements Stripe',
                'ar' => 'حساب تسوية مدفوعات Stripe',
            ]],
            ['1100', 'Accounts Receivable', AccountType::Asset, '1000', NormalBalance::Debit, 'Trade receivables', [
                'fr' => 'Comptes clients',
                'ar' => 'حسابات مدينة',
            ], [
                'fr' => 'Créances commerciales',
                'ar' => 'ذمم مدينة تجارية',
            ]],
            ['1110', 'Commission Receivable', AccountType::Asset, '1100', NormalBalance::Debit, 'Commissions due from deals', [
                'fr' => 'Commissions à recevoir',
                'ar' => 'عمولات مستحقة',
            ], [
                'fr' => 'Commissions dues sur les transactions',
                'ar' => 'عمولات مستحقة من الصفقات',
            ]],
            ['1200', 'Prepaid Expenses', AccountType::Asset, '1000', NormalBalance::Debit, 'Prepaid insurance, taxes, etc.', [
                'fr' => 'Charges payées d\'avance',
                'ar' => 'مصروفات مدفوعة مقدماً',
            ], [
                'fr' => 'Assurances, taxes, etc. payées d\'avance',
                'ar' => 'تأمين وضرائب مدفوعة مقدماً وغيرها',
            ]],
            ['1210', 'Income Tax Prepaid', AccountType::Asset, '1000', NormalBalance::Debit, 'Income tax installments paid in advance', [
                'fr' => 'Impôt sur le revenu payé d\'avance',
                'ar' => 'ضريبة دخل مدفوعة مقدماً',
            ], [
                'fr' => 'Acomptes provisionnels d\'impôt sur le revenu',
                'ar' => 'أقساط ضريبة الدخل المدفوعة مقدماً',
            ]],
            ['1300', 'Property & Equipment', AccountType::Asset, '1000', NormalBalance::Debit, 'Office and property assets', [
                'fr' => 'Immobilisations corporelles',
                'ar' => 'الممتلكات والمعدات',
            ], [
                'fr' => 'Actifs de bureau et immobiliers',
                'ar' => 'أصول المكتب والعقارات',
            ]],

            // Liabilities
            ['2000', 'Liabilities', AccountType::Liability, null, NormalBalance::Credit, 'Top-level liability group', [
                'fr' => 'Passifs',
                'ar' => 'الخصوم',
            ], [
                'fr' => 'Groupe de passifs de premier niveau',
                'ar' => 'مجموعة الخصوم الرئيسية',
            ]],
            ['2010', 'Accounts Payable', AccountType::Liability, '2000', NormalBalance::Credit, 'Trade payables', [
                'fr' => 'Comptes fournisseurs',
                'ar' => 'حسابات دائنة',
            ], [
                'fr' => 'Dettes commerciales',
                'ar' => 'ذمم دائنة تجارية',
            ]],
            ['2020', 'Agent Commission Payable', AccountType::Liability, '2000', NormalBalance::Credit, 'Commissions owed to agents', [
                'fr' => 'Commissions dues aux agents',
                'ar' => 'عمولات مستحقة للوكلاء',
            ], [
                'fr' => 'Commissions dues aux agents',
                'ar' => 'عمولات مستحقة للوكلاء',
            ]],
            ['2030', 'Trust Funds Payable', AccountType::Liability, '2000', NormalBalance::Credit, 'Client trust obligations', [
                'fr' => 'Fonds fiduciaires à payer',
                'ar' => 'التزامات أموال العملاء',
            ], [
                'fr' => 'Obligations fiduciaires envers les clients',
                'ar' => 'التزامات ائتمان العملاء',
            ]],
            ['2040', 'GST/HST Payable', AccountType::Liability, '2000', NormalBalance::Credit, 'Sales tax collected', [
                'fr' => 'TPS/TVH à payer',
                'ar' => 'ضريبة المبيعات المستحقة',
            ], [
                'fr' => 'Taxes de vente perçues',
                'ar' => 'ضريبة المبيعات المحصلة',
            ]],
            ['2050', 'Credit Card Payable', AccountType::Liability, '2000', NormalBalance::Credit, 'Corporate credit card balance', [
                'fr' => 'Carte de crédit à payer',
                'ar' => 'بطاقة ائتمان مستحقة',
            ], [
                'fr' => 'Solde de carte de crédit corporative',
                'ar' => 'رصيد بطاقة الائتمان للشركة',
            ]],
            ['2060', 'Income Tax Payable', AccountType::Liability, '2000', NormalBalance::Credit, 'Corporate income tax owed to CRA', [
                'fr' => 'Impôt sur le revenu à payer',
                'ar' => 'ضريبة دخل مستحقة',
            ], [
                'fr' => 'Impôt sur le revenu des sociétés dû à l\'ARC',
                'ar' => 'ضريبة دخل الشركات المستحقة',
            ]],
            ['2100', 'Mortgage Payable', AccountType::Liability, '2000', NormalBalance::Credit, 'Mortgage principal owed', [
                'fr' => 'Hypothèque à payer',
                'ar' => 'رهن عقاري مستحق',
            ], [
                'fr' => 'Capital hypothécaire dû',
                'ar' => 'أصل الرهن العقاري المستحق',
            ]],
            ['2110', 'Mortgage Interest Payable', AccountType::Liability, '2000', NormalBalance::Credit, 'Accrued mortgage interest', [
                'fr' => 'Intérêts hypothécaires à payer',
                'ar' => 'فائدة رهن عقاري مستحقة',
            ], [
                'fr' => 'Intérêts hypothécaires courus',
                'ar' => 'فائدة الرهن العقاري المستحقة',
            ]],

            // Equity
            ['3000', 'Equity', AccountType::Equity, null, NormalBalance::Credit, 'Owner equity', [
                'fr' => 'Capitaux propres',
                'ar' => 'حقوق الملكية',
            ], [
                'fr' => 'Capitaux propres du propriétaire',
                'ar' => 'حقوق ملكية المالك',
            ]],
            ['3010', 'Retained Earnings', AccountType::Equity, '3000', NormalBalance::Credit, 'Accumulated retained earnings', [
                'fr' => 'Bénéfices non répartis',
                'ar' => 'الأرباح المحتجزة',
            ], [
                'fr' => 'Bénéfices non répartis accumulés',
                'ar' => 'الأرباح المحتجزة المتراكمة',
            ]],
            ['3020', 'Owner Drawings', AccountType::Equity, '3000', NormalBalance::Debit, 'Owner withdrawals', [
                'fr' => 'Retraits du propriétaire',
                'ar' => 'مسحوبات المالك',
            ], [
                'fr' => 'Retraits du propriétaire',
                'ar' => 'مسحوبات المالك',
            ]],

            // Revenue
            ['4000', 'Revenue', AccountType::Revenue, null, NormalBalance::Credit, 'Top-level revenue group', [
                'fr' => 'Revenus',
                'ar' => 'الإيرادات',
            ], [
                'fr' => 'Groupe de revenus de premier niveau',
                'ar' => 'مجموعة الإيرادات الرئيسية',
            ]],
            ['4010', 'Brokerage Commission Income', AccountType::Revenue, '4000', NormalBalance::Credit, 'Commission income from transactions', [
                'fr' => 'Revenus de commissions de courtage',
                'ar' => 'إيرادات عمولات الوساطة',
            ], [
                'fr' => 'Revenus de commissions sur les transactions',
                'ar' => 'إيرادات العمولات من الصفقات',
            ]],
            ['4020', 'Property Management Income', AccountType::Revenue, '4000', NormalBalance::Credit, 'Property management fees', [
                'fr' => 'Revenus de gestion immobilière',
                'ar' => 'إيرادات إدارة العقارات',
            ], [
                'fr' => 'Honoraires de gestion immobilière',
                'ar' => 'رسوم إدارة العقارات',
            ]],
            ['4030', 'Rental Income', AccountType::Revenue, '4000', NormalBalance::Credit, 'Rental property income', [
                'fr' => 'Revenus locatifs',
                'ar' => 'إيرادات الإيجار',
            ], [
                'fr' => 'Revenus de location immobilière',
                'ar' => 'إيرادات تأجير العقارات',
            ]],
            ['4040', 'Listing Fee Income', AccountType::Revenue, '4000', NormalBalance::Credit, 'Listing and marketing fees', [
                'fr' => 'Revenus de frais d\'inscription',
                'ar' => 'إيرادات رسوم الإدراج',
            ], [
                'fr' => 'Frais d\'inscription et de marketing',
                'ar' => 'رسوم الإدراج والتسويق',
            ]],
            ['4050', 'Other Income', AccountType::Revenue, '4000', NormalBalance::Credit, 'Miscellaneous income', [
                'fr' => 'Autres revenus',
                'ar' => 'إيرادات أخرى',
            ], [
                'fr' => 'Revenus divers',
                'ar' => 'إيرادات متنوعة',
            ]],

            // Cost of Sales
            ['5000', 'Cost of Sales', AccountType::CostOfSales, null, NormalBalance::Debit, 'Direct costs of brokerage services', [
                'fr' => 'Coût des ventes',
                'ar' => 'تكلفة المبيعات',
            ], [
                'fr' => 'Coûts directs des services de courtage',
                'ar' => 'التكاليف المباشرة لخدمات الوساطة',
            ]],
            ['5010', 'Agent Commission Expense', AccountType::CostOfSales, '5000', NormalBalance::Debit, 'Agent commission payouts', [
                'fr' => 'Commissions versées aux agents',
                'ar' => 'مصروف عمولات الوكلاء',
            ], [
                'fr' => 'Paiements de commissions aux agents',
                'ar' => 'مدفوعات عمولات الوكلاء',
            ]],
            ['5020', 'Referral Fees', AccountType::CostOfSales, '5000', NormalBalance::Debit, 'Referral commission costs', [
                'fr' => 'Frais de référencement',
                'ar' => 'رسوم الإحالة',
            ], [
                'fr' => 'Coûts de commissions de référencement',
                'ar' => 'تكاليف عمولات الإحالة',
            ]],

            // Operating Expenses
            ['6000', 'Operating Expenses', AccountType::Expense, null, NormalBalance::Debit, 'Top-level expense group', [
                'fr' => 'Charges d\'exploitation',
                'ar' => 'المصروفات التشغيلية',
            ], [
                'fr' => 'Groupe de charges d\'exploitation de premier niveau',
                'ar' => 'مجموعة المصروفات التشغيلية الرئيسية',
            ]],
            ['6010', 'Listing Expenses', AccountType::Expense, '6000', NormalBalance::Debit, 'Listing-related costs', [
                'fr' => 'Frais d\'inscription',
                'ar' => 'مصروفات الإدراج',
            ], [
                'fr' => 'Coûts liés aux inscriptions',
                'ar' => 'تكاليف متعلقة بالإدراج',
            ]],
            ['6020', 'Marketing Expenses', AccountType::Expense, '6000', NormalBalance::Debit, 'Advertising and marketing', [
                'fr' => 'Frais de marketing',
                'ar' => 'مصروفات التسويق',
            ], [
                'fr' => 'Publicité et marketing',
                'ar' => 'الإعلان والتسويق',
            ]],
            ['6030', 'Office Rent', AccountType::Expense, '6000', NormalBalance::Debit, 'Office lease costs', [
                'fr' => 'Loyer de bureau',
                'ar' => 'إيجار المكتب',
            ], [
                'fr' => 'Coûts de location de bureau',
                'ar' => 'تكاليف إيجار المكتب',
            ]],
            ['6040', 'Insurance Expense', AccountType::Expense, '6000', NormalBalance::Debit, 'Property and liability insurance', [
                'fr' => 'Assurance',
                'ar' => 'مصروف التأمين',
            ], [
                'fr' => 'Assurance responsabilité et biens',
                'ar' => 'تأمين الممتلكات والمسؤولية',
            ]],
            ['6050', 'Maintenance Expense', AccountType::Expense, '6000', NormalBalance::Debit, 'Property maintenance and repairs', [
                'fr' => 'Entretien',
                'ar' => 'مصروف الصيانة',
            ], [
                'fr' => 'Entretien et réparations immobilières',
                'ar' => 'صيانة وإصلاح العقارات',
            ]],
            ['6060', 'Property Tax Expense', AccountType::Expense, '6000', NormalBalance::Debit, 'Municipal property taxes', [
                'fr' => 'Taxes foncières',
                'ar' => 'مصروف ضريبة العقار',
            ], [
                'fr' => 'Taxes foncières municipales',
                'ar' => 'ضرائب العقار البلدية',
            ]],
            ['6070', 'Mortgage Interest Expense', AccountType::Expense, '6000', NormalBalance::Debit, 'Mortgage interest paid', [
                'fr' => 'Intérêts hypothécaires',
                'ar' => 'مصروف فائدة الرهن العقاري',
            ], [
                'fr' => 'Intérêts hypothécaires payés',
                'ar' => 'فائدة الرهن العقاري المدفوعة',
            ]],
            ['6080', 'Professional Fees', AccountType::Expense, '6000', NormalBalance::Debit, 'Legal and accounting fees', [
                'fr' => 'Honoraires professionnels',
                'ar' => 'أتعاب مهنية',
            ], [
                'fr' => 'Honoraires juridiques et comptables',
                'ar' => 'أتعاب قانونية ومحاسبية',
            ]],
            ['6090', 'Bank Charges', AccountType::Expense, '6000', NormalBalance::Debit, 'Bank service fees', [
                'fr' => 'Frais bancaires',
                'ar' => 'رسوم بنكية',
            ], [
                'fr' => 'Frais de services bancaires',
                'ar' => 'رسوم الخدمات البنكية',
            ]],
            ['6100', 'Stripe Processing Fees', AccountType::Expense, '6000', NormalBalance::Debit, 'Payment processing fees', [
                'fr' => 'Frais de traitement Stripe',
                'ar' => 'رسوم معالجة Stripe',
            ], [
                'fr' => 'Frais de traitement des paiements',
                'ar' => 'رسوم معالجة المدفوعات',
            ]],
            ['6110', 'Depreciation Expense', AccountType::Expense, '6000', NormalBalance::Debit, 'Depreciation of property and equipment', [
                'fr' => 'Amortissement des immobilisations',
                'ar' => 'مصروف الاستهلاك',
            ], [
                'fr' => 'Amortissement des immobilisations corporelles',
                'ar' => 'استهلاك الممتلكات والمعدات',
            ]],
            ['6115', 'Amortization Expense', AccountType::Expense, '6000', NormalBalance::Debit, 'Amortization of intangible assets', [
                'fr' => 'Amortissement des actifs incorporels',
                'ar' => 'مصروف الإطفاء',
            ], [
                'fr' => 'Amortissement des actifs incorporels',
                'ar' => 'إطفاء الأصول غير الملموسة',
            ]],
            ['6120', 'Income Tax Expense', AccountType::Expense, '6000', NormalBalance::Debit, 'Corporate income tax for the period', [
                'fr' => 'Charge d\'impôt sur le revenu',
                'ar' => 'مصروف ضريبة الدخل',
            ], [
                'fr' => 'Impôt sur le revenu des sociétés pour la période',
                'ar' => 'ضريبة دخل الشركات للفترة',
            ]],
        ];

        $codeToId = [];

        foreach ($accounts as [$code, $name, $type, $parentCode, $balance, $description, $nameTranslations, $descriptionTranslations]) {
            $nameTranslations = array_merge(['en' => $name], $nameTranslations);
            $descriptionTranslations = array_merge(['en' => $description], $descriptionTranslations);

            $account = Account::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'name_translations' => $nameTranslations,
                    'account_type' => $type,
                    'parent_id' => $parentCode ? ($codeToId[$parentCode] ?? null) : null,
                    'normal_balance' => $balance,
                    'is_active' => true,
                    'description' => $description,
                    'description_translations' => $descriptionTranslations,
                ]
            );
            $codeToId[$code] = $account->id;
        }

        foreach ($accounts as [$code, $name, $type, $parentCode]) {
            if ($parentCode) {
                Account::where('code', $code)->update([
                    'parent_id' => $codeToId[$parentCode] ?? null,
                ]);
            }
        }
    }
}
