<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trading Journal</title>
    <style>
        :root {
            color-scheme: dark;
            --bg: #0b0f1a;
            --bg-2: #0f172a;
            --card: #111827;
            --card-2: #0f172a;
            --text: #e2e8f0;
            --muted: #94a3b8;
            --accent: #8b5cf6;
            --accent-2: #22c55e;
            --danger: #ef4444;
            --border: #1f2937;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Manrope", "DM Sans", "Segoe UI", sans-serif;
            background: var(--bg);
            color: var(--text);
        }
        header {
            padding: 20px 500px;
            background: rgba(17, 24, 39, 0.9);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(10px);
        }
        header h1 {
            margin: 0;
            font-size: 20px;
        }
        header nav {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        main { padding: 24px 500px 48px; }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.25);
        }
        .row {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }
        .spacer { flex: 1; }
        .btn {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 6px;
            background: var(--accent);
            color: white;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }
        .btn.secondary {
            background: #1f2937;
        }
        .btn.light {
            background: #111827;
            color: #e2e8f0;
            border: 1px solid #2b3648;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            text-align: left;
            padding: 10px 12px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }
        th {
            color: var(--muted);
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        table input, table select {
            width: 100%;
            min-width: 120px;
        }
        .date-input {
            width: 140px;
            min-width: 140px;
        }
        .actions {
            min-width: 180px;
        }

        .trades-toolbar {
            gap: 16px;
        }
        .trades-toolbar .btn {
            padding: 8px 16px;
            border-radius: 10px;
        }
        .trades-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 13px;
            background: #0f1627;
            border: 1px solid #1f2a3d;
            border-radius: 14px;
        }
        .trades-table thead th {
            background: #101827;
            border-bottom: 1px solid #1f2a3d;
            color: #a8b3c7;
            font-size: 11px;
            letter-spacing: 0.08em;
        }
        .trades-table thead th:first-child {
            border-top-left-radius: 10px;
        }
        .trades-table thead th:last-child {
            border-top-right-radius: 10px;
        }
        .trades-table tbody tr {
            background: #0f1627;
        }
        .trades-table tbody tr:nth-child(even) {
            background: #101a2d;
        }
        .trades-table tbody tr:hover {
            background: #15213a;
        }
        .trades-table td, .trades-table th {
            border-bottom: 1px solid #1f2a3d;
        }
        .trades-table td {
            color: #e2e8f0;
        }
        .trade-pair {
            font-weight: 700;
        }
        .trades-table input, .trades-table select {
            background: #0b1220;
            border: 1px solid #22304a;
            color: #e2e8f0;
            border-radius: 8px;
            padding: 6px 8px;
        }
        .trades-table .row {
            gap: 8px;
        }
        .trades-table .btn {
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 12px;
        }
        .trades-actions .btn {
            min-width: 76px;
            text-align: center;
        }
        .chip-select {
            appearance: none;
            border-radius: 999px;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 6px 12px;
            min-width: 92px;
            text-align: center;
            border: 1px solid transparent;
            background-image: none;
        }
        .chip-direction-long {
            color: #86efac;
            background: rgba(34, 197, 94, 0.16) !important;
            border-color: rgba(34, 197, 94, 0.3);
        }
        .chip-direction-short {
            color: #fca5a5;
            background: rgba(239, 68, 68, 0.16) !important;
            border-color: rgba(239, 68, 68, 0.3);
        }
        .chip-result-win {
            color: #86efac;
            background: rgba(1, 225, 82, 0.47) !important;
            border-color: rgba(34, 197, 94, 0.3);
        }
        .chip-result-loss {
            color: #fca5a5;
            background: rgba(239, 68, 68, 0.55) !important;
            border-color: rgba(239, 68, 68, 0.3);
        }
        .chip-result-be {
            color: #cbd5f5;
            background: rgba(148, 163, 184, 0.18) !important;
            border-color: rgba(148, 163, 184, 0.35);
        }
        .chip-result-in_progress {
            color: #fbbf24;
            background: rgba(15, 95, 186, 0.66) !important;
            border-color: rgba(245, 158, 11, 0.35);
        }
        .muted { color: var(--muted); }
        .badge {
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .badge.in_progress { background: rgba(245, 158, 11, 0.2); color: #fbbf24; }
        .badge.win { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
        .badge.loss { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .badge.be { background: rgba(148, 163, 184, 0.2); color: #94a3b8; }
        .badge.bullish { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
        .badge.bearish { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .badge.neutral { background: rgba(148, 163, 184, 0.2); color: #94a3b8; }
        form .field {
            display: grid;
            gap: 6px;
        }
        label { font-weight: 600; font-size: 14px; }
        input, select, textarea {
            padding: 8px 10px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            background: #0b1220;
            color: var(--text);
        }
        input::placeholder, textarea::placeholder { color: #64748b; }
        textarea {
            min-height: 120px;
            resize: vertical;
        }
        .grid {
            display: grid;
            gap: 16px;
        }
        .grid.two { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid.three { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid.four { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .grid.split { grid-template-columns: minmax(0, 7fr) minmax(0, 3fr); }
        .grid.plan-split { grid-template-columns: minmax(0, 3fr) minmax(0, 4fr) minmax(0, 3fr); }
        .plan-section {
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 16px;
            background: #0f1627;
        }
        .plan-section h3 { margin: 0 0 12px 0; }
        .image-stack {
            display: grid;
            gap: 16px;
        }
        .perf-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(3, 300px);
            align-items: start;
        }
        .perf-card {
            width: 300px;
            height: 150px;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px;
            background: #0f1627;
            display: grid;
            align-content: start;
            gap: 6px;
            font-size: 12px;
        }
        .perf-card-link { text-decoration: none; color: inherit; }
        .perf-card-link:hover { border-color: var(--accent); box-shadow: 0 4px 12px rgba(15, 118, 110, 0.12); }
        .perf-title {
            font-weight: 700;
            font-size: 13px;
        }
        .perf-row { color: var(--muted); }
        .stat-title {
            color: var(--muted);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-weight: 600;
        }
        .stat-value {
            font-size: 28px;
            font-weight: 700;
            margin-top: 6px;
        }
        .stat-sub {
            font-size: 13px;
            color: var(--muted);
            margin-top: 8px;
            line-height: 1.6;
        }
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }
        .stat-card {
            background: linear-gradient(160deg, rgba(139, 92, 246, 0.12), rgba(17, 24, 39, 0.8));
            border: 1px solid #222b3d;
            border-radius: 12px;
            padding: 16px;
            display: grid;
            gap: 8px;
            min-height: 120px;
        }
        .stat-card .stat-value { font-size: 26px; }
        .stat-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            color: var(--accent-2);
            background: rgba(34, 197, 94, 0.15);
        }
        .stat-chip.negative {
            color: var(--danger);
            background: rgba(239, 68, 68, 0.15);
        }
        .vs-card {
            background: #0f1627;
            border: 1px solid #1f2a3d;
        }
        .vs-metric {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--muted);
            font-size: 12px;
        }
        .vs-metric strong { color: var(--text); font-weight: 600; }
        .system-hero {
            position: relative;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #1f2a3d;
            background-size: cover;
            background-position: center;
            min-height: 360px;
        }
        .system-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(100deg, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, 0.75) 50%, rgba(15, 23, 42, 0.3) 100%);
        }
        .system-content {
            position: relative;
            z-index: 1;
            padding: 32px 36px;
            max-width: 620px;
        }
        .system-brand {
            font-weight: 700;
            color: #fbbf24;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .system-title {
            font-size: 56px;
            font-weight: 800;
            letter-spacing: 0.02em;
            margin: 10px 0 16px;
            line-height: 1;
            text-transform: uppercase;
            text-shadow: 0 6px 18px rgba(0, 0, 0, 0.4);
        }
        .system-title span {
            display: inline-block;
            margin-right: 10px;
        }
        .system-title .secondary {
            color: #fbbf24;
        }
        .system-body {
            font-size: 16px;
            line-height: 1.7;
            color: #e2e8f0;
        }
        .system-body p { margin: 0 0 8px; }
        .system-cta {
            display: inline-block;
            margin-top: 18px;
            padding: 12px 24px;
            border-radius: 10px;
            border: 2px solid rgba(139, 92, 246, 0.8);
            color: #e2e8f0;
            text-decoration: none;
            font-weight: 700;
            letter-spacing: 0.02em;
            background: rgba(15, 23, 42, 0.6);
            box-shadow: inset 0 0 0 1px rgba(139, 92, 246, 0.4);
        }
        .system-footer {
            margin-top: 20px;
            font-size: 14px;
            color: var(--muted);
        }
        .system-footer a { color: #fbbf24; text-decoration: underline; }
        .system-page {
            display: grid;
            gap: 24px;
        }
        .system-title-main {
            font-size: 22px;
            font-weight: 700;
        }
        .system-section {
            background: #0f1627;
            border: 1px solid #1f2a3d;
            border-radius: 12px;
            padding: 18px 20px;
        }
        .system-section h3 {
            margin: 0 0 12px 0;
            font-size: 14px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #e2e8f0;
        }
        .system-grid-two {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }
        .system-grid-three {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 24px;
        }
        .system-list {
            margin: 0;
            padding-left: 18px;
            line-height: 1.7;
            color: #cbd5f5;
        }
        .system-list li { margin-bottom: 6px; }
        .system-paragraph {
            color: #cbd5f5;
            line-height: 1.7;
        }
        .system-divider {
            height: 1px;
            background: #1f2a3d;
            margin: 16px 0;
        }
        .system-muted {
            color: var(--muted);
            font-size: 12px;
        }
        .error {
            color: #b91c1c;
            font-size: 13px;
        }
        @media (max-width: 900px) {
            .grid.two, .grid.three, .grid.four, .grid.split, .grid.plan-split { grid-template-columns: 1fr; }
            .perf-grid { grid-template-columns: 1fr; }
            .perf-card { width: 100%; height: auto; }
            .stat-grid { grid-template-columns: 1fr; }
            .system-grid-two, .system-grid-three { grid-template-columns: 1fr; }
            header { padding: 16px 20px; }
            main { padding: 20px; }
        }
        @media (max-width: 1400px) {
            header { padding: 20px 120px; }
            main { padding: 24px 120px 48px; }
        }
    </style>
</head>
<body>
    <header>
        <div class="row">
            <h1>Trading Journal</h1>
            <div class="spacer"></div>
            <nav>
                <a class="btn light" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="btn light" href="{{ route('trades.index') }}">Trades</a>
                <a class="btn light" href="{{ route('plans.index') }}">Plans</a>
                <a class="btn light" href="{{ route('performance.index') }}">Performance</a>
                <a class="btn light" href="{{ route('accounts.index') }}">Accounts</a>
                <a class="btn light" href="{{ route('system.index') }}">System</a>
            </nav>
        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>
</html>
