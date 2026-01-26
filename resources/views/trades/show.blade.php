@extends('layouts.app')

@section('content')
<div class="card">
    <div class="row" style="margin-bottom: 16px;">
        <div>
            <h2 style="margin: 0 0 4px 0;">Trade #{{ $trade->id }} Details</h2>
            <div class="muted" style="font-size: 13px;">Profit is calculated from the accounts listed below.</div>
        </div>
        <div class="spacer"></div>
        @php $profitValue = (float) $accountsProfit; @endphp
        <div class="profit-badge {{ $profitValue >= 0 ? 'positive' : 'negative' }}">
            {{ $profitValue >= 0 ? 'Profit' : 'Loss' }} {{ number_format(abs($profitValue), 2, '.', '') }}%
        </div>
        <a class="btn light" href="{{ route('trades.index') }}">Back to List</a>
    </div>

    <form method="POST" action="{{ route('trades.update', $trade) }}" class="grid" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="redirect_to" value="{{ route('trades.show', $trade) }}">

        <div class="grid two">
            <div class="field">
                <label for="start_date">Start Date</label>
                <input id="start_date" type="date" name="start_date" value="{{ old('start_date', $trade->start_date->format('Y-m-d')) }}" required>
                @error('start_date')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="end_date">End Date</label>
                <input id="end_date" type="date" name="end_date" value="{{ old('end_date', $trade->end_date ? $trade->end_date->format('Y-m-d') : '') }}">
                @error('end_date')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="grid three">
            <div class="field">
                <label for="pair">Pair</label>
                <select id="pair" name="pair" required>
                    @foreach ($pairCategories as $key => $label)
                        @php $pairs = $pairsByCategory->get($key, collect()); @endphp
                        @if ($pairs->isNotEmpty())
                            <optgroup label="{{ $label }}">
                                @foreach ($pairs as $pair)
                                    <option value="{{ $pair->name }}" {{ old('pair', $trade->pair) === $pair->name ? 'selected' : '' }}>{{ $pair->name }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                    @endforeach
                </select>
                @error('pair')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="direction">Direction</label>
                <select id="direction" name="direction" required>
                    <option value="long" {{ old('direction', $trade->direction) === 'long' ? 'selected' : '' }}>Long</option>
                    <option value="short" {{ old('direction', $trade->direction) === 'short' ? 'selected' : '' }}>Short</option>
                </select>
                @error('direction')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="result">Result</label>
                <select id="result" name="result" required>
                    <option value="in_progress" {{ old('result', $trade->result) === 'in_progress' ? 'selected' : '' }}>In progress</option>
                    <option value="win" {{ old('result', $trade->result) === 'win' ? 'selected' : '' }}>Win</option>
                    <option value="loss" {{ old('result', $trade->result) === 'loss' ? 'selected' : '' }}>Loss</option>
                    <option value="be" {{ old('result', $trade->result) === 'be' ? 'selected' : '' }}>BE</option>
                </select>
                @error('result')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="grid two">
            <div class="field">
                <label for="execution">Execution</label>
                <select id="execution" name="execution">
                    @foreach (['IDM', 'FVG', 'SNR', 'Market'] as $option)
                        <option value="{{ $option }}" {{ old('execution', $trade->execution) === $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
                @error('execution')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="entry_tf">Entry TF</label>
                <select id="entry_tf" name="entry_tf">
                    @foreach (['4H', 'Daily'] as $option)
                        <option value="{{ $option }}" {{ old('entry_tf', $trade->entry_tf) === $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
                @error('entry_tf')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="grid two">
            <div class="field">
                <label for="idea_screenshot">IDEA Screenshot</label>
                <input id="idea_screenshot" type="file" name="idea_screenshot" accept="image/*">
                @if ($trade->idea_screenshot_path)
                    <div style="margin-top: 8px;">
                        <img src="{{ Storage::url($trade->idea_screenshot_path) }}" alt="Idea screenshot" style="max-width: 100%; border: 1px solid var(--border); border-radius: 8px;">
                    </div>
                @endif
                @error('idea_screenshot')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="exit_screenshot">EXIT MOMENT Screenshot</label>
                <input id="exit_screenshot" type="file" name="exit_screenshot" accept="image/*">
                @if ($trade->exit_screenshot_path)
                    <div style="margin-top: 8px;">
                        <img src="{{ Storage::url($trade->exit_screenshot_path) }}" alt="Exit screenshot" style="max-width: 100%; border: 1px solid var(--border); border-radius: 8px;">
                    </div>
                @endif
                @error('exit_screenshot')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="grid two">
            <div class="field">
                <label for="idea_notes">IDEA Notes</label>
                <textarea id="idea_notes" name="idea_notes" class="tall-textarea" placeholder="Why you took the trade...">{{ old('idea_notes', $trade->idea_notes) }}</textarea>
                @error('idea_notes')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="conclusion_screenshot">CONCLUSIONS Screenshot</label>
                <input id="conclusion_screenshot" type="file" name="conclusion_screenshot" accept="image/*">
                @if ($trade->conclusion_screenshot_path)
                    <div style="margin-top: 8px;">
                        <img src="{{ Storage::url($trade->conclusion_screenshot_path) }}" alt="Conclusion screenshot" style="max-width: 100%; border: 1px solid var(--border); border-radius: 8px;">
                    </div>
                @endif
                @error('conclusion_screenshot')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="field">
            <label for="conclusions_notes">CONCLUSIONS Notes</label>
            <textarea id="conclusions_notes" name="conclusions_notes" class="tall-textarea" placeholder="Thoughts after the trade...">{{ old('conclusions_notes', $trade->conclusions_notes) }}</textarea>
            @error('conclusions_notes')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="row" style="margin-top: 8px;">
            <button class="btn" type="submit">Save Changes</button>
        </div>
    </form>

    <div class="card" style="margin-top: 24px;">
        <h3 style="margin: 0 0 12px 0;">Accounts</h3>
        <form method="POST" action="{{ route('trades.subtrades.store', $trade) }}" class="grid">
            @csrf
            <div class="grid three">
                <div class="field">
                    <label for="sub_account_id">Account</label>
                    <select id="sub_account_id" name="account_id" required>
                        @if ($accounts->isEmpty())
                            <option value="" disabled selected>No accounts</option>
                        @else
                            <option value="" disabled selected>Select account</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="field">
                    <label for="sub_risk_reward">Risk Reward</label>
                    <input id="sub_risk_reward" type="number" step="0.01" min="0" name="risk_reward" required>
                </div>
                <div class="field">
                    <label for="sub_risk_pct">Risk %</label>
                    <input id="sub_risk_pct" type="number" step="0.01" min="0" max="100" name="risk_pct" required>
                </div>
            </div>
            <div class="row">
                <button class="btn" type="submit">Add Account Trade</button>
            </div>
        </form>

        <div style="margin-top: 16px; overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>RR</th>
                        <th>Risk</th>
                        <th>Profit</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($accountTrades as $subtrade)
                        @php
                            $updateId = 'subtrade-update-' . $subtrade->id;
                            $deleteId = 'subtrade-delete-' . $subtrade->id;
                        @endphp
                        <tr>
                            <td>
                                <select name="account_id" form="{{ $updateId }}" required>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}" {{ $subtrade->account_id === $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" name="risk_reward" value="{{ number_format((float) $subtrade->risk_reward, 2, '.', '') }}" form="{{ $updateId }}" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" max="100" name="risk_pct" value="{{ number_format((float) $subtrade->risk_pct, 2, '.', '') }}" form="{{ $updateId }}" required>
                            </td>
                            <td>{{ $subtrade->profit_pct }}%</td>
                            <td>
                                <form id="{{ $updateId }}" method="POST" action="{{ route('trades.subtrades.update', [$trade, $subtrade]) }}">
                                    @csrf
                                    @method('PUT')
                                </form>
                                <form id="{{ $deleteId }}" method="POST" action="{{ route('trades.subtrades.destroy', [$trade, $subtrade]) }}" onsubmit="return confirm('Delete this account trade?')">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <div class="row">
                                    <button class="btn secondary" type="submit" form="{{ $updateId }}">Save</button>
                                    <button class="btn light" type="submit" form="{{ $deleteId }}">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="muted">No accounts yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <form method="POST" action="{{ route('trades.destroy', $trade) }}" onsubmit="return confirm('Delete this trade?')" style="margin-top: 16px;">
        @csrf
        @method('DELETE')
        <button class="btn light" type="submit">Delete Trade</button>
    </form>
</div>
@endsection
