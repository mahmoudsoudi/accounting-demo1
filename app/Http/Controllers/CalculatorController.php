<?php

namespace App\Http\Controllers;

use App\Services\Calculators\CommissionCalculatorService;
use App\Services\Calculators\MortgageCalculatorService;
use App\Services\Calculators\RentalCalculatorService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalculatorController extends Controller
{
    public function index(): View
    {
        return view('calculators.index');
    }

    public function mortgage(Request $request, MortgageCalculatorService $calculator): View
    {
        $result = null;

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'principal' => ['required', 'numeric', 'min:1'],
                'annual_rate' => ['required', 'numeric', 'min:0'],
                'term_months' => ['required', 'integer', 'min:1'],
            ]);
            $result = $calculator->calculate($data['principal'], $data['annual_rate'], $data['term_months']);
        }

        return view('calculators.mortgage', compact('result'));
    }

    public function rental(Request $request, RentalCalculatorService $calculator): View
    {
        $result = null;

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'monthly_rent' => ['required', 'numeric', 'min:0'],
                'vacancy_rate' => ['nullable', 'numeric', 'min:0'],
                'management_fee_rate' => ['nullable', 'numeric', 'min:0'],
                'maintenance_rate' => ['nullable', 'numeric', 'min:0'],
                'insurance' => ['nullable', 'numeric', 'min:0'],
                'taxes' => ['nullable', 'numeric', 'min:0'],
                'mortgage' => ['nullable', 'numeric', 'min:0'],
                'other_expenses' => ['nullable', 'numeric', 'min:0'],
            ]);
            $result = $calculator->calculate(
                $data['monthly_rent'],
                $data['vacancy_rate'] ?? 5,
                $data['management_fee_rate'] ?? 8,
                $data['maintenance_rate'] ?? 5,
                $data['insurance'] ?? 0,
                $data['taxes'] ?? 0,
                $data['mortgage'] ?? 0,
                $data['other_expenses'] ?? 0
            );
        }

        return view('calculators.rental', compact('result'));
    }

    public function commission(Request $request, CommissionCalculatorService $calculator): View
    {
        $result = null;

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'sale_price' => ['required', 'numeric', 'min:1'],
                'commission_rate' => ['required', 'numeric', 'min:0'],
                'agent_split_rate' => ['required', 'numeric', 'min:0', 'max:100'],
                'referral_fee' => ['nullable', 'numeric', 'min:0'],
            ]);
            $result = $calculator->calculate(
                $data['sale_price'],
                $data['commission_rate'],
                $data['agent_split_rate'],
                $data['referral_fee'] ?? 0
            );
        }

        return view('calculators.commission', compact('result'));
    }
}
