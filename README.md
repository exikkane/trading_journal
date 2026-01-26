# Trading Journal

<img width="2013" height="962" alt="image" src="https://github.com/user-attachments/assets/a312ba03-8937-442d-88a6-011642528eec" />


A Laravel 10 trading journal that separates **trade ideas** from **per‑account executions**, with performance analytics, planning, and reviews.

## Core Concepts
- **Trade**: the idea/setup (dates, direction, pair, result, execution details, notes, screenshots).
- **Account Trade**: the execution of a trade on a specific account (RR, risk %).
- **Account**: trading account with balance, status, payouts, and derived stats.

## Features
### Trades
- List trades with filters (week / month / quarter / all) and pair filter.
- Create/edit trade ideas with execution info, notes, and screenshots.
- View a trade and manage per‑account executions (RR, risk %).

### Accounts + Payouts
- Create accounts with initial/current balance and status.
- Statuses: Eval Stage 1, Eval Stage 2, Funded, Live, Passed, Failed (archived).
- Track payouts by date and amount.
- Account stats: win/loss/BE/in‑progress counts, win ratio, net profit %, profit $, max drawdown.

### Pairs
- Maintain a list of tradeable pairs (Forex / Indices categories).
- Use pairs to filter trades and plans.

### Dashboard & Stats
- Aggregated stats across trades or per account.
- Equity curve and net profit from account trades.
- Filters: time range, account, pair.
- Monthly analytics chart with selectable year range.

### Performance Analysis
- MPA/QPA views with quarter and month cards.
- Period drill‑downs by month/quarter with result filters.
- Performance reviews with notes and screenshots.

### Trading Plans
- Create plans with narrative (bullish/bearish/neutral).
- Attach weekly/daily/DXY charts, Plan A/Plan B, cancel condition, and review questions.
- Store periodic updates with screenshots.

### Trading System
- Maintain a long‑form system/strategy page with tools, rules, and risk sections.

## Profit Rules
For each **account trade**:
- **Win**: `RR × Risk %`
- **Loss**: `-Risk %`
- **BE**: `RR × Risk %` (can be positive/negative/0)
- **In progress**: `0`

## Screenshots & File Storage
Trade, plan, and review screenshots are stored in `storage/app/public`.
Enable public access once:
```
php artisan storage:link
```

## Local Setup
1) Install PHP 8.1+, MySQL, Composer.
2) Configure database settings in `.env`.
3) Install dependencies:
```
composer install
```
4) Run migrations:
```
php artisan migrate
```
5) Generate app key if needed:
```
php artisan key:generate
```
6) Serve the app:
```
php artisan serve
```
Open: `http://127.0.0.1:8000`

### Optional: Apache + PHP‑FPM (no `artisan serve`)
Point Apache’s document root to `public/` and use your PHP‑FPM socket.
Example vhost name can be mapped via `/etc/hosts`.

## Importing Notion CSV
Import a Notion CSV export into trades + account trades:
```
php artisan journal:import Journal.csv
```
Use a dry run:
```
php artisan journal:import Journal.csv --dry-run
```

## Routes
- `/` or `/trades` — trade list
- `/trades/{trade}` — trade detail + account trades
- `/dashboard` — overview metrics
- `/stats` — metrics with time/account filters
- `/data` — accounts, payouts, pairs
- `/plans` — trading plans
- `/performance` — performance analysis
- `/system` — trading system

## Notes
- Account trades are unique per trade + account.
