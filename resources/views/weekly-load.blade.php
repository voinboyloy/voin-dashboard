<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voin - Weekly Load</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://api.fontshare.com">
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
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
            <div class="brand">
                <svg viewBox="0 0 64 64" fill="none" aria-label="Voin logo" style="width:34px; height:34px; color:var(--accent-teal);">
                    <path d="M14 47L32 15L50 47" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 35H42" stroke="currentColor" stroke-width="6" stroke-linecap="round"/>
                </svg>
                <div class="brand-text">
                    <h1>Voin</h1>
                    <p class="brand-subtitle">Life routine control panel</p>
                </div>
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
                            <a href="{{ route('calendar') }}" class="flex items-center gap-3 px-2 py-1.5 text-sm rounded-md text-axis-muted hover:bg-axis-border hover:text-axis-text">Calendar</a>
                <a href="{{ route('review-log') }}" class="nav-link {{ request()->routeIs('review-log') ? 'active' : '' }}">
                                <span class="nav-text">Review log</span>
                                <span class="nav-link-meta">Daily notes</span>
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
                @if(isset($notes))
                <div class="sidebar-section">
                    <p class="sidebar-label">Scratchpad</p>
                    <div style="padding: 0 12px; margin-top: 8px;">
                        <textarea id="quick-note" placeholder="Brain dump..." style="width: 100%; font-size: 0.8rem; background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; padding: 8px; color: var(--text-primary); resize: none;" rows="2"></textarea>
                    </div>
                    <div id="notes-list" style="padding: 12px; max-height: 150px; overflow-y: auto;">
                        @foreach($notes as $note)
                        <div class="task-item" style="border-bottom: none; padding: 4px 0; display: flex; align-items: center; justify-content: space-between;">
                            <span style="font-size: 0.75rem; color: var(--text-secondary); line-height: 1.2; flex: 1;">{{ $note->content }}</span>
                            <button class="btn btn-ghost delete-note-btn" data-id="{{ $note->id }}" style="padding: 0 4px; min-height: unset; color: #df3b3b; font-size: 1rem;">×</button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
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
                <div class="header-title-area">
                    <h1>Weekly load</h1>
                </div>
                <div class="header-actions">
                    <button class="theme-toggle" id="theme-toggle" aria-label="Switch theme" style="width:44px; height:44px; border-radius:50%; background:var(--surface-color); border:1px solid var(--border-color); display:grid; place-items:center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    </button>
                </div>
            </header>

            <div class="dashboard-viewport">
                <div class="section-panel" style="margin-bottom: 24px;">
                    <div class="panel-header" style="border-bottom: none; padding-bottom: 0;">
                        <h3 class="panel-title">Planned Workload</h3>
                        <p class="panel-subtitle">Block counts scheduled across daily timeline columns</p>
                    </div>
                    <div class="card" style="margin-top: 16px;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 16px;">
                            @php
                                $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                            @endphp
                            @foreach($days as $day)
                            <div style="text-align: center; padding: 20px 16px; border: 1px solid var(--border-color); border-radius: 12px; background: rgba(0,0,0,0.005);">
                                <div style="font-weight: 700; font-size: 0.8rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">{{ $day }}</div>
                                <div style="font-size: 1.85rem; font-weight: 900; color: var(--text-primary);">{{ $blocks->count() }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary); font-weight: 500; margin-top: 4px;">Blocks</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="section-panel">
                    <div class="panel-header" style="border-bottom: none; padding-bottom: 0;">
                        <h3 class="panel-title">Weekly Tasks Summary</h3>
                        <p class="panel-subtitle">Total completion progress metric</p>
                    </div>
                    <div class="card" style="margin-top: 16px;">
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; text-align: center;">
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">Total Tasks</div>
                                <div style="font-size: 1.85rem; font-weight: 900; color: var(--text-primary); margin-top: 6px;">{{ $tasks->count() }}</div>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">Completed</div>
                                <div style="font-size: 1.85rem; font-weight: 900; color: #0f766e; margin-top: 6px;">{{ $tasks->where('is_done', true)->count() }}</div>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">Remaining</div>
                                <div style="font-size: 1.85rem; font-weight: 900; color: #df3b3b; margin-top: 6px;">{{ $tasks->where('is_done', false)->count() }}</div>
                            </div>
                        </div>
                        <div style="height: 12px; background: var(--border-color); border-radius: 6px; overflow: hidden; margin-top: 16px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);">
                            @php
                                $percent = $tasks->count() > 0 ? ($tasks->where('is_done', true)->count() / $tasks->count()) * 100 : 0;
                            @endphp
                            <div style="height: 100%; width: {{ $percent }}%; background: #0f766e; border-radius: 6px; transition: width 0.3s ease;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modals -->
    <div id="confirm-modal" class="modal">
        <div class="card" style="text-align: center;">
            <h3 class="panel-title">Confirm Delete</h3>
            <p class="muted tiny" id="confirm-modal-message" style="margin: 16px 0; color: var(--text-secondary);">This action is permanent.</p>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;"><button type="button" id="confirm-cancel-btn" class="btn btn-secondary">Cancel</button><button type="button" id="confirm-delete-btn" class="btn btn-primary" style="background:#df3b3b; border-color:#df3b3b; box-shadow:none;">Delete</button></div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
