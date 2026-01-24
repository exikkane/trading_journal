<?php

namespace App\Http\Controllers;

use App\Models\TradingSystem;
use Illuminate\Http\Request;

class TradingSystemController extends Controller
{
    public function index()
    {
        $system = TradingSystem::query()->first();
        if (! $system) {
            $system = TradingSystem::create([
                'title' => 'Trading System',
                'traded_pairs' => "EURUSD\nGER40",
                'analysis_tools' => "Dealing Range\nSNH (Support & Resistance)\nFVG (Fair Value Gap)\nRejection Block\nFractal High / Low (IDM)",
                'analysis_algorithm' => "Определение точки A: Где цена сейчас? На что цена реагирует? На что она может реагировать?\nОпределение точки B: Какой точки цена может достичь с высокой вероятностью?\nОпределение положение Stop Loss: Точка отмены идеи.\nКакие дополнительные переменные присутствуют? LRL, VC, MQ, Rejection",
                'risk_intro' => 'Разделение системы риска на три направления:',
                'risk_live' => "Live account:\n1% risk per trade\n2% risk per trade idea",
                'risk_personal' => "Personal Deposit:\n5% risk per trade\n10% risk per trade idea",
                'risk_challenge' => "Challenge:\n2% risk per trade\n4% risk per trade idea",
                'risk_loss_reduction' => 'Сокращение убытков: Допускается закрытие позиции в минус досрочно, в случае, если направление рынка выбрано неверно. Правила и условия досрочного выхода прописываются в плане.',
                'risk_note' => "Примечание:\nДопускается реализация одной идеи в несколько заходов:\nНапример, сначала пробуем агрессивный вход (1%), получаем стоп.\nЗатем — повторный вход с другой точки, все ещё в рамках той же идеи.\nТакже допускается добавление к позиции, если движение подтвердилось.\nДо 3-х сделок, при этом первая переводится в Б/У.",
                'risk_params' => 'Параметры риска: 1.5 RR — Минимальный RR для открытия позиции.',
                'risk_limits' => "Максимально допустимая просадка 2% на неделю.\nПри потере 2% в течение недели — торговля прекращается до начала следующей недели.\nМаксимально допустимая просадка 5% на месяц.\nПри потере 5% в течение месяца — торговля прекращается на одну неделю.",
                'risk_footer' => 'Важно: риск после начала торговли сокращается до 0.5% на сделку до возврата к значениям депозита -3%.',
            ]);
        }

        return view('system.index', [
            'system' => $system,
        ]);
    }

    public function edit()
    {
        $system = TradingSystem::query()->first();
        if (! $system) {
            return redirect()->route('system.index');
        }

        return view('system.edit', [
            'system' => $system,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'traded_pairs' => ['nullable', 'string'],
            'analysis_tools' => ['nullable', 'string'],
            'analysis_algorithm' => ['nullable', 'string'],
            'risk_intro' => ['nullable', 'string'],
            'risk_live' => ['nullable', 'string'],
            'risk_personal' => ['nullable', 'string'],
            'risk_challenge' => ['nullable', 'string'],
            'risk_loss_reduction' => ['nullable', 'string'],
            'risk_note' => ['nullable', 'string'],
            'risk_params' => ['nullable', 'string'],
            'risk_limits' => ['nullable', 'string'],
            'risk_footer' => ['nullable', 'string'],
        ]);

        $system = TradingSystem::query()->first();
        if (! $system) {
            $system = TradingSystem::create($data);
        } else {
            $system->update($data);
        }

        return redirect()->route('system.index');
    }
}
