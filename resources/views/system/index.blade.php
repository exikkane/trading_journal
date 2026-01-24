@extends('layouts.app')

@section('content')
@php
    $lines = function ($text) {
        $items = preg_split('/\r\n|\r|\n/', (string) $text);
        $items = array_map('trim', $items);
        return array_values(array_filter($items, fn ($line) => $line !== ''));
    };
    $block = function ($text) use ($lines) {
        $items = $lines($text);
        $title = $items[0] ?? '';
        $list = array_slice($items, 1);
        return [$title, $list];
    };
@endphp

<div class="card system-page">
    <div class="row">
        <div>
            <div class="system-title-main">{{ $system->title ?? 'Trading System' }}</div>
            <div class="system-muted">Editable system guide</div>
        </div>
        <div class="spacer"></div>
        <a class="btn light" href="{{ route('system.edit') }}">Edit</a>
    </div>

    <div class="system-grid-two">
        <div style="display: grid; gap: 16px;">
            <div class="system-section">
                <h3>📈 Торгуемые пары</h3>
                <ul class="system-list">
                    @forelse ($lines($system->traded_pairs) as $item)
                        <li>{{ $item }}</li>
                    @empty
                        <li class="system-muted">No pairs added.</li>
                    @endforelse
                </ul>
            </div>

            <div class="system-section">
                <h3>📋 Алгоритм анализа графика и открытие позиций</h3>
                <ol class="system-list">
                    @forelse ($lines($system->analysis_algorithm) as $item)
                        <li>{{ $item }}</li>
                    @empty
                        <li class="system-muted">No steps added.</li>
                    @endforelse
                </ol>
            </div>
        </div>

        <div class="system-section">
            <h3>🛠 Инструменты анализа</h3>
            <ul class="system-list">
                @forelse ($lines($system->analysis_tools) as $item)
                    <li>{{ $item }}</li>
                @empty
                    <li class="system-muted">No tools added.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="system-divider"></div>

    <div class="system-section">
        <h3>⚖️ Система Риска</h3>
        <div class="system-grid-three">
            <div>
                <div class="system-paragraph" style="margin-bottom: 12px;">{{ $system->risk_intro }}</div>
                @php [$liveTitle, $liveItems] = $block($system->risk_live); @endphp
                <div style="margin-bottom: 12px;">
                    <strong>{{ $liveTitle }}</strong>
                    <ul class="system-list">
                        @foreach ($liveItems as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
                @php [$personalTitle, $personalItems] = $block($system->risk_personal); @endphp
                <div style="margin-bottom: 12px;">
                    <strong>{{ $personalTitle }}</strong>
                    <ul class="system-list">
                        @foreach ($personalItems as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
                @php [$challengeTitle, $challengeItems] = $block($system->risk_challenge); @endphp
                <div>
                    <strong>{{ $challengeTitle }}</strong>
                    <ul class="system-list">
                        @foreach ($challengeItems as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div>
                <strong>Сокращение убытков</strong>
                <div class="system-paragraph" style="margin-top: 8px;">{{ $system->risk_loss_reduction }}</div>
            </div>

            <div>
                @php [$noteTitle, $noteItems] = $block($system->risk_note); @endphp
                <strong>{{ $noteTitle }}</strong>
                <ul class="system-list" style="margin-top: 8px;">
                    @foreach ($noteItems as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>

                <div class="system-divider"></div>

                <strong>Параметры риска</strong>
                <div class="system-paragraph" style="margin-top: 6px;">{{ $system->risk_params }}</div>

                <div class="system-divider"></div>

                <strong>Лимиты риска</strong>
                <ul class="system-list" style="margin-top: 8px;">
                    @foreach ($lines($system->risk_limits) as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>

                <div class="system-divider"></div>
                <div class="system-paragraph"><strong>Важно:</strong> {{ $system->risk_footer }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
