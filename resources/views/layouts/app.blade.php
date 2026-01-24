<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trading Journal</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f6f7fb;
            --card: #ffffff;
            --text: #1b1f24;
            --muted: #64748b;
            --accent: #0f766e;
            --border: #e2e8f0;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg);
            color: var(--text);
        }
        header {
            padding: 20px 32px;
            background: var(--card);
            border-bottom: 1px solid var(--border);
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
        main {
            padding: 24px 32px 48px;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 20px;
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
            background: #0f172a;
        }
        .btn.light {
            background: #e2e8f0;
            color: #0f172a;
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
        .badge.in_progress { background: #fef9c3; color: #92400e; }
        .badge.win { background: #dcfce7; color: #166534; }
        .badge.loss { background: #fee2e2; color: #991b1b; }
        .badge.be { background: #e2e8f0; color: #0f172a; }
        .badge.bullish { background: #dcfce7; color: #166534; }
        .badge.bearish { background: #fee2e2; color: #991b1b; }
        .badge.neutral { background: #e2e8f0; color: #0f172a; }
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
        }
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
            background: #f8fafc;
        }
        .plan-section h3 { margin: 0 0 12px 0; }
        .image-stack {
            display: grid;
            gap: 16px;
        }
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
        .error {
            color: #b91c1c;
            font-size: 13px;
        }
        @media (max-width: 900px) {
            .grid.two, .grid.three, .grid.four, .grid.split, .grid.plan-split { grid-template-columns: 1fr; }
            main { padding: 20px; }
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
                <a class="btn light" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="btn light" href="{{ route('accounts.index') }}">Accounts</a>
                <a class="btn light" href="{{ route('stats.index') }}">Stats</a>
            </nav>
        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>
</html>
