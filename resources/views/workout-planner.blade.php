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
    <title>Voin - Workout Planner</title>
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
                <div class="header-title-area" style="display: flex; align-items: center; gap: 16px;">
                    <!-- Toggle main navigation sidebar (hamburger menu) -->
                    <button class="btn btn-secondary" id="toggle-main-sidebar" title="Main menu" style="min-height: unset; height: 32px; width: 32px; padding: 0; border-radius: 8px; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; background: var(--surface-color); margin-right: -4px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                    </button>
                    <h1>Workout planner</h1>
                </div>
                <div class="header-actions">
                    <button class="theme-toggle" id="theme-toggle" aria-label="Switch theme" style="width:44px; height:44px; border-radius:50%; background:var(--surface-color); border:1px solid var(--border-color); display:grid; place-items:center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    </button>
                </div>
            </header>

            <div class="dashboard-viewport">
                <div class="dashboard-grid">
                    <div class="left-column">
                        <!-- Workout Plans -->
                        <div class="section-panel">
                            <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: none; padding-bottom: 0;">
                                <div>
                                    <h3 class="panel-title">Your Workout Plans</h3>
                                    <p class="panel-subtitle">Custom routines assigned to week days</p>
                                </div>
                                <button class="btn btn-secondary" style="min-height: unset; height: 32px; padding: 0 12px; font-size: 0.8rem; border-radius: 8px;">+ Create Plan</button>
                            </div>
                            <div style="margin-top: 16px;">
                                @forelse($plans as $plan)
                                <div class="card" style="margin-bottom: 24px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                                        <div>
                                            <h4 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary);">{{ $plan->title }}</h4>
                                            <p style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 2px;">Scheduled: {{ $plan->day_of_week }}</p>
                                        </div>
                                        <span class="chip chip-work" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; padding: 2px 8px; border-radius: 6px;">{{ $plan->exercises->count() }} Exercises</span>
                                    </div>
                                    <div class="task-list">
                                        @foreach($plan->exercises as $ex)
                                        <div class="task-item" style="display: flex; align-items: center; justify-content: space-between;">
                                            <div style="flex: 1;">
                                                <div style="font-size: 0.95rem; font-weight: 600; color: var(--text-primary);">{{ $ex->title }}</div>
                                                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 2px;">{{ $ex->pivot->sets }} sets × {{ $ex->pivot->reps }} reps • {{ $ex->equipment }}</div>
                                            </div>
                                            <span class="chip chip-routine" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; padding: 2px 8px; border-radius: 6px;">{{ $ex->muscle_group }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @empty
                                <div class="card" style="text-align: center; padding: 40px;">
                                    <p style="font-size: 0.9rem; color: var(--text-secondary);">No workout plans yet. Time to build one!</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="right-column">
                        <!-- Exercise Library -->
                        <div class="section-panel">
                            <div class="panel-header" style="border-bottom: none; padding-bottom: 0;">
                                <h3 class="panel-title">Exercise Library</h3>
                                <p class="panel-subtitle">Available catalog categorized by muscle group</p>
                            </div>
                            <div class="card" style="max-height: 800px; overflow-y: auto; margin-top: 16px; padding: 20px;">
                                @foreach($allExercises as $muscle => $exercises)
                                <div style="margin-bottom: 24px;">
                                    <h4 style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 12px; border-bottom: 1px solid var(--border-color); padding-bottom: 6px; letter-spacing: 0.05em;">{{ $muscle }}</h4>
                                    @foreach($exercises as $ex)
                                    <div style="margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; padding: 4px 0;">
                                        <div>
                                            <div style="font-size: 0.95rem; font-weight: 600; color: var(--text-primary);">{{ $ex->title }}</div>
                                            <div style="font-size: 0.72rem; color: var(--text-secondary); margin-top: 2px;">Focus: {{ $ex->equipment }}</div>
                                        </div>
                                        <button class="btn btn-secondary add-ex-to-plan-btn" 
                                            data-ex-id="{{ $ex->id }}"
                                            data-ex-title="{{ $ex->title }}"
                                            style="min-height: unset; height: 28px; padding: 0 10px; font-size: 0.75rem; border-radius: 6px;">+ Add</button>
                                    </div>
                                    @endforeach
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Exercise to Plan Modal -->
    <div id="add-ex-modal" class="modal">
        <div class="card">
            <div class="panel-header" style="display:flex; justify-content:space-between; align-items:center; border:none; padding:0;">
                <h3 class="panel-title">Add to Workout Plan</h3>
                <button class="btn-ghost" id="close-add-ex-modal" style="font-size:1.5rem; padding:4px;">×</button>
            </div>
            <p id="selected-ex-title" style="margin-top: 12px; margin-bottom: 16px; font-weight: 700; color: #0f766e;"></p>
            <form id="add-ex-form">
                <input type="hidden" name="exercise_id" id="selected-ex-id">
                <div class="form-group">
                    <label>Select Plan</label>
                    <select name="workout_plan_id" class="select" required>
                        @foreach($plans as $plan)
                        <option value="{{ $plan->id }}">{{ $plan->title }} ({{ $plan->day_of_week }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="display: flex; gap: 12px;">
                    <div style="flex: 1;">
                        <label>Sets</label>
                        <input type="number" name="sets" class="input" value="3" required>
                    </div>
                    <div style="flex: 1;">
                        <label>Reps</label>
                        <input type="number" name="reps" class="input" value="12" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">Add to Plan</button>
            </form>
        </div>
    </div>

    <!-- Custom Confirm Delete Modal -->
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
