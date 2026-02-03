<?php

namespace App\Http\Controllers;

use App\Models\TradingPlan;
use App\Models\Pair;
use App\Models\TradingPlanUpdate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TradingPlanController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all');
        if (! in_array($filter, ['month', 'quarter', 'all'], true)) {
            $filter = 'all';
        }

        $pairs = Pair::query()->orderBy('name')->get();
        $pairName = $request->query('pair', 'all');
        if ($pairName === 'all') {
            $pairName = null;
        } elseif (! $pairs->pluck('name')->contains($pairName)) {
            $pairName = null;
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
        if ($pairName) {
            $query->where('pair', $pairName);
        }

        return view('plans.index', [
            'plans' => $query->get(),
            'filter' => $filter,
            'pairName' => $pairName,
            'pairs' => $pairs,
        ]);
    }

    public function create()
    {
        $pairsByCategory = Pair::query()->orderBy('name')->get()->groupBy('category');

        return view('plans.create', [
            'pairsByCategory' => $pairsByCategory,
            'pairCategories' => Pair::categories(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePlan($request);
        $data = $this->handleUploads($request, $data);

        $plan = TradingPlan::create($data);

        return redirect()->route('plans.edit', $plan);
    }

    public function edit(TradingPlan $plan)
    {
        $pairsByCategory = Pair::query()->orderBy('name')->get()->groupBy('category');
        $updates = $plan->updates()->orderByDesc('update_date')->orderByDesc('id')->get();

        return view('plans.edit', [
            'plan' => $plan,
            'pairsByCategory' => $pairsByCategory,
            'pairCategories' => Pair::categories(),
            'updates' => $updates,
        ]);
    }

    public function show(TradingPlan $plan)
    {
        $updates = $plan->updates()->orderByDesc('update_date')->orderByDesc('id')->get();

        return view('plans.show', [
            'plan' => $plan,
            'updates' => $updates,
        ]);
    }

    public function update(Request $request, TradingPlan $plan)
    {
        $data = $this->validatePlan($request);
        $data = $this->handleUploads($request, $data, $plan);

        $plan->update($data);

        return redirect()->route('plans.show', $plan);
    }

    public function destroy(Request $request, TradingPlan $plan)
    {
        $this->deleteUploads($plan);
        $plan->delete();

        return redirect()->route('plans.index', [
            'filter' => $request->query('filter', 'all'),
            'pair' => $request->query('pair', 'all'),
        ]);
    }

    private function validatePlan(Request $request): array
    {
        return $request->validate([
            'plan_date' => ['required', 'date'],
            'pair' => ['required', Rule::exists('pairs', 'name')],
            'narrative' => ['required', 'in:bullish,bearish,neutral'],
            'weekly_chart_screenshot' => ['nullable', 'image', 'max:5120'],
            'weekly_chart_notes' => ['nullable', 'string'],
            'daily_chart_screenshot' => ['nullable', 'image', 'max:5120'],
            'daily_chart_notes' => ['nullable', 'string'],
            'dxy_chart_screenshot' => ['nullable', 'image', 'max:5120'],
            'dxy_chart_notes' => ['nullable', 'string'],
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

    public function storeUpdate(Request $request, TradingPlan $plan)
    {
        $data = $request->validate([
            'update_notes' => ['nullable', 'string'],
            'update_screenshots' => ['nullable', 'array'],
            'update_screenshots.*' => ['image', 'max:5120'],
        ]);

        $paths = [];
        if ($request->hasFile('update_screenshots')) {
            foreach ($request->file('update_screenshots') as $file) {
                $paths[] = $file->store('trading-plans/updates', 'public');
            }
        }

        TradingPlanUpdate::create([
            'trading_plan_id' => $plan->id,
            'update_date' => Carbon::now()->toDateString(),
            'update_notes' => $data['update_notes'] ?? null,
            'update_screenshots' => $paths ?: null,
        ]);

        return redirect()->route('plans.show', $plan);
    }

    private function handleUploads(Request $request, array $data, ?TradingPlan $plan = null): array
    {
        $map = [
            'weekly_chart_screenshot' => 'weekly_chart_screenshot_path',
            'daily_chart_screenshot' => 'daily_chart_screenshot_path',
            'dxy_chart_screenshot' => 'dxy_chart_screenshot_path',
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

    private function deleteUploads(TradingPlan $plan): void
    {
        foreach (['weekly_chart_screenshot_path', 'daily_chart_screenshot_path', 'dxy_chart_screenshot_path', 'plan_a_screenshot_path', 'plan_b_screenshot_path'] as $column) {
            if ($plan->{$column}) {
                Storage::disk('public')->delete($plan->{$column});
            }
        }
    }
}
