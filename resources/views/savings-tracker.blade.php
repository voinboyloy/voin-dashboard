<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
        if (localStorage.getItem('mainSidebarCollapsed') === 'true') {
            document.documentElement.classList.add('main-sidebar-collapsed');
        }
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voin - Savings Tracker</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://api.fontshare.com">
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Modern helper styles for fine-tuning layout and colors */
        .header-title-area h1 {
            font-size: 2.25rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            color: var(--text-primary);
            line-height: 1.1;
        }
        .panel-header {
            margin-bottom: 24px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 14px;
        }
        .panel-title {
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: var(--text-primary);
        }
        .panel-subtitle {
            font-size: 0.8rem;
            color: var(--text-secondary);
            font-weight: 500;
            margin-top: 2px;
        }
        .task-item {
            padding: 16px 0;
            border-bottom: 1px solid var(--border-color);
            transition: background-color 0.2s ease;
        }
        .task-item:last-child {
            border-bottom: none;
        }
        .task-item:hover {
            background-color: rgba(0,0,0,0.005);
        }
        .edit-btn-icon, .delete-btn-icon {
            opacity: 0.4;
            transition: opacity 0.2s ease, color 0.2s ease;
        }
        .edit-btn-icon:hover, .delete-btn-icon:hover {
            opacity: 1;
        }
        .form-group label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #8c8c88;
            font-weight: 700;
        }
        .modal .card {
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        /* Style for the brand container */
        .brand-text {
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand" style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <svg viewBox="0 0 64 64" fill="none" aria-label="Voin logo" style="width:34px; height:34px; color:var(--accent-teal);">
                        <path d="M14 47L32 15L50 47" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M22 35H42" stroke="currentColor" stroke-width="6" stroke-linecap="round"/>
                    </svg>
                    <div class="brand-text">
                        <h1>Voin</h1>
                        <p class="brand-subtitle">Life routine control panel</p>
                    </div>
                </div>
                <button class="btn btn-ghost" id="sidebar-collapse-btn" title="Collapse navigation" style="min-height: unset; height: 32px; width: 32px; padding: 0; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--text-secondary); background: transparent; border: none; cursor: pointer;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
            </div>
            <nav>
                <div class="sidebar-section">
                    <p class="sidebar-label">Views</p>
                    <ul class="nav-list">
                        <li>
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <span class="nav-text">Today</span>
                                <span class="nav-link-meta">Rigid plan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('weekly-load') }}" class="nav-link {{ request()->routeIs('weekly-load') ? 'active' : '' }}">
                                <span class="nav-text">Weekly load</span>
                                <span class="nav-link-meta">7 blocks</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('calendar') }}" class="nav-link {{ request()->routeIs('calendar') ? 'active' : '' }}">
                                <span class="nav-text">Calendar</span>
                                <span class="nav-link-meta">Schedule</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('review-log') }}" class="nav-link {{ request()->routeIs('review-log') ? 'active' : '' }}">
                                <span class="nav-text">Review log</span>
                                <span class="nav-link-meta">Daily notes</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('monthly-tasks') }}" class="nav-link {{ request()->routeIs('monthly-tasks') ? 'active' : '' }}">
                                <span class="nav-text">Monthly tasks</span>
                                <span class="nav-link-meta">Review & Add</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('jules.index') }}" class="nav-link {{ request()->routeIs('jules.index') ? 'active' : '' }}">
                                <span class="nav-text">Jules Console</span>
                                <span class="nav-link-meta">AI Agent</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="sidebar-section">
                    <p class="sidebar-label">Life Tracking</p>
                    <ul class="nav-list">
                        <li>
                            <a href="{{ route('savings-tracker') }}" class="nav-link {{ request()->routeIs('savings-tracker') ? 'active' : '' }}">
                                <span class="nav-text">Savings tracker</span>
                                <span class="nav-link-meta">Cash flow</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('workout-planner') }}" class="nav-link {{ request()->routeIs('workout-planner') ? 'active' : '' }}">
                                <span class="nav-text">Workout planner</span>
                                <span class="nav-link-meta">Routine</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('credentials-vault') }}" class="nav-link {{ request()->routeIs('credentials-vault') ? 'active' : '' }}">
                                <span class="nav-text">Credentials vault</span>
                                <span class="nav-link-meta">Secure keys</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="sidebar-section">
                    <p class="sidebar-label">Account</p>
                    <ul class="nav-list">
                        <li>
                            <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                                <span class="nav-text">Profile</span>
                                <span class="nav-link-meta">Settings</span>
                            </a>
                        </li>
                        <li>
                            @php
                                $formId = 'logout-form-' . str_replace(['/', '.'], '-', request()->path());
                            @endphp
                            <form method="POST" action="{{ route('logout') }}" id="{{ $formId }}" style="display: none;">
                                @csrf
                            </form>
                            <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('{{ $formId }}').submit();">
                                <span class="nav-text" style="color: var(--color-error);">Logout</span>
                                <span class="nav-link-meta">End session</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="header">
                <div class="header-title-area" style="display: flex; align-items: center; gap: 16px;">
                    <!-- Toggle main navigation sidebar (hamburger menu) -->
                    <button class="btn btn-secondary" id="toggle-main-sidebar" title="Main menu" style="min-height: unset; height: 32px; width: 32px; padding: 0; border-radius: 8px; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; background: var(--surface-color); margin-right: -4px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                    </button>
                    <h1>Savings tracker</h1>
                </div>
                <div class="header-actions">
                    <button class="theme-toggle" id="theme-toggle" aria-label="Switch theme" style="width:44px; height:44px; border-radius:50%; background:var(--surface-color); border:1px solid var(--border-color); display:grid; place-items:center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    </button>
                </div>
            </header>

            <div class="dashboard-viewport">
                <!-- Stats Row -->
                <div class="stats">
                    <article class="card stat-card">
                        <p class="stat-label">Monthly Budget</p>
                        <p class="stat-value">${{ number_format($user->monthly_budget ?? 0, 2) }}</p>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: auto;">
                            <p class="stat-desc">Target spending limit</p>
                            <button id="set-budget-btn" class="btn btn-secondary" style="min-height: unset; height: 28px; padding: 0 10px; font-size: 0.75rem; border-radius: 6px;">Configure</button>
                        </div>
                    </article>
                    <article class="card stat-card">
                        <p class="stat-label">Total Income</p>
                        <p class="stat-value" style="color: #0f766e;">${{ number_format($income, 2) }}</p>
                        <p class="stat-desc" style="margin-top: auto;">Total incoming earnings</p>
                    </article>
                    <article class="card stat-card">
                        <p class="stat-label">Total Expenses</p>
                        <p class="stat-value" style="color: #df3b3b;">${{ number_format($expenses, 2) }}</p>
                        <p class="stat-desc" style="margin-top: auto;">Total recorded spending</p>
                    </article>
                    <article class="card stat-card">
                        <p class="stat-label">Current Savings</p>
                        <p class="stat-value">${{ number_format($savings, 2) }}</p>
                        <p class="stat-desc" style="margin-top: auto;">Net savings balance</p>
                    </article>
                </div>

                <!-- Savings Trend Chart -->
                <article class="card panel" style="margin-bottom: var(--space-6);">
                    <div class="panel-header">
                        <h3 class="panel-title">Savings Trend</h3>
                        <p class="panel-subtitle">Monthly cash flow visualization</p>
                    </div>
                    <div style="height: 320px; position: relative;">
                        <canvas id="savings-chart"></canvas>
                    </div>
                </article>

                <div class="dashboard-grid">
                    <div class="left-column">
                        <!-- Subscriptions -->
                        <article class="card panel" style="margin-bottom: var(--space-6);">
                            <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: none; padding-bottom: 0;">
                                <div>
                                    <h3 class="panel-title">Subscriptions</h3>
                                    <p class="panel-subtitle">Recurring monthly charges</p>
                                </div>
                                <button class="btn btn-secondary" id="add-sub-btn" style="min-height: unset; height: 32px; padding: 0 12px; font-size: 0.8rem; border-radius: 8px;">+ Add Sub</button>
                            </div>
                            <div class="task-list" style="margin-top: 16px;">
                                @forelse($subscriptions as $sub)
                                <div class="task-item" style="display: flex; align-items: center; justify-content: space-between;">
                                    <div style="flex: 1;">
                                        <strong style="color: var(--text-primary); font-size: 0.95rem;">{{ $sub->title }}</strong>
                                        <p class="muted tiny" style="margin-top: 2px;">
                                            <span style="font-weight: 700; color: var(--text-primary);">${{ number_format($sub->amount, 2) }}</span>
                                            <span style="color: var(--text-secondary);"> / </span>
                                            <span class="chip" style="background-color: #f3f3ee; color: #787870; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; padding: 2px 8px; border-radius: 6px; display: inline-block;">{{ $sub->billing_cycle }}</span>
                                        </p>
                                    </div>
                                    <button class="btn-ghost delete-sub-btn delete-btn-icon" data-id="{{ $sub->id }}" style="padding: 4px 10px; color: var(--color-error); font-size: 1.25rem;">×</button>
                                </div>
                                @empty
                                <div class="empty" style="text-align: center; padding: 32px; color: var(--text-secondary); font-size: 0.9rem;">No subscriptions tracked yet.</div>
                                @endforelse
                            </div>
                        </article>

                        <!-- Wishlist -->
                        <article class="card panel">
                            <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: none; padding-bottom: 0;">
                                <div>
                                    <h3 class="panel-title">Wishlist</h3>
                                    <p class="panel-subtitle">Target purchases & savings goals</p>
                                </div>
                                <button class="btn btn-secondary" id="add-wishlist-btn" style="min-height: unset; height: 32px; padding: 0 12px; font-size: 0.8rem; border-radius: 8px;">+ Add Goal</button>
                            </div>
                            <div class="task-list" style="margin-top: 16px;">
                                @forelse($wishlist as $item)
                                @php
                                    $priorityStyle = '';
                                    if ($item->priority === 'high') {
                                        $priorityStyle = 'background-color: #fef2f2; color: #e11d48; border: 1px solid rgba(225, 29, 72, 0.15);';
                                    } elseif ($item->priority === 'medium') {
                                        $priorityStyle = 'background-color: #f8fafc; color: #475569; border: 1px solid rgba(71, 85, 105, 0.15);';
                                    } else {
                                        $priorityStyle = 'background-color: #f0fdf4; color: #16a34a; border: 1px solid rgba(22, 163, 74, 0.15);';
                                    }
                                @endphp
                                <div class="task-item {{ $item->is_bought ? 'done' : '' }}" style="display: flex; align-items: center; gap: 16px;">
                                    <div class="checkbox wishlist-checkbox {{ $item->is_bought ? 'checked' : '' }}" data-id="{{ $item->id }}"></div>
                                    <div style="flex: 1;">
                                        <span class="task-title" style="font-weight: 600; font-size: 0.95rem; color: var(--text-primary);">{{ $item->title }}</span>
                                        <p class="muted tiny" style="margin-top: 2px; display: flex; align-items: center; gap: 8px;">
                                            <span style="font-weight: 700; color: var(--text-primary);">${{ number_format($item->price, 2) }}</span>
                                            <span>•</span>
                                            <span class="chip" style="{{ $priorityStyle }} font-size: 0.65rem; font-weight: 700; text-transform: uppercase; padding: 2px 8px; border-radius: 6px;">{{ $item->priority }}</span>
                                        </p>
                                    </div>
                                    <button class="btn-ghost edit-wishlist-btn edit-btn-icon" 
                                        data-id="{{ $item->id }}"
                                        data-title="{{ $item->title }}"
                                        data-price="{{ $item->price }}"
                                        data-priority="{{ $item->priority }}"
                                        style="padding: 6px; color: var(--text-secondary);">
                                        <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                </div>
                                @empty
                                <div class="empty" style="text-align: center; padding: 32px; color: var(--text-secondary); font-size: 0.9rem;">Your wishlist is empty.</div>
                                @endforelse
                            </div>
                        </article>
                    </div>

                    <div class="right-column">
                        <!-- Recent Activity -->
                        <article class="card panel">
                            <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: none; padding-bottom: 0;">
                                <div>
                                    <h3 class="panel-title">Activity</h3>
                                    <p class="panel-subtitle">Recent cash transactions</p>
                                </div>
                                <button class="btn btn-secondary" id="add-transaction-btn" style="min-height: unset; height: 32px; padding: 0 12px; font-size: 0.8rem; border-radius: 8px;">+ Add Tx</button>
                            </div>
                            <div class="task-list" style="margin-top: 16px;">
                                @forelse($transactions as $tx)
                                <div class="task-item" style="display: flex; align-items: center; justify-content: space-between;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <button class="btn-ghost edit-transaction-btn edit-btn-icon" 
                                            data-id="{{ $tx->id }}"
                                            data-type="{{ $tx->type }}"
                                            data-amount="{{ $tx->amount }}"
                                            data-category="{{ $tx->category }}"
                                            data-description="{{ $tx->description }}"
                                            data-date="{{ $tx->date }}"
                                            style="padding: 6px; color: var(--text-secondary);">
                                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </button>
                                        <div>
                                            <strong style="color: var(--text-primary); font-size: 0.95rem;">{{ $tx->category }}</strong>
                                            <p class="muted tiny" style="margin-top: 2px; color: var(--text-secondary);">{{ \Carbon\Carbon::parse($tx->date)->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                    <div style="font-weight: 700; font-size: 1rem; color: {{ $tx->type == 'income' ? '#0f766e' : '#df3b3b' }}; font-variant-numeric: tabular-nums;">
                                        {{ $tx->type == 'income' ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
                                    </div>
                                </div>
                                @empty
                                <div class="empty" style="text-align: center; padding: 32px; color: var(--text-secondary); font-size: 0.9rem;">No transactions recorded.</div>
                                @endforelse
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modals -->
    <div id="transaction-modal" class="modal"><div class="card">
        <div class="panel-header" style="display:flex; justify-content:space-between; align-items:center; border:none; padding:0;"><h3 class="panel-title" id="tx-modal-title">Transaction</h3><button class="btn-ghost" id="close-tx-modal" style="font-size:1.5rem; padding:4px;">×</button></div>
        <form id="transaction-form" style="margin-top:16px;"><input type="hidden" name="id" id="tx-id">
            <div class="form-group"><label>Type</label><select name="type" id="tx-type" class="select" required><option value="income">Income</option><option value="expense">Expense</option></select></div>
            <div class="form-group"><label>Amount</label><input type="number" name="amount" id="tx-amount" class="input" step="0.01" required></div>
            <div class="form-group"><label>Category</label><input type="text" name="category" id="tx-category" class="input" placeholder="e.g. Salary, Groceries" required></div>
            <div class="form-group"><label>Description</label><input type="text" name="description" id="tx-description" class="input" placeholder="Optional notes"></div>
            <div class="form-group"><label>Date</label><input type="date" name="date" id="tx-date" class="input" value="{{ date('Y-m-d') }}"></div>
            <div style="display:grid; grid-template-columns:1fr 2.5fr; gap:12px; margin-top:24px;"><button type="button" id="delete-tx-btn" class="btn btn-secondary" style="color:var(--color-error); border-color:var(--color-error); display:none;">Delete</button><button type="submit" class="btn btn-primary" id="tx-submit-btn">Save</button></div>
        </form>
    </div></div>

    <div id="wishlist-modal" class="modal"><div class="card">
        <div class="panel-header" style="display:flex; justify-content:space-between; align-items:center; border:none; padding:0;"><h3 class="panel-title" id="wish-modal-title">Wishlist Item</h3><button class="btn-ghost" id="close-wish-modal" style="font-size:1.5rem; padding:4px;">×</button></div>
        <form id="wishlist-form" style="margin-top:16px;"><input type="hidden" name="id" id="wish-id">
            <div class="form-group"><label>Item</label><input type="text" name="title" id="wish-title" class="input" placeholder="e.g. New Headphones" required></div>
            <div class="form-group"><label>Price</label><input type="number" name="price" id="wish-price" class="input" step="0.01" required></div>
            <div class="form-group"><label>Priority</label><select name="priority" id="wish-priority" class="select"><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option></select></div>
            <div style="display:grid; grid-template-columns:1fr 2.5fr; gap:12px; margin-top:24px;"><button type="button" id="delete-wish-btn" class="btn btn-secondary" style="color:var(--color-error); border-color:var(--color-error); display:none;">Delete</button><button type="submit" class="btn btn-primary" id="wish-submit-btn">Save</button></div>
        </form>
    </div></div>

    <div id="sub-modal" class="modal"><div class="card">
        <div class="panel-header" style="display:flex; justify-content:space-between; align-items:center; border:none; padding:0;"><h3 class="panel-title">Add Subscription</h3><button class="btn-ghost" id="close-sub-modal" style="font-size:1.5rem; padding:4px;">×</button></div>
        <form id="sub-form" style="margin-top:16px;">
            <div class="form-group"><label>Title</label><input type="text" name="title" class="input" placeholder="e.g. Spotify Premium" required></div>
            <div class="form-group"><label>Amount</label><input type="number" name="amount" class="input" step="0.01" required></div>
            <div class="form-group"><label>Cycle</label><select name="billing_cycle" class="select"><option value="monthly">Monthly</option><option value="yearly">Yearly</option></select></div>
            <button type="submit" class="btn btn-primary" style="width:100%; margin-top:20px;">Save</button>
        </form>
    </div></div>

    <div id="budget-modal" class="modal"><div class="card">
        <div class="panel-header" style="display:flex; justify-content:space-between; align-items:center; border:none; padding:0;"><h3 class="panel-title">Set Budget</h3><button class="btn-ghost" id="close-budget-modal" style="font-size:1.5rem; padding:4px;">×</button></div>
        <form id="budget-form" style="margin-top:16px;"><div class="form-group"><label>Monthly Target ($)</label><input type="number" name="monthly_budget" class="input" value="{{ $user->monthly_budget ?? 0 }}" required></div><button type="submit" class="btn btn-primary" style="width:100%; margin-top:20px;">Save Target</button></form>
    </div></div>

    <div id="confirm-modal" class="modal"><div class="card" style="text-align:center;">
        <h3 class="panel-title">Confirm Delete</h3>
        <p class="muted tiny" id="confirm-modal-message" style="margin: 16px 0; color: var(--text-secondary);">This action is permanent.</p>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;"><button type="button" id="confirm-cancel-btn" class="btn btn-secondary">Cancel</button><button type="button" id="confirm-delete-btn" class="btn btn-primary" style="background:#df3b3b; border-color:#df3b3b; box-shadow:none;">Delete</button></div>
    </div></div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
