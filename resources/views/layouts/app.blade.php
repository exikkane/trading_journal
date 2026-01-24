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
            padding: 20px 400px;
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
        main { padding: 24px 400px 48px; }
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
        .error {
            color: #b91c1c;
            font-size: 13px;
        }
        @media (max-width: 900px) {
            .grid.two, .grid.three, .grid.four, .grid.split, .grid.plan-split { grid-template-columns: 1fr; }
            .perf-grid { grid-template-columns: 1fr; }
            .perf-card { width: 100%; height: auto; }
            .stat-grid { grid-template-columns: 1fr; }
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
                <a class="btn light" href="{{ route('trades.index') }}">Trades</a>
                <a class="btn light" href="{{ route('plans.index') }}">Plans</a>
                <a class="btn light" href="{{ route('performance.index') }}">Performance</a>
                <a class="btn light" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="btn light" href="{{ route('accounts.index') }}">Accounts</a>
            </nav>
        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>
</html>
