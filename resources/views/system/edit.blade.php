@extends('layouts.app')

@section('content')
<div class="card">
    <div class="row" style="margin-bottom: 12px;">
        <div>
            <h3 style="margin: 0;">Edit Trading System</h3>
            <div class="system-muted">One item per line for lists.</div>
        </div>
        <div class="spacer"></div>
        <a class="btn light" href="{{ route('system.index') }}">Back</a>
    </div>
    <form method="POST" action="{{ route('system.update') }}" class="grid">
        @csrf
        <div class="grid two">
            <div class="field">
                <label for="title">Title</label>
                <input id="title" type="text" name="title" value="{{ old('title', $system->title) }}">
            </div>
        </div>
        <div class="grid two">
            <div class="field">
                <label for="traded_pairs">Торгуемые пары (one per line)</label>
                <textarea id="traded_pairs" name="traded_pairs">{{ old('traded_pairs', $system->traded_pairs) }}</textarea>
            </div>
            <div class="field">
                <label for="analysis_tools">Инструменты анализа (one per line)</label>
                <textarea id="analysis_tools" name="analysis_tools">{{ old('analysis_tools', $system->analysis_tools) }}</textarea>
            </div>
        </div>
        <div class="field">
            <label for="analysis_algorithm">Алгоритм анализа (one per line)</label>
            <textarea id="analysis_algorithm" name="analysis_algorithm">{{ old('analysis_algorithm', $system->analysis_algorithm) }}</textarea>
        </div>
        <div class="field">
            <label for="risk_intro">Система риска — вводный текст</label>
            <textarea id="risk_intro" name="risk_intro">{{ old('risk_intro', $system->risk_intro) }}</textarea>
        </div>
        <div class="grid three">
            <div class="field">
                <label for="risk_live">Live account (title + lines)</label>
                <textarea id="risk_live" name="risk_live">{{ old('risk_live', $system->risk_live) }}</textarea>
            </div>
            <div class="field">
                <label for="risk_personal">Personal Deposit (title + lines)</label>
                <textarea id="risk_personal" name="risk_personal">{{ old('risk_personal', $system->risk_personal) }}</textarea>
            </div>
            <div class="field">
                <label for="risk_challenge">Challenge (title + lines)</label>
                <textarea id="risk_challenge" name="risk_challenge">{{ old('risk_challenge', $system->risk_challenge) }}</textarea>
            </div>
        </div>
        <div class="grid two">
            <div class="field">
                <label for="risk_loss_reduction">Сокращение убытков</label>
                <textarea id="risk_loss_reduction" name="risk_loss_reduction">{{ old('risk_loss_reduction', $system->risk_loss_reduction) }}</textarea>
            </div>
            <div class="field">
                <label for="risk_note">Примечание (title + lines)</label>
                <textarea id="risk_note" name="risk_note">{{ old('risk_note', $system->risk_note) }}</textarea>
            </div>
        </div>
        <div class="grid two">
            <div class="field">
                <label for="risk_params">Параметры риска</label>
                <textarea id="risk_params" name="risk_params">{{ old('risk_params', $system->risk_params) }}</textarea>
            </div>
            <div class="field">
                <label for="risk_limits">Лимиты риска (one per line)</label>
                <textarea id="risk_limits" name="risk_limits">{{ old('risk_limits', $system->risk_limits) }}</textarea>
            </div>
        </div>
        <div class="field">
            <label for="risk_footer">Важно</label>
            <textarea id="risk_footer" name="risk_footer">{{ old('risk_footer', $system->risk_footer) }}</textarea>
        </div>
        <div class="row">
            <button class="btn" type="submit">Save Changes</button>
        </div>
    </form>
</div>
@endsection
