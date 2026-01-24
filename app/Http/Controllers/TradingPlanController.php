<?php

namespace App\Http\Controllers;

use App\Models\TradingPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TradingPlanController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'month');
        if (! in_array($filter, ['month', 'quarter', 'all'], true)) {
            $filter = 'month';
        }

        $query = TradingPlan::query()->orderByDesc('plan_date');

        if (in_array($filter, ['month', 'quarter'], true)) {
            $now = Carbon::now();
            $start = match ($filter) {
                'month' => $now->copy()->startOfMonth(),
                'quarter' => $now->copy()->startOfQuarter(),
            };
            $end = match ($filter) {
                'month' => $now->copy()->endOfMonth(),
                'quarter' => $now->copy()->endOfQuarter(),
            };

            $query->whereBetween('plan_date', [$start->toDateString(), $end->toDateString()]);
        }

        return view('plans.index', [
            'plans' => $query->get(),
            'filter' => $filter,
        ]);
    }

    public function create()
    {
        return view('plans.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatePlan($request);
        $data = $this->handleUploads($request, $data);

        TradingPlan::create($data);

        return redirect()->route('plans.edit');
    }

    public function edit(TradingPlan $plan)
    {
        return view('plans.edit', [
            'plan' => $plan,
        ]);
    }

    public function update(Request $request, TradingPlan $plan)
    {
        $data = $this->validatePlan($request);
        $data = $this->handleUploads($request, $data, $plan);

        $plan->update($data);

        return redirect()->route('plans.index', [
            'filter' => $request->query('filter', 'month'),
        ]);
    }

    private function validatePlan(Request $request): array
    {
        return $request->validate([
            'plan_date' => ['required', 'date'],
            'pair' => ['required', 'string', 'max:255'],
            'narrative' => ['required', 'in:bullish,bearish,neutral'],
            'weekly_chart_screenshot' => ['nullable', 'image', 'max:5120'],
            'weekly_chart_notes' => ['nullable', 'string'],
            'daily_chart_screenshot' => ['nullable', 'image', 'max:5120'],
            'daily_chart_notes' => ['nullable', 'string'],
            'plan_a_screenshot' => ['nullable', 'image', 'max:5120'],
            'plan_a_notes' => ['nullable', 'string'],
            'plan_b_screenshot' => ['nullable', 'image', 'max:5120'],
            'plan_b_notes' => ['nullable', 'string'],
            'cancel_condition' => ['nullable', 'string', 'max:255'],
            'notes_review' => ['nullable', 'string'],
            'weekly_review_q1' => ['nullable', 'string'],
            'weekly_review_q2' => ['nullable', 'string'],
            'weekly_review_q3' => ['nullable', 'string'],
            'weekly_review_q4' => ['nullable', 'string'],
            'weekly_review_q5' => ['nullable', 'string'],
        ]);
    }

    private function handleUploads(Request $request, array $data, ?TradingPlan $plan = null): array
    {
        $map = [
            'weekly_chart_screenshot' => 'weekly_chart_screenshot_path',
            'daily_chart_screenshot' => 'daily_chart_screenshot_path',
            'plan_a_screenshot' => 'plan_a_screenshot_path',
            'plan_b_screenshot' => 'plan_b_screenshot_path',
        ];

        foreach ($map as $input => $column) {
            if ($request->hasFile($input)) {
                if ($plan && $plan->{$column}) {
                    Storage::disk('public')->delete($plan->{$column});
                }
                $data[$column] = $request->file($input)->store('trading-plans', 'public');
            }
            unset($data[$input]);
        }

        return $data;
    }
}
