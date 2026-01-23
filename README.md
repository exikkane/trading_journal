# Trading Journal

A personal trading journal built with Laravel. It separates trade **ideas** from per‑account executions, so you can record one trade setup and track results across multiple accounts.

## Purpose
- Capture trade ideas with notes and screenshots.
- Record executions per account (risk/reward, risk %).
- See performance metrics (wins, losses, drawdown, equity curve).

## Core Concepts
- **Trade (Parent)**: the trade idea/setup (dates, direction, pair, result, notes, screenshots).
- **Account Trade**: the execution of that trade on a specific account (RR, risk %).

## Features
### Trades
- List parent trades only (no account executions shown in the list).
- Create a trade idea without account details.
- Edit the trade’s core info (dates, direction, pair, result, notes).

### Accounts (per trade)
- On the trade detail page, add **Account Trades** for each account.
- Each account trade has:
  - Account
  - Risk Reward (RR)
  - Risk %
- Profit for a trade = sum of profits from all account trades.

### Accounts (global)
- Create accounts with initial/current balance.
- Account stats: wins/losses/BE/in‑progress, win ratio, profit %, profit $, max drawdown.

### Dashboard
- Aggregated stats for parent trades + account executions.
- Net Profit and Equity Curve are calculated from **account trades**.
- Filters: current month / current quarter / all.

### Stats Page
- Same metrics as dashboard with filters:
  - Time range
  - Account

## Profit Rules
For each **account trade**:
- **Win**: `RR × Risk %`
- **Loss**: `-Risk %`
- **BE**: `RR × Risk %` (can be positive/negative/0)
- **In progress**: `0`

## Screenshots
Each trade can store screenshots:
- **IDEA** (setup when opened)
- **EXIT MOMENT** (after close)
- **CONCLUSIONS** (post‑trade review)

Run once to enable image access:
```
php artisan storage:link
```

## Local Setup
1) Install PHP 8.1+, MySQL, Composer.
2) Configure DB in `.env`.
3) Run migrations:
```
php artisan migrate
```
4) Start the app:
```
php artisan serve
```
Open: `http://127.0.0.1:8000`

## Importing Notion CSV
There’s a command to import a Notion CSV export:
```
php artisan journal:import Journal.csv
```
It creates trades + account trades and auto‑creates accounts by name.

## Routes
- `/` or `/trades` — trade list
- `/trades/{id}` — trade detail + account trades
- `/accounts` — account list + stats
- `/dashboard` — overview metrics
- `/stats` — metrics filtered by time and account

## Notes
- Parent trade IDs and account trade IDs are separate and can be reset if needed.
- Account trades are unique per trade+account.
