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
    <title>Voin - Monthly Tasks Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://api.fontshare.com">
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        .header-title-area h1 {
            font-size: 2.25rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            color: var(--text-primary);
            line-height: 1.1;
        }
        .brand-text {
            display: flex;
            flex-direction: column;
        }

        /* Monthly Calendar Grid Layout */
        .monthly-layout-container {
            display: grid;
            grid-template-columns: 1.4fr 0.9fr;
            gap: 28px;
            align-items: start;
            margin-top: 16px;
        }

        .calendar-grid-header {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 12px;
            margin-bottom: 12px;
            text-align: center;
        }

        .calendar-header-day {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 8px 0;
        }

        .calendar-days-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 12px;
        }

        .day-cell {
            aspect-ratio: 1.15;
            background-color: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 12px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            user-select: none;
        }

        .day-cell:hover {
            transform: translateY(-2px);
            border-color: var(--accent-teal);
            box-shadow: var(--shadow-md);
        }

        .day-cell.muted {
            opacity: 0.35;
            background: rgba(0, 0, 0, 0.015);
        }

        .day-cell.active-day {
            border: 2px solid var(--accent-teal);
            box-shadow: 0 0 12px var(--accent-teal-soft);
            background: var(--accent-teal-soft);
        }

        .day-cell.today-cell {
            border: 1.5px dashed var(--accent-teal);
        }

        .day-number {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .day-cell.active-day .day-number {
            color: var(--accent-teal);
        }

        .day-task-indicator {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .progress-dots {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
            margin-top: 4px;
        }

        .progress-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .progress-dot.done {
            background-color: var(--accent-teal);
        }

        .progress-dot.pending {
            background-color: #f59e0b; /* Amber */
        }

        .day-status-pill {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-flex;
            align-self: flex-start;
        }

        .day-status-done {
            background-color: var(--accent-teal-soft);
            color: var(--accent-teal);
        }

        .day-status-partial {
            background-color: rgba(245, 158, 11, 0.08);
            color: #d97706;
        }

        .day-status-pending {
            background-color: rgba(0, 0, 0, 0.03);
            color: var(--text-secondary);
        }

        /* Detail Panel Card */
        .detail-panel-card {
            position: sticky;
            top: calc(var(--header-height) + 40px);
            max-height: calc(100vh - var(--header-height) - 100px);
            overflow-y: auto;
        }

        .category-legend {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
            background: var(--surface-color);
            padding: 12px 20px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        @media (max-width: 1024px) {
            .monthly-layout-container {
                grid-template-columns: 1fr;
            }
            .detail-panel-card {
                position: static;
                max-height: none;
            }
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
                    <h1>Monthly tasks</h1>

                    <!-- Month view navigation -->
                    <div style="display: flex; align-items: center; gap: 6px; background: rgba(0,0,0,0.02); padding: 4px; border-radius: 8px; border: 1px solid var(--border-color); margin-left: 12px;">
                        <a href="?month={{ now()->format('Y-m') }}" class="btn btn-secondary" style="min-height: unset; height: 28px; padding: 0 10px; font-size: 0.75rem; border-radius: 6px;">This Month</a>
                        <div style="display: flex; align-items: center; gap: 1px;">
                            <a href="?month={{ $month->copy()->subMonth()->format('Y-m') }}" class="btn btn-secondary" style="min-height: unset; height: 28px; width: 28px; padding: 0; border-radius: 6px;" title="Previous month">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg>
                            </a>
                            <a href="?month={{ $month->copy()->addMonth()->format('Y-m') }}" class="btn btn-secondary" style="min-height: unset; height: 28px; width: 28px; padding: 0; border-radius: 6px;" title="Next month">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6 6-6"/></svg>
                            </a>
                        </div>
                        <span style="font-size: 0.85rem; font-weight: 700; padding: 0 8px; color: var(--text-primary);">
                            {{ $month->format('F Y') }}
                        </span>
                    </div>
                </div>
                <div class="header-actions">
                    <button class="theme-toggle" id="theme-toggle" aria-label="Switch theme" style="width:38px; height:38px; border-radius:50%; background:var(--surface-color); border:1px solid var(--border-color); display:grid; place-items:center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    </button>
                </div>
            </header>

            <div class="dashboard-viewport">
                <!-- Monthly Metrics / KPI Row -->
                <div class="stats">
                    <article class="card stat-card" style="border-bottom: 3px solid var(--accent-teal);">
                        <p class="stat-label">Monthly Tasks</p>
                        <p class="stat-value">{{ $kpiTotal }}</p>
                        <p class="stat-desc">Targeted in {{ $month->format('F') }}</p>
                    </article>
                    <article class="card stat-card" style="border-bottom: 3px solid #10b981;">
                        <p class="stat-label">Completion Rate</p>
                        <p class="stat-value" style="color: #10b981;">{{ $kpiCompletionRate }}%</p>
                        <div style="height: 6px; background: var(--border-color); border-radius: 3px; overflow: hidden; margin-top: 4px;">
                            <div style="height: 100%; width: {{ $kpiCompletionRate }}%; background: #10b981; border-radius: 3px;"></div>
                        </div>
                    </article>
                    <article class="card stat-card" style="border-bottom: 3px solid #f59e0b;">
                        <p class="stat-label">Remaining Tasks</p>
                        <p class="stat-value" style="color: #f59e0b;">{{ $kpiPending }}</p>
                        <p class="stat-desc">Remaining to complete</p>
                    </article>
                    <article class="card stat-card" style="border-bottom: 3px solid var(--text-secondary);">
                        <p class="stat-label">Category Mix</p>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4px; margin-top: 6px;">
                            <span style="font-size: 0.72rem; font-weight: 700; color: var(--text-secondary);">💼 Work: <strong style="color:var(--text-primary);">{{ $kpiWork }}</strong></span>
                            <span style="font-size: 0.72rem; font-weight: 700; color: var(--text-secondary);">🎓 Study: <strong style="color:var(--text-primary);">{{ $kpiStudy }}</strong></span>
                            <span style="font-size: 0.72rem; font-weight: 700; color: var(--text-secondary);">📝 Review: <strong style="color:var(--text-primary);">{{ $kpiReview }}</strong></span>
                            <span style="font-size: 0.72rem; font-weight: 700; color: var(--text-secondary);">🔄 Routine: <strong style="color:var(--text-primary);">{{ $kpiRoutine }}</strong></span>
                        </div>
                    </article>
                </div>

                <!-- Main Layout Grid -->
                <div class="monthly-layout-container">
                    
                    <!-- Left: Monthly Calendar -->
                    <div>
                        <!-- Category indicators legend -->
                        <div class="category-legend">
                            <div class="legend-item">
                                <span class="legend-dot" style="background-color: var(--accent-teal);"></span>
                                <span>Completed Task</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-dot" style="background-color: #f59e0b;"></span>
                                <span>Pending Task</span>
                            </div>
                        </div>

                        <div class="calendar-grid-header">
                            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
                                <div class="calendar-header-day">{{ $dayName }}</div>
                            @endforeach
                        </div>

                        <div class="calendar-days-grid">
                            @foreach($days as $index => $day)
                                @php
                                    $isToday = $day['date']->isToday();
                                    $dayTasks = $day['tasks'];
                                    $totalTasks = $dayTasks->count();
                                    $doneTasks = $dayTasks->where('is_done', true)->count();
                                    $pendingTasks = $totalTasks - $doneTasks;
                                @endphp
                                <div class="day-cell {{ !$day['isCurrentMonth'] ? 'muted' : '' }} {{ $isToday ? 'today-cell' : '' }}" 
                                     data-index="{{ $index }}"
                                     data-date="{{ $day['date']->toDateString() }}"
                                     onclick="selectDay({{ $index }})">
                                    
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span class="day-number">{{ $day['date']->format('j') }}</span>
                                        @if($isToday)
                                            <span style="font-size:0.6rem; font-weight:700; color:var(--accent-teal); text-transform:uppercase;">Today</span>
                                        @endif
                                    </div>

                                    @if($totalTasks > 0)
                                        <div class="day-task-indicator">
                                            <!-- Visual status badge -->
                                            @if($doneTasks === $totalTasks)
                                                <span class="day-status-pill day-status-done">✓ Done</span>
                                            @elseif($doneTasks > 0)
                                                <span class="day-status-pill day-status-partial">{{ $doneTasks }}/{{ $totalTasks }} Done</span>
                                            @else
                                                <span class="day-status-pill day-status-pending">{{ $totalTasks }} Tasks</span>
                                            @endif

                                            <!-- Visual dots breakdown -->
                                            <div class="progress-dots">
                                                @foreach($dayTasks as $task)
                                                    <span class="progress-dot {{ $task->is_done ? 'done' : 'pending' }}" title="{{ $task->title }}"></span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Right: Goals & Active Day Drawer -->
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        
                        <!-- Monthly Goals Card -->
                        <div class="card" style="padding: 20px;">
                            <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 12px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <h3 class="panel-title" style="font-size: 1.15rem; font-weight:700;">Monthly Goals</h3>
                                    <p class="panel-subtitle" style="font-size: 0.8rem; color: var(--text-secondary); margin-top:2px;">Targets for {{ $month->format('F Y') }}</p>
                                </div>
                            </div>

                            <!-- Goals List -->
                            <div id="monthly-goals-list" class="task-list" style="margin-bottom: 16px;">
                                @forelse($monthlyGoals as $goal)
                                    <div class="task-item {{ $goal->is_completed ? 'done' : '' }}" style="display: flex; align-items: center; justify-content: space-between; padding: 8px 0; border-bottom: none;">
                                        <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                            <div class="checkbox goal-checkbox {{ $goal->is_completed ? 'checked' : '' }}" data-id="{{ $goal->id }}"></div>
                                            <span class="task-title" style="font-weight: 500; font-size: 0.95rem; color: var(--text-primary);">{{ $goal->title }}</span>
                                        </div>
                                        <button class="btn btn-ghost delete-goal-btn" data-id="{{ $goal->id }}" style="padding: 0 4px; min-height: unset; color: #df3b3b; font-size: 1.25rem; border: none; background: transparent; cursor: pointer;">×</button>
                                    </div>
                                @empty
                                    <p style="font-size: 0.85rem; color: var(--text-secondary); text-align: center; padding: 8px 0;">No goals set for this month yet.</p>
                                @endforelse
                            </div>

                            <!-- Add Goal Form -->
                            <form id="add-goal-form" style="display: flex; gap: 8px; border-top: 1px solid var(--border-color); padding-top: 16px;">
                                <input type="hidden" name="month" value="{{ $month->format('Y-m') }}">
                                <input type="text" name="title" class="input" placeholder="New monthly goal..." required style="flex: 1; height: 36px; padding: 0 12px; font-size: 0.85rem;">
                                <button type="submit" class="btn btn-primary" style="height: 36px; min-height: unset; padding: 0 16px; font-size: 0.85rem; display: flex; align-items: center;">Add</button>
                            </form>
                        </div>

                        <!-- Active Day Drawer -->
                        <div class="card detail-panel-card" id="detail-panel" style="margin-top: 0; position: static;">
                            <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 16px; margin-bottom: 20px;">
                                <h3 class="panel-title" id="active-day-heading" style="font-size:1.25rem;">Active Day Tasks</h3>
                                <p class="panel-subtitle" id="active-day-subtitle" style="font-size:0.85rem; margin-top:4px;">Select any date from the monthly grid to manage tasks.</p>
                            </div>

                            <!-- Active Day Tasks List -->
                            <div id="active-tasks-container" class="task-list" style="margin-bottom: 24px;">
                                <!-- Dynamically filled by Javascript -->
                                <p style="font-size:0.85rem; color:var(--text-secondary); text-align:center; padding:16px;">Click on a date to load its task list.</p>
                            </div>

                            <!-- Quick Task Add Form for the Active Day -->
                            <div id="quick-add-form-container" style="display: none; border-top: 1px solid var(--border-color); padding-top: 20px;">
                                <h4 style="font-size:0.9rem; font-weight:700; margin-bottom:12px; text-transform:uppercase; color:#8c8c88; letter-spacing:0.05em;">Add Task to this Day</h4>
                                <form id="active-day-add-form">
                                    <input type="hidden" name="task_date" id="active-day-input-date">
                                    
                                    <div class="form-group">
                                        <label>Task Title</label>
                                        <input type="text" name="title" class="input" placeholder="What needs to be done?" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Assign to Block</label>
                                        <select name="time_block_id" class="select" required>
                                            @foreach($blocks as $block)
                                                <option value="{{ $block->id }}">{{ $block->title }} ({{ \Carbon\Carbon::parse($block->starts_at)->format('H:i') }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Category</label>
                                        <select name="category" class="select" required>
                                            <option value="work">Work</option>
                                            <option value="study">Study</option>
                                            <option value="review">Review</option>
                                            <option value="routine">Routine</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Review Note (Optional)</label>
                                        <textarea name="review_note" class="input" rows="2" placeholder="Task details or review notes..."></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 8px;">Create Task</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <!-- Modals -->
    <!-- Custom Confirm Delete Modal -->
    <div id="confirm-modal" class="modal">
        <div class="card" style="text-align: center;">
            <h3 class="panel-title">Confirm Delete</h3>
            <p class="muted tiny" id="confirm-modal-message" style="margin: 16px 0; color: var(--text-secondary);">This action is permanent.</p>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <button type="button" id="confirm-cancel-btn" class="btn btn-secondary">Cancel</button>
                <button type="button" id="confirm-delete-btn" class="btn btn-primary" style="background:#df3b3b; border-color:#df3b3b; box-shadow:none;">Delete</button>
            </div>
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

    <!-- Data Injection & Local Binding Scripts -->
    <script>
        // Inject the complete calendar days data structures directly from Laravel
        const monthlyDaysData = @json($days);
    </script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        // Shared script logic for Monthly Tasks Dashboard
        const getCsrfTokenLocal = () => document.querySelector('meta[name="csrf-token"]')?.content;
        
        let selectedDayIndex = null;

        // Custom Confirmation dialog helper
        let confirmDeletionCallbackLocal = null;
        const confirmLocalModal = document.getElementById('confirm-modal');
        const confirmLocalMessage = document.getElementById('confirm-modal-message');
        const confirmLocalCancelBtn = document.getElementById('confirm-cancel-btn');
        const confirmLocalDeleteBtn = document.getElementById('confirm-delete-btn');

        const showLocalModal = (modalId) => {
            const modal = document.getElementById(modalId);
            if (modal) modal.style.display = 'flex';
        };

        const hideLocalModal = (modalId) => {
            const modal = document.getElementById(modalId);
            if (modal) modal.style.display = 'none';
        };

        const askConfirmationLocal = (message, onConfirm) => {
            if (confirmLocalMessage) confirmLocalMessage.textContent = message;
            confirmDeletionCallbackLocal = onConfirm;
            showLocalModal('confirm-modal');
        };

        if (confirmLocalCancelBtn) {
            confirmLocalCancelBtn.addEventListener('click', () => {
                hideLocalModal('confirm-modal');
                confirmDeletionCallbackLocal = null;
            });
        }

        if (confirmLocalDeleteBtn) {
            confirmLocalDeleteBtn.addEventListener('click', async () => {
                if (confirmDeletionCallbackLocal) {
                    await confirmDeletionCallbackLocal();
                    hideLocalModal('confirm-modal');
                    confirmDeletionCallbackLocal = null;
                }
            });
        }

        // Action when a cell is clicked
        function selectDay(index) {
            selectedDayIndex = index;
            const dayData = monthlyDaysData[index];
            
            // Format the date to YYYY-MM-DD format (extracting from ISO-8601 string)
            const dateString = typeof dayData.date === 'string' ? dayData.date.split('T')[0] : dayData.date;
            
            const dateObj = new Date(dateString);
            const formattedDate = dateObj.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

            // Store active date in sessionStorage to preserve selection on page refresh
            sessionStorage.setItem('monthly_selected_date', dateString);

            // Highlight the clicked day cell
            document.querySelectorAll('.day-cell').forEach(cell => {
                cell.classList.remove('active-day');
            });
            const cellEl = document.querySelector(`.day-cell[data-index="${index}"]`);
            if (cellEl) cellEl.classList.add('active-day');

            // Update heading and inputs
            document.getElementById('active-day-heading').textContent = `Tasks: ${formattedDate}`;
            document.getElementById('active-day-subtitle').textContent = `Manage workload and quick capture for this day.`;
            document.getElementById('active-day-input-date').value = dateString;

            // Render tasks
            const container = document.getElementById('active-tasks-container');
            container.innerHTML = '';

            const tasks = dayData.tasks;
            if (tasks.length === 0) {
                container.innerHTML = `<p style="font-size:0.85rem; color:var(--text-secondary); text-align:center; padding:24px;">No tasks scheduled for this day.</p>`;
            } else {
                tasks.forEach(task => {
                    const blockTitle = task.time_block ? task.time_block.title : 'No Block';
                    const categoryUpper = task.category ? task.category.toUpperCase() : 'TASK';
                    
                    let categoryColorClass = 'chip-routine';
                    if (task.category === 'work') categoryColorClass = 'chip-work';
                    else if (task.category === 'study') categoryColorClass = 'chip-study';
                    else if (task.category === 'review') categoryColorClass = 'chip-review';

                    const noteHtml = task.review_note ? `<p class="task-desc" style="font-size:0.75rem; color:var(--text-secondary); margin-top:2px;">📝 ${task.review_note}</p>` : '';

                    const taskItem = document.createElement('div');
                    taskItem.className = `task-item ${task.is_done ? 'done' : ''}`;
                    taskItem.style.display = 'flex';
                    taskItem.style.alignItems = 'center';
                    taskItem.style.gap = '12px';
                    taskItem.innerHTML = `
                        <div class="checkbox local-task-checkbox ${task.is_done ? 'checked' : ''}" data-id="${task.id}"></div>
                        <div style="flex:1;">
                            <span class="task-title" style="font-weight:600; font-size:0.95rem; color:var(--text-primary);">${task.title}</span>
                            ${noteHtml}
                            <div style="display:flex; align-items:center; gap:8px; margin-top:4px;">
                                <span class="chip ${categoryColorClass}" style="font-size:0.6rem; font-weight:700; text-transform:uppercase; padding:1px 6px; border-radius:4px;">${categoryUpper}</span>
                                <span style="font-size:0.72rem; color:var(--text-secondary);">${blockTitle}</span>
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <button class="btn btn-ghost edit-local-task-btn" 
                                    data-id="${task.id}" 
                                    data-title="${task.title}" 
                                    data-block-id="${task.time_block_id}" 
                                    data-review-note="${task.review_note || ''}" 
                                    style="padding: 4px; min-height: unset; color:var(--text-secondary);">
                                <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </button>
                            <button class="btn btn-secondary local-carry-btn" data-id="${task.id}" style="min-height: unset; height: 26px; padding: 0 8px; font-size: 0.7rem; border-radius: 6px;">Carry</button>
                        </div>
                    `;
                    container.appendChild(taskItem);
                });

                // Attach checkbox click handlers
                container.querySelectorAll('.local-task-checkbox').forEach(cb => {
                    cb.addEventListener('click', async () => {
                        const id = cb.dataset.id;
                        cb.classList.toggle('checked');
                        cb.closest('.task-item')?.classList.toggle('done');
                        try {
                            const res = await fetch(`/api/tasks/${id}/toggle`, {
                                method: 'PATCH',
                                headers: { 'X-CSRF-TOKEN': getCsrfTokenLocal() }
                            });
                            if (res.ok) {
                                // Update injected data locally to prevent reset on toggle
                                const indexLocal = monthlyDaysData[selectedDayIndex].tasks.findIndex(t => t.id == id);
                                if (indexLocal !== -1) {
                                    monthlyDaysData[selectedDayIndex].tasks[indexLocal].is_done = !monthlyDaysData[selectedDayIndex].tasks[indexLocal].is_done;
                                }
                                // Update indicators
                                updateCellIndicators(selectedDayIndex);
                            }
                        } catch (err) {
                            console.error(err);
                            cb.classList.toggle('checked');
                            cb.closest('.task-item')?.classList.toggle('done');
                        }
                    });
                });

                // Attach carry-over handlers
                container.querySelectorAll('.local-carry-btn').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        const id = btn.dataset.id;
                        try {
                            const res = await fetch(`/api/tasks/${id}/carry-over`, {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': getCsrfTokenLocal() }
                            });
                            if (res.ok) {
                                window.location.reload();
                            }
                        } catch (err) {
                            console.error(err);
                        }
                    });
                });

                // Attach edit task button handlers
                container.querySelectorAll('.edit-local-task-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const idInput = document.getElementById('edit-task-id');
                        const titleInput = document.getElementById('edit-task-title');
                        const blockInput = document.getElementById('edit-task-block-id');
                        const noteInput = document.getElementById('edit-task-review-note');
                        
                        if (idInput) idInput.value = btn.dataset.id;
                        if (titleInput) titleInput.value = btn.dataset.title;
                        if (blockInput) blockInput.value = btn.dataset.blockId;
                        if (noteInput) noteInput.value = btn.dataset.reviewNote || '';
                        
                        showLocalModal('task-modal');
                    });
                });
            }

            // Show Quick Add form container
            document.getElementById('quick-add-form-container').style.display = 'block';
        }

        // Task edit form submission handler
        const editTaskLocalForm = document.getElementById('edit-task-form');
        const deleteTaskLocalBtn = document.getElementById('delete-task-btn');
        const closeTaskLocalModal = document.getElementById('close-task-modal');

        if (closeTaskLocalModal) {
            closeTaskLocalModal.addEventListener('click', () => hideLocalModal('task-modal'));
        }

        if (editTaskLocalForm) {
            editTaskLocalForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const id = document.getElementById('edit-task-id')?.value;
                if (id) {
                    const data = Object.fromEntries(new FormData(editTaskLocalForm).entries());
                    await fetch(`/api/tasks/${id}`, {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfTokenLocal() },
                        body: JSON.stringify(data)
                    });
                    window.location.reload();
                }
            });
        }

        if (deleteTaskLocalBtn) {
            deleteTaskLocalBtn.addEventListener('click', () => {
                const id = document.getElementById('edit-task-id')?.value;
                if (id) {
                    askConfirmationLocal('Delete this task?', async () => {
                        await fetch(`/api/tasks/${id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': getCsrfTokenLocal() }
                        });
                        window.location.reload();
                    });
                }
            });
        }

        // Dynamically update calendar day cell headers (e.g. status pills and dots) when checkbox clicked
        function updateCellIndicators(index) {
            const dayData = monthlyDaysData[index];
            const total = dayData.tasks.length;
            const done = dayData.tasks.filter(t => t.is_done).length;
            
            const cellEl = document.querySelector(`.day-cell[data-index="${index}"]`);
            if (!cellEl) return;

            const pillEl = cellEl.querySelector('.day-status-pill');
            if (pillEl) {
                pillEl.className = 'day-status-pill';
                if (done === total) {
                    pillEl.classList.add('day-status-done');
                    pillEl.textContent = '✓ Done';
                } else if (done > 0) {
                    pillEl.classList.add('day-status-partial');
                    pillEl.textContent = `${done}/${total} Done`;
                } else {
                    pillEl.classList.add('day-status-pending');
                    pillEl.textContent = `${total} Tasks`;
                }
            }

            // Update dots
            const dotsContainer = cellEl.querySelector('.progress-dots');
            if (dotsContainer) {
                dotsContainer.innerHTML = '';
                dayData.tasks.forEach(task => {
                    const dot = document.createElement('span');
                    dot.className = `progress-dot ${task.is_done ? 'done' : 'pending'}`;
                    dotsContainer.appendChild(dot);
                });
            }
        }

        // Handle Quick Add Task Submission
        const activeAddForm = document.getElementById('active-day-add-form');
        if (activeAddForm) {
            activeAddForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const data = Object.fromEntries(new FormData(activeAddForm).entries());
                try {
                    const res = await fetch('/api/tasks', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfTokenLocal() },
                        body: JSON.stringify(data)
                    });
                    if (res.ok) {
                        // Task created successfully, refresh view
                        window.location.reload();
                    }
                } catch (err) {
                    console.error('Failed to create task:', err);
                }
            });
        }

        // Goal completion toggle
        document.querySelectorAll('.goal-checkbox').forEach(cb => {
            cb.addEventListener('click', async () => {
                const id = cb.dataset.id;
                cb.classList.toggle('checked');
                cb.closest('.task-item')?.classList.toggle('done');
                try {
                    await fetch(`/api/monthly-goals/${id}/toggle`, {
                        method: 'PATCH',
                        headers: { 'X-CSRF-TOKEN': getCsrfTokenLocal() }
                    });
                } catch (err) {
                    console.error(err);
                    cb.classList.toggle('checked');
                    cb.closest('.task-item')?.classList.toggle('done');
                }
            });
        });

        // Delete Goal
        document.querySelectorAll('.delete-goal-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                askConfirmationLocal('Delete this goal?', async () => {
                    try {
                        const res = await fetch(`/api/monthly-goals/${id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': getCsrfTokenLocal() }
                        });
                        if (res.ok) {
                            window.location.reload();
                        }
                    } catch (err) {
                        console.error(err);
                    }
                });
            });
        });

        // Add Goal Submission
        const addGoalForm = document.getElementById('add-goal-form');
        if (addGoalForm) {
            addGoalForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const data = Object.fromEntries(new FormData(addGoalForm).entries());
                try {
                    const res = await fetch('/api/monthly-goals', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfTokenLocal() },
                        body: JSON.stringify(data)
                    });
                    if (res.ok) {
                        window.location.reload();
                    }
                } catch (err) {
                    console.error('Failed to create goal:', err);
                }
            });
        }

        // Restore selected date from sessionStorage on page load
        document.addEventListener('DOMContentLoaded', () => {
            const savedDate = sessionStorage.getItem('monthly_selected_date');
            if (savedDate) {
                const cellEl = document.querySelector(`.day-cell[data-date="${savedDate}"]`);
                if (cellEl) {
                    const index = cellEl.dataset.index;
                    selectDay(parseInt(index));
                } else {
                    // Fallback to select first day or today
                    selectFirstCurrentMonthDay();
                }
            } else {
                selectFirstCurrentMonthDay();
            }
        });

        // Helper to select the current active day (today or the first of current month)
        function selectFirstCurrentMonthDay() {
            // Find today or the first cell of the current month
            let targetCell = null;
            document.querySelectorAll('.day-cell').forEach(cell => {
                if (cell.classList.contains('today-cell')) {
                    targetCell = cell;
                }
            });

            if (!targetCell) {
                document.querySelectorAll('.day-cell').forEach(cell => {
                    if (!cell.classList.contains('muted') && !targetCell) {
                        targetCell = cell;
                    }
                });
            }

            if (targetCell) {
                selectDay(parseInt(targetCell.dataset.index));
            }
        }
    </script>
</body>
</html>
