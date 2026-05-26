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
    <title>Voin - Today Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://api.fontshare.com">
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
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
                    <h1>Today dashboard</h1>
                </div>
                <div class="header-actions" style="display: flex; gap: 12px; align-items: center;">
                    <button class="btn btn-secondary" id="load-sample-btn" style="min-height: unset; height: 38px; padding: 0 12px; font-size: 0.8rem; border-radius: 8px;">Load Sample Day</button>
                    <button class="theme-toggle" id="theme-toggle" aria-label="Switch theme" style="width:38px; height:38px; border-radius:50%; background:var(--surface-color); border:1px solid var(--border-color); display:grid; place-items:center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    </button>
                </div>
            </header>

            <div class="dashboard-viewport">
                <!-- Stats Row -->
                <div class="stats">
                    <article class="card stat-card">
                        <p class="stat-label">Planned blocks</p>
                        <p class="stat-value">{{ $blocks->count() }}</p>
                        <p class="stat-desc">Total scheduled today</p>
                    </article>
                    <article class="card stat-card">
                        <p class="stat-label">Active tasks</p>
                        <p class="stat-value" id="kpi-active-tasks" style="color: #0f766e;">{{ $tasks->where('is_done', false)->whereNull('carry_over_date')->count() }}</p>
                        <p class="stat-desc">Remaining to complete</p>
                    </article>
                    <article class="card stat-card">
                        <p class="stat-label">Completion</p>
                        <p class="stat-value" id="kpi-completion" style="color: #df3b3b;">
                            @php
                                $total = $tasks->whereNull('carry_over_date')->count();
                                $done = $tasks->where('is_done', true)->whereNull('carry_over_date')->count();
                                echo $total > 0 ? round(($done / $total) * 100) : 0;
                            @endphp%
                        </p>
                        <p class="stat-desc">Tasks finished today</p>
                    </article>
                    <article class="card stat-card">
                        <p class="stat-label">Carry-over</p>
                        <p class="stat-value" style="color: var(--text-primary);">{{ $tasks->whereNotNull('carry_over_date')->count() }}</p>
                        <p class="stat-desc">Items for tomorrow</p>
                    </article>
                </div>

                <div class="dashboard-grid">
                    <!-- Left Column -->
                    <div class="left-column">
                        <!-- Time Blocks -->
                        <div class="section-panel" id="time-blocks-panel" style="margin-bottom: 24px;">
                            <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: none; padding-bottom: 0;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <h3 class="panel-title">Time Blocks</h3>
                                    <button id="toggle-blocks-btn" class="btn btn-secondary" style="min-height: unset; height: 28px; padding: 0 10px; font-size: 0.75rem; border-radius: 6px;">Hide</button>
                                </div>
                                <button class="btn btn-secondary" id="add-block-btn" style="min-height: unset; height: 32px; padding: 0 12px; font-size: 0.8rem; border-radius: 8px;">+ Add Block</button>
                            </div>
                            <div class="card" id="time-blocks-content" style="margin-top: 16px;">
                                @foreach($blocks as $block)
                                <div class="timeline-item">
                                    <div class="timeline-time" style="font-weight: 700;">
                                        {{ \Carbon\Carbon::parse($block->starts_at)->format('H:i') }}<br>
                                        {{ \Carbon\Carbon::parse($block->ends_at)->format('H:i') }}
                                    </div>
                                    <div class="timeline-content">
                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <span class="chip chip-{{ $block->block_type }}" style="font-size:0.65rem; font-weight:700; text-transform:uppercase; padding:2px 8px; border-radius:6px;">{{ $block->block_type }}</span>
                                                <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary);">{{ $block->title }}</h4>
                                            </div>
                                            <button class="btn btn-ghost edit-block-btn edit-btn-icon" 
                                                data-id="{{ $block->id }}"
                                                data-title="{{ $block->title }}"
                                                data-type="{{ $block->block_type }}"
                                                data-starts="{{ $block->starts_at }}"
                                                data-ends="{{ $block->ends_at }}"
                                                data-notes="{{ $block->notes }}"
                                                style="padding: 4px; min-height: unset;">
                                                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </button>
                                        </div>
                                        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 4px;">{{ $block->notes ?? 'Focus on ' . $block->title }}</p>
                                        <div style="font-size: 0.75rem; font-weight: 700; margin-top: 8px; color: var(--accent-teal);">
                                            {{ $block->tasks->where('is_done', true)->count() }}/{{ $block->tasks->count() }} done
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Task Lanes -->
                        <div class="section-panel" style="margin-bottom: 24px;">
                            <div class="panel-header" style="border-bottom: none; padding-bottom: 0;">
                                <h3 class="panel-title">Task Lanes</h3>
                                <p class="panel-subtitle">Habits & activities mapped to schedule slots</p>
                            </div>
                            <div style="margin-top: 16px;">
                                @foreach($blocks as $block)
                                <div class="card" style="margin-bottom: 16px;">
                                    <div style="margin-bottom: 12px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; display: flex; justify-content: space-between; align-items: center;">
                                        <div>
                                            <h4 style="font-size: 0.95rem; font-weight: 700;">{{ $block->title }}</h4>
                                            <span style="font-size: 0.75rem; color: var(--text-secondary)">{{ \Carbon\Carbon::parse($block->starts_at)->format('H:i') }} - {{ \Carbon\Carbon::parse($block->ends_at)->format('H:i') }}</span>
                                        </div>
                                        <span class="chip chip-{{ $block->block_type }}" style="font-size:0.6rem; font-weight:700; text-transform:uppercase; padding:2px 6px; border-radius:4px;">{{ $block->block_type }}</span>
                                    </div>
                                    <div class="task-list">
                                        @foreach($habits->where('time_block_id', $block->id) as $habit)
                                        <div class="task-item" style="display: flex; align-items: center; gap: 12px;">
                                            <div class="checkbox habit-checkbox {{ $habit->completedToday() ? 'checked' : '' }}" data-id="{{ $habit->id }}"></div>
                                            <div style="flex: 1; display: flex; align-items: center; justify-content: space-between;">
                                                <span class="task-title" style="font-weight: 500; font-size: 0.95rem;">{{ $habit->title }}</span>
                                                <span class="chip chip-routine" style="font-size: 0.6rem; font-weight: 700; text-transform: uppercase; padding: 2px 6px; border-radius: 4px;">Habit</span>
                                            </div>
                                        </div>
                                        @endforeach

                                        @empty($block->tasks->whereNull('carry_over_date'))
                                            @if($habits->where('time_block_id', $block->id)->isEmpty())
                                                <p style="font-size: 0.85rem; color: var(--text-secondary); text-align: center; padding: 12px;">No activities for this block.</p>
                                            @endif
                                        @else
                                            @foreach($block->tasks->whereNull('carry_over_date') as $task)
                                            <div class="task-item {{ $task->is_done ? 'done' : '' }}" style="display: flex; align-items: center; gap: 12px;">
                                                <div class="checkbox task-checkbox {{ $task->is_done ? 'checked' : '' }}" data-task-id="{{ $task->id }}"></div>
                                                <div style="flex: 1;">
                                                    <span class="task-title" style="font-weight: 500; font-size: 0.95rem;">{{ $task->title }}</span>
                                                    @if($task->review_note)
                                                        <p style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 2px;">📝 {{ $task->review_note }}</p>
                                                    @endif
                                                </div>
                                                <div style="display: flex; gap: 8px; align-items: center;">
                                                    <button class="btn btn-ghost edit-task-btn edit-btn-icon" 
                                                        data-id="{{ $task->id }}"
                                                        data-title="{{ $task->title }}"
                                                        data-block-id="{{ $task->time_block_id }}"
                                                        data-review-note="{{ $task->review_note }}"
                                                        style="padding: 4px; min-height: unset;">
                                                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                                    </button>
                                                    <button class="btn btn-secondary btn-carry" data-task-id="{{ $task->id }}" style="min-height: unset; height: 26px; padding: 0 8px; font-size: 0.7rem; border-radius: 6px;">Carry</button>
                                                </div>
                                            </div>
                                            @endforeach
                                        @endempty
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Today's Workout -->
                        <div class="section-panel">
                            <div class="panel-header" style="border-bottom: none; padding-bottom: 0;">
                                <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                                    <div>
                                        <h3 class="panel-title">Today's Workout</h3>
                                        <p class="panel-subtitle">{{ now()->format('l') }} routine plan</p>
                                    </div>
                                    <span class="chip chip-work" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; padding: 3px 8px; border-radius: 6px;">{{ $workoutPlan->title ?? 'Rest Day' }}</span>
                                </div>
                            </div>
                            <div class="card" style="margin-top: 16px;">
                                @if($workoutPlan && $workoutPlan->exercises->count() > 0)
                                    @foreach($workoutPlan->exercises as $ex)
                                    <div class="task-item" style="display: flex; align-items: center; gap: 12px;">
                                        <div class="checkbox exercise-checkbox {{ in_array($ex->id, $exerciseLogs) ? 'checked' : '' }}" 
                                            data-plan-id="{{ $workoutPlan->id }}" 
                                            data-ex-id="{{ $ex->id }}"></div>
                                        <div style="flex: 1;">
                                            <span class="task-title" style="font-weight: 600; font-size: 0.95rem; color: var(--text-primary);">{{ $ex->title }}</span>
                                            <p style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 2px;">{{ $ex->pivot->sets }} sets × {{ $ex->pivot->reps }} reps • {{ $ex->equipment }}</p>
                                        </div>
                                        <span class="chip chip-routine" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; padding: 2px 8px; border-radius: 6px;">{{ $ex->muscle_group }}</span>
                                    </div>
                                    @endforeach
                                @else
                                    <div style="text-align: center; padding: 24px;">
                                        <p style="font-size: 0.85rem; color: var(--text-secondary);">Enjoy your rest day or do some light stretching!</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="right-column">
                        <!-- Focus Timer -->
                        <div class="section-panel" style="margin-bottom: 24px;">
                            <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: none; padding-bottom: 0;">
                                <div>
                                    <h3 class="panel-title">Focus Timer</h3>
                                    <p class="panel-subtitle">Pomodoro routine slots</p>
                                </div>
                                <span id="pomo-status" class="chip chip-study" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; padding: 3px 8px; border-radius: 6px;">Focus</span>
                            </div>
                            <div class="card" style="text-align: center; margin-top: 16px;">
                                <div id="pomo-display" style="font-size: 2.80rem; font-weight: 700; font-variant-numeric: tabular-nums; margin-bottom: 16px; letter-spacing: -0.02em; color: var(--text-primary);">25:00</div>
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <button id="pomo-start" class="btn btn-primary" style="min-height: unset; height: 32px; padding: 0 16px; font-size: 0.75rem; border-radius: 8px;">Start</button>
                                    <button id="pomo-pause" class="btn btn-secondary" style="min-height: unset; height: 32px; padding: 0 16px; font-size: 0.75rem; border-radius: 8px;">Pause</button>
                                    <button id="pomo-reset" class="btn btn-ghost" style="min-height: unset; height: 32px; padding: 0 16px; font-size: 0.75rem; border-radius: 8px;">Reset</button>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="section-panel" style="margin-bottom: 24px;">
                            <div class="panel-header" style="border-bottom: none; padding-bottom: 0;">
                                <h3 class="panel-title">Actions</h3>
                                <p class="panel-subtitle">Quick task capture & review forms</p>
                            </div>
                            <div class="card" style="margin-top: 16px;">
                                <form id="task-form">
                                    <input type="hidden" name="task_date" value="{{ now()->toDateString() }}">
                                    <div class="form-group">
                                        <label>New Task</label>
                                        <input type="text" name="title" class="input" placeholder="What needs to be done?" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Assign to Block</label>
                                        <select name="time_block_id" class="select" required>
                                            @foreach($blocks as $block)
                                            <option value="{{ $block->id }}">{{ $block->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Review Note (Optional)</label>
                                        <textarea name="review_note" class="input" rows="2" placeholder="Task details or review notes..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 8px;">Save Task</button>
                                </form>

                                <hr style="margin: 28px 0; border: none; border-top: 1px solid var(--border-color);">

                                <form id="review-form">
                                    <div class="form-group">
                                        <label>Today's Primary Focus</label>
                                        <input type="text" name="daily_focus" class="input" placeholder="One main goal for today" value="{{ $review->daily_focus ?? '' }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Daily Review Summary</label>
                                        <textarea name="summary" class="input" rows="3" placeholder="How did today go?">{{ $review->summary ?? '' }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Focus Score (1-10)</label>
                                        <input type="number" name="focus_score" class="input" min="1" max="10" value="{{ $review->focus_score ?? 8 }}">
                                    </div>
                                    <button type="submit" class="btn btn-secondary" style="width: 100%; margin-top: 8px;">Save Review & Carry Over</button>
                                </form>
                            </div>
                        </div>

                        <!-- Habit Tracker -->
                        <div class="section-panel" style="margin-bottom: 24px;">
                            <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: none; padding-bottom: 0;">
                                <div>
                                    <h3 class="panel-title">Habit Tracker</h3>
                                    <p class="panel-subtitle">Active streaks & targets</p>
                                </div>
                                <button class="btn btn-secondary" id="add-habit-btn" style="min-height: unset; height: 32px; padding: 0 12px; font-size: 0.8rem; border-radius: 8px;">+ New</button>
                            </div>
                            <div class="card" style="margin-top: 16px;">
                                @forelse($habits as $habit)
                                <div class="task-item" style="display: flex; align-items: center; gap: 12px;">
                                    <div class="checkbox habit-checkbox {{ $habit->completedToday() ? 'checked' : '' }}" data-id="{{ $habit->id }}"></div>
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; justify-content: space-between;">
                                            <span class="task-title" style="font-weight: 600; font-size: 0.95rem;">{{ $habit->title }}</span>
                                            <span style="font-size: 0.72rem; color: var(--accent-teal); font-weight: 700;">🔥 {{ $habit->calculateStreak() }}d</span>
                                        </div>
                                        <p style="font-size: 0.72rem; color: var(--text-secondary); margin-top: 2px;">Target: {{ $habit->target ?? 'Daily' }} • {{ $habit->timeBlock->title ?? 'No Block' }}</p>
                                    </div>
                                    <div style="display: flex; gap: 4px; align-items: center;">
                                        <button class="btn btn-ghost edit-habit-btn edit-btn-icon" 
                                            data-id="{{ $habit->id }}"
                                            data-title="{{ $habit->title }}"
                                            data-target="{{ $habit->target }}"
                                            data-block-id="{{ $habit->time_block_id }}"
                                            style="padding: 4px; min-height: unset;">
                                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </button>
                                        <button class="btn btn-ghost delete-habit-btn delete-btn-icon" data-id="{{ $habit->id }}" style="padding: 4px; min-height: unset; color: var(--color-error);">
                                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </div>
                                @empty
                                <p style="font-size: 0.85rem; color: var(--text-secondary); text-align: center; padding: 12px;">No habits tracked yet.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Carry-over Panel -->
                        <div class="section-panel" style="margin-bottom: 24px;">
                            <div class="panel-header" style="border-bottom: none; padding-bottom: 0;">
                                <h3 class="panel-title">Carry-over Tasks</h3>
                                <p class="panel-subtitle">Items carried over to tomorrow</p>
                            </div>
                            <div class="card" style="margin-top: 16px;">
                                @forelse($tasks->whereNotNull('carry_over_date') as $task)
                                <div class="task-item" style="display: flex; align-items: center; justify-content: space-between;">
                                    <div style="flex: 1;">
                                        <span class="task-title" style="font-weight: 600; font-size: 0.95rem; color: var(--text-primary);">{{ $task->title }}</span>
                                        <p style="font-size: 0.72rem; color: var(--text-secondary); margin-top: 2px;">From: {{ $task->timeBlock->title ?? 'Unassigned' }}</p>
                                    </div>
                                    <span class="chip chip-study" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; padding: 2px 8px; border-radius: 6px;">Tomorrow</span>
                                </div>
                                @empty
                                <div style="text-align: center; padding: 24px;">
                                    <p style="font-size: 0.85rem; color: var(--text-secondary);">No carry-over items.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modals -->
    <!-- Time Block Modal -->
    <div id="block-modal" class="modal">
        <div class="card">
            <div class="panel-header" style="display:flex; justify-content:space-between; align-items:center; border:none; padding:0;">
                <h3 class="panel-title" id="block-modal-title">Time Block</h3>
                <button class="btn-ghost" id="close-block-modal" style="font-size:1.5rem; padding:4px;">×</button>
            </div>
            <form id="block-form" style="margin-top:16px;">
                <input type="hidden" name="id" id="block-id">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" id="block-title" class="input" required placeholder="e.g., Morning Setup">
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select name="block_type" id="block-type" class="select" required>
                        <option value="work">Work</option>
                        <option value="study">Study</option>
                        <option value="review">Review</option>
                        <option value="routine">Routine</option>
                    </select>
                </div>
                <div class="form-group" style="display: flex; gap: 12px;">
                    <div style="flex: 1;">
                        <label>Start</label>
                        <input type="time" name="starts_at" id="block-starts" class="input" required>
                    </div>
                    <div style="flex: 1;">
                        <label>End</label>
                        <input type="time" name="ends_at" id="block-ends" class="input" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" id="block-notes" class="input" rows="2" placeholder="What is this block for?"></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 2.5fr; gap: 12px; margin-top:24px;">
                    <button type="button" id="delete-block-btn" class="btn btn-secondary" style="color:var(--color-error); border-color:var(--color-error); display: none;">Delete</button>
                    <button type="submit" class="btn btn-primary" id="block-submit-btn">Save Block</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Task Modal -->
    <div id="task-modal" class="modal">
        <div class="card">
            <div class="panel-header" style="display:flex; justify-content:space-between; align-items:center; border:none; padding:0;">
                <h3 class="panel-title">Edit Task</h3>
                <button class="btn-ghost" id="close-task-modal" style="font-size:1.5rem; padding:4px;">×</button>
            </div>
            <form id="edit-task-form" style="margin-top:16px;">
                <input type="hidden" name="id" id="edit-task-id">
                <div class="form-group">
                    <label>Task Title</label>
                    <input type="text" name="title" id="edit-task-title" class="input" required>
                </div>
                <div class="form-group">
                    <label>Assign to Block</label>
                    <select name="time_block_id" id="edit-task-block-id" class="select" required>
                        @foreach($blocks as $block)
                        <option value="{{ $block->id }}">{{ $block->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Review Note</label>
                    <textarea name="review_note" id="edit-task-review-note" class="input" rows="2" placeholder="Task details or review notes..."></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 2.5fr; gap: 12px; margin-top:24px;">
                    <button type="button" id="delete-task-btn" class="btn btn-secondary" style="color:var(--color-error); border-color:var(--color-error);">Delete</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Habit Modal -->
    <div id="habit-modal" class="modal">
        <div class="card">
            <div class="panel-header" style="display:flex; justify-content:space-between; align-items:center; border:none; padding:0;">
                <h3 class="panel-title" id="habit-modal-title">Track New Habit</h3>
                <button class="btn-ghost" id="close-habit-modal" style="font-size:1.5rem; padding:4px;">×</button>
            </div>
            <form id="habit-form" style="margin-top:16px;">
                <input type="hidden" name="id" id="habit-modal-id">
                <div class="form-group">
                    <label>Habit Name</label>
                    <input type="text" name="title" id="habit-modal-title-input" class="input" required placeholder="e.g., Drink Water, Meditate">
                </div>
                <div class="form-group">
                    <label>Target / Goal</label>
                    <input type="text" name="target" id="habit-modal-target-input" class="input" placeholder="e.g., 8 glasses, 15 mins">
                </div>
                <div class="form-group">
                    <label>Assign to Block (Optional)</label>
                    <select name="time_block_id" id="habit-modal-block-id" class="select">
                        <option value="">No specific block</option>
                        @foreach($blocks as $block)
                        <option value="{{ $block->id }}">{{ $block->title }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">Start Tracking</button>
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
