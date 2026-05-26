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
    <title>Voin - Calendar</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://api.fontshare.com">
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        /* Main application sidebar collapsing transition styles */
        .sidebar {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .main-content {
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .main-sidebar-collapsed .sidebar {
            transform: translateX(-100%);
        }

        .main-sidebar-collapsed .main-content {
            margin-left: 0 !important;
        }

        /* Google Calendar layout overrides and styles */
        .calendar-view-container {
            display: flex;
            gap: 24px;
            height: calc(100vh - var(--header-height) - 80px);
            overflow: hidden;
            width: 100%;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .left-filter-sidebar {
            width: 240px;
            flex-shrink: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
        }

        .main-week-grid {
            flex: 1;
            min-width: 0;
        }

        .right-tasks-sidebar {
            width: 280px;
            flex-shrink: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
        }

        .calendar-view-container.left-hidden .left-filter-sidebar {
            width: 0px;
            opacity: 0;
            visibility: hidden;
            margin-right: -24px; /* Offset flex gap */
            transform: translateX(-20px);
            pointer-events: none;
        }

        .calendar-view-container.right-hidden .right-tasks-sidebar {
            width: 0px;
            opacity: 0;
            visibility: hidden;
            margin-left: -24px; /* Offset flex gap */
            transform: translateX(20px);
            pointer-events: none;
        }

        @media (max-width: 1200px) {
            .right-tasks-sidebar {
                display: none !important;
            }
            #toggle-right-sidebar {
                display: none !important;
            }
        }

        @media (max-width: 900px) {
            .left-filter-sidebar {
                display: none !important;
            }
            #toggle-left-sidebar {
                display: none !important;
            }
        }

        /* Sidebars common style */
        .inner-sidebar {
            display: flex;
            flex-direction: column;
            gap: 24px;
            overflow-y: auto;
            padding-right: 4px;
        }

        /* Mini Monthly Calendar */
        .mini-calendar {
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            background: var(--surface-color);
            padding: 16px;
            box-shadow: var(--shadow-sm);
        }

        .mini-calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .mini-calendar-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .mini-calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
            text-align: center;
        }

        .mini-day-name {
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
        }

        .mini-day-cell {
            font-size: 0.75rem;
            font-weight: 500;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            cursor: pointer;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.1s ease;
        }

        .mini-day-cell:hover {
            background-color: var(--border-color);
        }

        .mini-day-cell.muted {
            color: var(--text-secondary);
            opacity: 0.4;
        }

        .mini-day-cell.selected {
            background-color: var(--accent-teal);
            color: #ffffff !important;
            font-weight: 700;
        }

        .mini-day-cell.today:not(.selected) {
            border: 1.5px solid var(--accent-teal);
            color: var(--accent-teal);
            font-weight: 700;
        }

        /* Calendar type checkboxes */
        .calendar-checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .calendar-checkbox-item {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .calendar-color-dot {
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }

        /* Main Hero Week Grid */
        .main-week-grid {
            display: flex;
            flex-direction: column;
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            height: 100%;
        }

        /* Week view header */
        .week-grid-header {
            display: grid;
            grid-template-columns: 60px repeat(7, 1fr);
            border-bottom: 1px solid var(--border-color);
            background: rgba(0, 0, 0, 0.005);
            z-index: 10;
        }

        .week-header-cell {
            padding: 12px 4px;
            text-align: center;
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .week-header-cell:last-child {
            border-right: none;
        }

        .week-day-name {
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .week-day-num {
            font-size: 1.1rem;
            font-weight: 700;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.15s ease;
        }

        .week-day-num.today {
            background-color: var(--accent-teal);
            color: #ffffff;
        }

        /* All Day Row */
        .all-day-row {
            display: grid;
            grid-template-columns: 60px repeat(7, 1fr);
            border-bottom: 2px solid var(--border-color);
            background: rgba(15, 118, 110, 0.01);
            min-height: 48px;
        }

        .all-day-label {
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 12px;
            border-right: 1px solid var(--border-color);
        }

        .all-day-content-cell {
            padding: 6px;
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-height: 48px;
            overflow-y: auto;
        }

        .all-day-content-cell:last-child {
            border-right: none;
        }

        /* Scrollable Time Grid */
        .time-grid-scrollable {
            flex: 1;
            overflow-y: auto;
            position: relative;
        }

        .time-grid-rows {
            display: grid;
            grid-template-columns: 60px 1fr;
            position: relative;
        }

        .time-labels-col {
            display: grid;
            grid-template-rows: repeat(16, 60px);
            border-right: 1px solid var(--border-color);
            background: rgba(0, 0, 0, 0.005);
        }

        .time-label {
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-align: right;
            padding-right: 12px;
            line-height: 20px;
            transform: translateY(-10px);
        }

        .grid-columns-container {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            position: relative;
        }

        .grid-column {
            border-right: 1px solid var(--border-color);
            position: relative;
            height: calc(16 * 60px); /* 16 hours */
        }

        .grid-column:last-child {
            border-right: none;
        }

        /* Horizontal Hour Dividers */
        .hour-divider-lines {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            display: grid;
            grid-template-rows: repeat(16, 60px);
        }

        .hour-line {
            border-bottom: 1px solid var(--border-color);
            width: 100%;
        }

        .hour-line:last-child {
            border-bottom: none;
        }

        /* Daily Time Blocks rendered on background */
        .routine-time-block {
            position: absolute;
            left: 4px;
            right: 4px;
            border-radius: 6px;
            padding: 6px 8px;
            font-size: 0.75rem;
            font-weight: 600;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 2px;
            border-left: 3px solid transparent;
            opacity: 0.35;
            transition: opacity 0.2s ease;
        }

        .routine-time-block:hover {
            opacity: 0.65;
        }

        /* Event Capsule / Card styles */
        .event-card {
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1.2;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .event-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        /* Modern Event Types Colors */
        .event-card.event-type-event {
            background-color: var(--accent-teal-soft);
            color: var(--accent-teal);
            border-left: 3px solid var(--accent-teal);
        }
        .event-card.event-type-deadline {
            background-color: rgba(223, 59, 59, 0.05);
            color: var(--color-error);
            border-left: 3px solid var(--color-error);
        }
        .event-card.event-type-reminder {
            background-color: rgba(245, 158, 11, 0.05);
            color: #d97706;
            border-left: 3px solid #f59e0b;
        }

        .event-card.timed-event {
            position: absolute;
            left: 6px;
            right: 6px;
            z-index: 10;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 2px;
            text-align: left;
            font-size: 0.72rem;
            padding: 6px 8px;
            box-shadow: var(--shadow-sm);
        }

        /* Standard Red Current Time Indicator */
        .current-time-indicator {
            position: absolute;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #ef4444;
            z-index: 5;
            pointer-events: none;
        }

        .current-time-indicator::before {
            content: '';
            position: absolute;
            left: -4px;
            top: -4px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #ef4444;
        }

        /* Task Sidebar Styles */
        .sidebar-task-card {
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            background: var(--surface-color);
            padding: 20px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            gap: 16px;
            height: 100%;
        }

        .sidebar-task-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-task-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            overflow-y: auto;
            flex: 1;
        }

        .sidebar-task-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 8px;
            background-color: rgba(0, 0, 0, 0.005);
            border: 1px solid var(--border-color);
            transition: all 0.2s ease;
        }

        .sidebar-task-item:hover {
            background-color: rgba(0, 0, 0, 0.015);
            border-color: var(--text-secondary);
        }

        .sidebar-task-checkbox {
            width: 18px;
            height: 18px;
            border: 2px solid var(--border-color);
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.15s ease;
        }

        .sidebar-task-checkbox:hover {
            border-color: var(--accent-teal);
        }

        .sidebar-task-text {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-primary);
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-task-meta {
            font-size: 0.7rem;
            color: var(--text-secondary);
            padding: 2px 6px;
            border-radius: 4px;
            background-color: var(--border-color);
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

                    <!-- Toggle left sidebar button -->
                    <button class="btn btn-secondary" id="toggle-left-sidebar" title="Toggle left sidebar" style="min-height: unset; height: 32px; width: 32px; padding: 0; border-radius: 8px; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; background: var(--surface-color);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
                    </button>
                    <h1>Calendar</h1>
                    
                    <!-- Week view navigation -->
                    <div style="display: flex; align-items: center; gap: 6px; background: rgba(0,0,0,0.02); padding: 4px; border-radius: 8px; border: 1px solid var(--border-color);">
                        <a href="?date={{ now()->toDateString() }}" class="btn btn-secondary" style="min-height: unset; height: 28px; padding: 0 10px; font-size: 0.75rem; border-radius: 6px;">Today</a>
                        <div style="display: flex; align-items: center; gap: 1px;">
                            <a href="?date={{ $selectedDate->copy()->subWeek()->toDateString() }}&month={{ $month->format('Y-m') }}" class="btn btn-secondary" style="min-height: unset; height: 28px; width: 28px; padding: 0; border-radius: 6px;" title="Previous week">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg>
                            </a>
                            <a href="?date={{ $selectedDate->copy()->addWeek()->toDateString() }}&month={{ $month->format('Y-m') }}" class="btn btn-secondary" style="min-height: unset; height: 28px; width: 28px; padding: 0; border-radius: 6px;" title="Next week">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6 6-6"/></svg>
                            </a>
                        </div>
                        <span style="font-size: 0.8rem; font-weight: 700; padding: 0 8px; color: var(--text-primary);">
                            {{ $weekDays[0]->format('M d') }} – {{ $weekDays[6]->format('M d, Y') }}
                        </span>
                    </div>
                </div>

                <div class="header-actions" style="display: flex; gap: 12px; align-items: center;">
                    <!-- Toggle right sidebar button -->
                    <button class="btn btn-secondary" id="toggle-right-sidebar" title="Toggle tasks panel" style="min-height: unset; height: 38px; width: 38px; padding: 0; border-radius: 50%; border: 1px solid var(--border-color); display: grid; place-items: center; background: var(--surface-color);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="15" y1="3" x2="15" y2="21"/></svg>
                    </button>

                    <!-- Create Event trigger button -->
                    <button class="btn btn-primary" onclick="openEventModal('{{ $selectedDate->toDateString() }}')" style="min-height: unset; height: 38px; padding: 0 16px; font-size: 0.8rem; border-radius: 8px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 4px;"><path d="M12 5v14M5 12h14"/></svg>
                        Create Event
                    </button>

                    <!-- Standard Theme Toggle -->
                    <button class="theme-toggle" id="theme-toggle" aria-label="Switch theme" style="width:38px; height:38px; border-radius:50%; background:var(--surface-color); border:1px solid var(--border-color); display:grid; place-items:center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    </button>
                </div>
            </header>

            <div class="dashboard-viewport">
                <!-- 3-Column Google Calendar layout -->
                <div class="calendar-view-container">
                    
                    <!-- COLUMN 1: LEFT INNER SIDEBAR (Mini Calendar & Filters) -->
                    <div class="inner-sidebar left-filter-sidebar">
                        
                        <!-- Mini Monthly Calendar -->
                        <div class="mini-calendar">
                            <div class="mini-calendar-header">
                                <span class="mini-calendar-title">{{ $month->format('F Y') }}</span>
                                <div style="display: flex; gap: 2px;">
                                    <a href="?date={{ $selectedDate->toDateString() }}&month={{ $month->copy()->subMonth()->format('Y-m') }}" class="btn btn-ghost" style="min-height: unset; height: 20px; width: 20px; padding: 0; border-radius: 4px;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg>
                                    </a>
                                    <a href="?date={{ $selectedDate->toDateString() }}&month={{ $month->copy()->addMonth()->format('Y-m') }}" class="btn btn-ghost" style="min-height: unset; height: 20px; width: 20px; padding: 0; border-radius: 4px;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
                                    </a>
                                </div>
                            </div>
                            <div class="mini-calendar-grid">
                                <!-- Day Names -->
                                @foreach(['S', 'M', 'T', 'W', 'T', 'F', 'S'] as $dayLetter)
                                    <span class="mini-day-name">{{ $dayLetter }}</span>
                                @endforeach

                                <!-- Day Cells -->
                                @foreach($days as $day)
                                    @php
                                        $isCellToday = $day['date']->isToday();
                                        $isCellSelected = $day['date']->toDateString() === $selectedDate->toDateString();
                                        $isCellCurrentMonth = $day['isCurrentMonth'];
                                    @endphp
                                    <a href="?date={{ $day['date']->toDateString() }}&month={{ $month->format('Y-m') }}" 
                                       class="mini-day-cell {{ !$isCellCurrentMonth ? 'muted' : '' }} {{ $isCellSelected ? 'selected' : '' }} {{ $isCellToday ? 'today' : '' }}">
                                        {{ $day['date']->format('j') }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Calendar Filters (Checkboxes) -->
                        <div class="card" style="padding: 20px;">
                            <h3 class="panel-title" style="margin-bottom: 14px; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary);">Calendars</h3>
                            <div class="calendar-checkbox-group">
                                <label class="calendar-checkbox-item">
                                    <input type="checkbox" id="filter-events" checked onchange="toggleFilter('event')" style="accent-color: var(--accent-teal); cursor: pointer;">
                                    <span class="calendar-color-dot" style="background-color: var(--accent-teal);"></span>
                                    <span>Events</span>
                                </label>
                                <label class="calendar-checkbox-item">
                                    <input type="checkbox" id="filter-deadlines" checked onchange="toggleFilter('deadline')" style="accent-color: var(--color-error); cursor: pointer;">
                                    <span class="calendar-color-dot" style="background-color: var(--color-error);"></span>
                                    <span>Deadlines</span>
                                </label>
                                <label class="calendar-checkbox-item">
                                    <input type="checkbox" id="filter-reminders" checked onchange="toggleFilter('reminder')" style="accent-color: #f59e0b; cursor: pointer;">
                                    <span class="calendar-color-dot" style="background-color: #f59e0b;"></span>
                                    <span>Reminders</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- COLUMN 2: CENTER HERO WEEK GRID -->
                    <div class="main-week-grid">
                        
                        <!-- X-Axis Day Headers Row -->
                        <div class="week-grid-header">
                            <div class="week-header-cell" style="border-right: 1px solid var(--border-color);">
                                <!-- Spacer for hour column -->
                            </div>
                            @foreach($weekDays as $day)
                                @php
                                    $isToday = $day->isToday();
                                @endphp
                                <div class="week-header-cell">
                                    <span class="week-day-name">{{ $day->format('D') }}</span>
                                    <span class="week-day-num {{ $isToday ? 'today' : '' }}">
                                        {{ $day->format('d') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <!-- All-Day Events row -->
                        <div class="all-day-row">
                            <div class="all-day-label">All day</div>
                            @foreach($weekDays as $day)
                                @php
                                    $dateStr = $day->toDateString();
                                    $dayEvents = $events->get($dateStr, []);
                                @endphp
                                <div class="all-day-content-cell" data-date="{{ $dateStr }}">
                                    @foreach($dayEvents as $event)
                                        @if(is_null($event->start_time))
                                            <div class="event-card event-type-{{ $event->type ?? 'event' }}" 
                                                 data-event-type="{{ $event->type ?? 'event' }}"
                                                 onclick="editEvent({{ $event->id }}, '{{ addslashes($event->title) }}', '{{ $event->event_date->format('Y-m-d') }}', '{{ $event->type }}', '{{ addslashes($event->description) }}', '{{ $event->start_time ? substr($event->start_time, 0, 5) : '' }}', '{{ $event->end_time ? substr($event->end_time, 0, 5) : '' }}')">
                                                <div style="font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                    {{ $event->title }}
                                                </div>
                                                @if($event->description)
                                                    <div style="font-size: 0.65rem; opacity: 0.8; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                        {{ $event->description }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        </div>

                        <!-- Scrollable Hourly Grid Area -->
                        <div class="time-grid-scrollable">
                            
                            <!-- Current Time Indicator Line (absolutely positioned) -->
                            <div id="time-indicator" class="current-time-indicator" style="display: none;"></div>

                            <div class="time-grid-rows">
                                
                                <!-- Hours Axis Column -->
                                <div class="time-labels-col">
                                    @for($hour = 7; $hour <= 22; $hour++)
                                        <div class="time-label">
                                            {{ $hour > 12 ? ($hour - 12) . ':00 PM' : ($hour === 12 ? '12:00 PM' : $hour . ':00 AM') }}
                                        </div>
                                    @endfor
                                </div>

                                <!-- Columns Area for background time blocks & hover events -->
                                <div class="grid-columns-container">
                                    <!-- Horizontal grid lines inside the background -->
                                    <div class="hour-divider-lines">
                                        @for($hour = 7; $hour <= 22; $hour++)
                                            <div class="hour-line"></div>
                                        @endfor
                                    </div>

                                    @foreach($weekDays as $dayIndex => $day)
                                        @php
                                            $dateStr = $day->toDateString();
                                            $dayEvents = $events->get($dateStr, []);
                                        @endphp
                                        <div class="grid-column" data-day-index="{{ $dayIndex }}" data-date="{{ $dateStr }}" onclick="handleGridClick(event, this)">

                                            <!-- Render Timed Events -->
                                            @foreach($dayEvents as $event)
                                                @if(!is_null($event->start_time))
                                                    @php
                                                        $eventStart = \Carbon\Carbon::parse($event->start_time);
                                                        $eventEnd = $event->end_time ? \Carbon\Carbon::parse($event->end_time) : $eventStart->copy()->addHour();
                                                        
                                                        $startMins = ($eventStart->hour * 60) + $eventStart->minute;
                                                        $endMins = ($eventEnd->hour * 60) + $eventEnd->minute;
                                                        
                                                        $gridStartMins = 7 * 60; // Grid starts at 7:00 AM
                                                        $gridEndMins = 23 * 60;  // Grid ends at 11:00 PM
                                                        
                                                        if ($startMins >= $gridStartMins && $endMins <= $gridEndMins) {
                                                            $topPercent = (($startMins - $gridStartMins) / ($gridEndMins - $gridStartMins)) * 100;
                                                            $heightPercent = (($endMins - $startMins) / ($gridEndMins - $gridStartMins)) * 100;
                                                        } else {
                                                            $topPercent = -1; // Out of bounds
                                                        }
                                                    @endphp
                                                    
                                                    @if($topPercent >= 0)
                                                        <div class="event-card event-type-{{ $event->type ?? 'event' }} timed-event"
                                                             style="top: {{ $topPercent }}%; height: {{ $heightPercent }}%;"
                                                             data-event-type="{{ $event->type ?? 'event' }}"
                                                             onclick="event.stopPropagation(); editEvent({{ $event->id }}, '{{ addslashes($event->title) }}', '{{ $event->event_date->format('Y-m-d') }}', '{{ $event->type }}', '{{ addslashes($event->description) }}', '{{ substr($event->start_time, 0, 5) }}', '{{ $event->end_time ? substr($event->end_time, 0, 5) : '' }}')">
                                                            <div style="font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                                {{ $event->title }}
                                                            </div>
                                                            <div style="font-size: 0.65rem; opacity: 0.9; margin-bottom: 2px;">
                                                                {{ $eventStart->format('g:i A') }} - {{ $eventEnd->format('g:i A') }}
                                                            </div>
                                                            @if($event->description)
                                                                <div class="event-desc-preview" style="font-size: 0.65rem; opacity: 0.7; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                                    {{ $event->description }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- COLUMN 3: RIGHT TASKS SIDEBAR (Google Calendar style sidebar) -->
                    <div class="inner-sidebar right-tasks-sidebar">
                        <div class="sidebar-task-card">
                            <div class="sidebar-task-title">
                                <span>Pending Tasks</span>
                                <span style="font-size: 0.75rem; font-weight: 600; color: var(--accent-teal); background-color: var(--accent-teal-soft); padding: 2px 8px; border-radius: 9999px;">
                                    {{ $tasks->count() }} active
                                </span>
                            </div>

                            <!-- Fast quick-task form -->
                            <form id="sidebar-quick-task-form" onsubmit="saveQuickTask(event)" style="display: flex; gap: 8px;">
                                <input type="text" id="quick-task-title" placeholder="Add a task..." required class="input" style="padding: 6px 12px; font-size: 0.8rem; border-radius: 8px; flex: 1;">
                                <button type="submit" class="btn btn-primary" style="min-height: unset; height: 34px; padding: 0 12px; font-size: 0.75rem; border-radius: 8px;">+</button>
                            </form>

                            <div class="sidebar-task-list">
                                @forelse($tasks as $task)
                                    <div class="sidebar-task-item" id="task-item-{{ $task->id }}">
                                        <div class="sidebar-task-checkbox" onclick="toggleTask({{ $task->id }})" title="Complete task"></div>
                                        <div class="sidebar-task-text" title="{{ $task->title }}">{{ $task->title }}</div>
                                        @if($task->timeBlock)
                                            <span class="sidebar-task-meta">{{ $task->timeBlock->title }}</span>
                                        @endif
                                    </div>
                                @empty
                                    <div style="text-align: center; padding: 32px 16px; color: var(--text-secondary); font-size: 0.8rem;">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto 8px; opacity: 0.4;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                        All caught up!
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <!-- Event Modal (Add/Edit) -->
    <div id="event-modal" class="modal">
        <div class="card">
            <div class="panel-header" style="border-bottom: none; padding-bottom: 0;">
                <h3 class="panel-title" id="modal-title">Add Event</h3>
                <button onclick="closeEventModal()" class="btn btn-ghost" style="padding: 4px; min-height: unset; font-size: 1.5rem;">×</button>
            </div>
            
            <form id="event-form" onsubmit="saveEvent(event)" style="margin-top: 16px;">
                <input type="hidden" id="event-id">

                <div class="form-group">
                    <label>Title</label>
                    <input type="text" id="event-title" required class="input" placeholder="e.g., Team Sync or Dentist">
                </div>

                <div class="form-group" style="display: flex; gap: 12px;">
                    <div style="flex: 1;">
                        <label>Date</label>
                        <input type="date" id="event-date" required class="input">
                    </div>
                    <div style="flex: 1;">
                        <label>Type</label>
                        <select id="event-type" class="input">
                            <option value="event">Event</option>
                            <option value="deadline">Deadline</option>
                            <option value="reminder">Reminder</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="display: flex; gap: 12px;">
                    <div style="flex: 1;">
                        <label>Start Time <span style="font-size: 0.75rem; color: var(--text-secondary);">(Optional)</span></label>
                        <input type="time" id="event-start-time" class="input">
                    </div>
                    <div style="flex: 1;">
                        <label>End Time <span style="font-size: 0.75rem; color: var(--text-secondary);">(Optional)</span></label>
                        <input type="time" id="event-end-time" class="input">
                    </div>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea id="event-desc" rows="3" class="input" style="resize: none;" placeholder="Description or notes (optional)"></textarea>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px;">
                    <button type="button" id="btn-delete-event" onclick="deleteEvent()" class="btn" style="color: var(--color-error); background: transparent; border: none; display: none;">Delete</button>
                    <div style="display: flex; gap: 8px; margin-left: auto;">
                        <button type="button" onclick="closeEventModal()" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btn-save-event">Save Event</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Choice Modal -->
    <div id="choice-modal" class="modal">
        <div class="card" style="max-width: 340px; width: 90%; text-align: center; padding: 28px;">
            <h3 class="panel-title" id="choice-modal-title" style="margin-bottom: 8px;">Create New Item</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 24px;" id="choice-modal-subtitle"></p>
            
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <button id="choice-btn-event" class="btn btn-primary" style="justify-content: center; width: 100%;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 8px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Create Event
                </button>
                <button id="choice-btn-task" class="btn btn-secondary" style="justify-content: center; width: 100%; border: 1px solid var(--border-color);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 8px;"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    Create Task
                </button>
                <button onclick="closeChoiceModal()" class="btn btn-ghost" style="justify-content: center; width: 100%; margin-top: 6px;">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Task Modal -->
    <div id="task-modal" class="modal">
        <div class="card">
            <div class="panel-header" style="border-bottom: none; padding-bottom: 0;">
                <h3 class="panel-title">Add Task</h3>
                <button onclick="closeTaskModal()" class="btn btn-ghost" style="padding: 4px; min-height: unset; font-size: 1.5rem;">×</button>
            </div>
            
            <form id="task-form" onsubmit="saveTask(event)" style="margin-top: 16px;">
                <div class="form-group">
                    <label>Task Title</label>
                    <input type="text" id="task-title" required class="input" placeholder="e.g., Complete math homework">
                </div>

                <div class="form-group" style="display: flex; gap: 12px;">
                    <div style="flex: 1;">
                        <label>Time Block</label>
                        <select id="task-time-block" class="input">
                            <option value="">No Block</option>
                            @foreach($blocks as $block)
                                <option value="{{ $block->id }}" data-start="{{ $block->starts_at }}" data-end="{{ $block->ends_at }}" data-type="{{ $block->block_type }}">
                                    {{ $block->title }} ({{ substr($block->starts_at, 0, 5) }} - {{ substr($block->ends_at, 0, 5) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label>Category</label>
                        <select id="task-category" class="input">
                            <option value="routine">Routine</option>
                            <option value="work">Work</option>
                            <option value="study">Study</option>
                            <option value="review">Review</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 24px;">
                    <button type="button" onclick="closeTaskModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-task">Save Task</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Modal triggers
        const modal = document.getElementById('event-modal');
        const form = document.getElementById('event-form');
        const modalTitle = document.getElementById('modal-title');
        const deleteBtn = document.getElementById('btn-delete-event');
        const saveBtn = document.getElementById('btn-save-event');
        
        // Choice & Task Modals triggers
        const choiceModal = document.getElementById('choice-modal');
        const choiceTitle = document.getElementById('choice-modal-title');
        const choiceSubtitle = document.getElementById('choice-modal-subtitle');
        const choiceBtnEvent = document.getElementById('choice-btn-event');
        const choiceBtnTask = document.getElementById('choice-btn-task');

        function openEventModal(date, startTime = '', endTime = '') {
            form.reset();
            document.getElementById('event-id').value = '';
            document.getElementById('event-date').value = date;
            document.getElementById('event-type').value = 'event';
            document.getElementById('event-start-time').value = startTime;
            document.getElementById('event-end-time').value = endTime;
            modalTitle.textContent = 'Add Event';
            deleteBtn.style.display = 'none';
            modal.style.display = 'flex';
        }

        function editEvent(id, title, date, type, desc, startTime = '', endTime = '') {
            document.getElementById('event-id').value = id;
            document.getElementById('event-title').value = title;
            document.getElementById('event-date').value = date;
            document.getElementById('event-type').value = type;
            document.getElementById('event-desc').value = desc;
            document.getElementById('event-start-time').value = startTime;
            document.getElementById('event-end-time').value = endTime;

            modalTitle.textContent = 'Edit Event';
            deleteBtn.style.display = 'inline-flex';
            modal.style.display = 'flex';
        }

        function closeEventModal() {
            modal.style.display = 'none';
        }
        
        function openTaskModal(startTimeStr) {
            document.getElementById('task-form').reset();
            const selectEl = document.getElementById('task-time-block');
            const categoryEl = document.getElementById('task-category');
            
            // Auto-select Time Block based on time clicked
            if (startTimeStr) {
                let matchedOption = null;
                for (let i = 0; i < selectEl.options.length; i++) {
                    const opt = selectEl.options[i];
                    const start = opt.getAttribute('data-start');
                    const end = opt.getAttribute('data-end');
                    if (start && end) {
                        // Check if startTimeStr falls between start and end
                        if (startTimeStr >= start && startTimeStr <= end) {
                            matchedOption = opt;
                            break;
                        }
                    }
                }
                if (matchedOption) {
                    selectEl.value = matchedOption.value;
                    categoryEl.value = matchedOption.getAttribute('data-type') || 'routine';
                }
            }
            
            // Setup listener to update category when block is changed manually
            selectEl.onchange = () => {
                const selectedOpt = selectEl.options[selectEl.selectedIndex];
                const blockType = selectedOpt.getAttribute('data-type');
                if (blockType) {
                    categoryEl.value = blockType;
                }
            };
            
            document.getElementById('task-modal').style.display = 'flex';
        }
        
        function closeTaskModal() {
            document.getElementById('task-modal').style.display = 'none';
        }

        function showAddChoiceModal(dateStr, startTimeStr, endTimeStr) {
            // Format date for subtitle, e.g. "Monday, May 22"
            const dateObj = new Date(dateStr + 'T00:00:00');
            const dateOptions = { weekday: 'long', month: 'short', day: 'numeric' };
            const formattedDate = dateObj.toLocaleDateString('en-US', dateOptions);
            
            // Format time, e.g. "08:30 AM"
            const [hours, minutes] = startTimeStr.split(':').map(Number);
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const displayHours = hours % 12 || 12;
            const displayMinutes = minutes.toString().padStart(2, '0');
            const formattedTime = `${displayHours}:${displayMinutes} ${ampm}`;
            
            choiceSubtitle.textContent = `at ${formattedTime} on ${formattedDate}`;
            
            choiceBtnEvent.onclick = () => {
                closeChoiceModal();
                openEventModal(dateStr, startTimeStr, endTimeStr);
            };
            
            choiceBtnTask.onclick = () => {
                closeChoiceModal();
                openTaskModal(startTimeStr);
            };
            
            choiceModal.style.display = 'flex';
        }

        function closeChoiceModal() {
            choiceModal.style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == modal) {
                closeEventModal();
            } else if (event.target == choiceModal) {
                closeChoiceModal();
            } else if (event.target == document.getElementById('task-modal')) {
                closeTaskModal();
            }
        }

        // Click handler for hourly column
        function handleGridClick(event, columnElement) {
            // Ignore if clicked on an event card or routine block
            if (event.target.closest('.event-card') || event.target.closest('.routine-time-block')) {
                return;
            }

            const rect = columnElement.getBoundingClientRect();
            const clickY = event.clientY - rect.top; // pixel offset from the top of the 960px column
            
            // Total height is 960px for 16 hours (7:00 AM to 11:00 PM)
            // Each hour is exactly 60px (960 / 16 = 60px)
            // Calculate the hour clicked
            const totalHours = 16;
            const startHour = 7;
            
            const hoursOffset = clickY / 60; // e.g. 2.5 means 2 hours and 30 minutes from startHour
            const clickedHourDecimal = startHour + hoursOffset;
            
            const clickedHour = Math.floor(clickedHourDecimal);
            // Round to nearest 30-minute block for a better user experience!
            const clickedMinutes = Math.floor((clickedHourDecimal - clickedHour) * 2) * 30;
            
            // Format hour and minute as HH:MM
            const formatTime = (h, m) => {
                const pad = (n) => n.toString().padStart(2, '0');
                return `${pad(h)}:${pad(m)}`;
            };
            
            const startTimeStr = formatTime(clickedHour, clickedMinutes);
            // End time is 1 hour later
            const endTimeStr = formatTime(clickedHour + 1, clickedMinutes);
            
            const dateStr = columnElement.getAttribute('data-date');
            
            showAddChoiceModal(dateStr, startTimeStr, endTimeStr);
        }

        // Save Event (AJAX call to /api/events instead of /events)
        async function saveEvent(e) {
            e.preventDefault();
            const id = document.getElementById('event-id').value;
            const payload = {
                id: id ? id : null,
                title: document.getElementById('event-title').value,
                event_date: document.getElementById('event-date').value,
                type: document.getElementById('event-type').value,
                description: document.getElementById('event-desc').value,
                start_time: document.getElementById('event-start-time').value || null,
                end_time: document.getElementById('event-end-time').value || null,
            };

            saveBtn.textContent = 'Saving...';
            saveBtn.disabled = true;

            try {
                const response = await fetch('/api/events', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Error saving event');
                    saveBtn.textContent = 'Save Event';
                    saveBtn.disabled = false;
                }
            } catch (err) {
                console.error(err);
                saveBtn.textContent = 'Save Event';
                saveBtn.disabled = false;
            }
        }

        // Delete Event (AJAX call to /api/events/{id} instead of /events/{id})
        async function deleteEvent() {
            const id = document.getElementById('event-id').value;
            if (!id) return;
            if (!confirm('Are you sure you want to delete this event?')) return;

            deleteBtn.textContent = 'Deleting...';
            deleteBtn.disabled = true;

            try {
                const response = await fetch(`/api/events/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Error deleting event');
                    deleteBtn.textContent = 'Delete';
                    deleteBtn.disabled = false;
                }
            } catch (err) {
                console.error(err);
                deleteBtn.textContent = 'Delete';
                deleteBtn.disabled = false;
            }
        }

        // Toggle Task completion directly from Right Sidebar
        async function toggleTask(taskId) {
            const taskItem = document.getElementById(`task-item-${taskId}`);
            const checkbox = taskItem.querySelector('.sidebar-task-checkbox');
            
            // Interactive UI transition
            checkbox.style.backgroundColor = 'var(--accent-teal)';
            checkbox.style.borderColor = 'var(--accent-teal)';
            taskItem.style.opacity = '0.5';
            taskItem.style.transform = 'scale(0.97)';

            try {
                const response = await fetch(`/api/tasks/${taskId}/toggle`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                });

                if (response.ok) {
                    setTimeout(() => {
                        taskItem.remove();
                        // Update active count badge
                        const badge = document.querySelector('.sidebar-task-title span');
                        if (badge) {
                            const count = parseInt(badge.textContent);
                            badge.textContent = `${count - 1} active`;
                        }
                    }, 300);
                } else {
                    alert('Error toggling task');
                    checkbox.style.backgroundColor = '';
                    checkbox.style.borderColor = '';
                    taskItem.style.opacity = '';
                    taskItem.style.transform = '';
                }
            } catch (err) {
                console.error(err);
                checkbox.style.backgroundColor = '';
                checkbox.style.borderColor = '';
                taskItem.style.opacity = '';
                taskItem.style.transform = '';
            }
        }

        // Quick task creation directly in Right Sidebar
        async function saveQuickTask(e) {
            e.preventDefault();
            const titleInput = document.getElementById('quick-task-title');
            const title = titleInput.value;
            if (!title) return;

            const payload = {
                title: title,
                time_block_id: {{ $blocks->first() ? $blocks->first()->id : 'null' }} // Assign to first block by default
            };

            try {
                const response = await fetch('/api/tasks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Error creating task');
                }
            } catch (err) {
                console.error(err);
            }
        }

        // Save Task from Calendar Grid Click Modal
        async function saveTask(e) {
            e.preventDefault();
            const title = document.getElementById('task-title').value;
            const timeBlockId = document.getElementById('task-time-block').value || null;
            const category = document.getElementById('task-category').value;
            const saveTaskBtn = document.getElementById('btn-save-task');

            saveTaskBtn.textContent = 'Saving...';
            saveTaskBtn.disabled = true;

            const payload = {
                title: title,
                time_block_id: timeBlockId,
                category: category
            };

            try {
                const response = await fetch('/api/tasks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Error creating task');
                    saveTaskBtn.textContent = 'Save Task';
                    saveTaskBtn.disabled = false;
                }
            } catch (err) {
                console.error(err);
                saveTaskBtn.textContent = 'Save Task';
                saveTaskBtn.disabled = false;
            }
        }

        // Filter calendar items
        function toggleFilter(type) {
            const isChecked = document.getElementById(`filter-${type}s`).checked;
            const cards = document.querySelectorAll(`.event-card.event-type-${type}`);
            cards.forEach(card => {
                card.style.display = isChecked ? 'block' : 'none';
            });
        }

        // Setup the Red Current Time Indicator
        function updateTimeIndicator() {
            const indicator = document.getElementById('time-indicator');
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();

            const gridStartHour = 7;
            const gridEndHour = 23;

            // Only show if between 7 AM and 11 PM
            if (hours >= gridStartHour && hours < gridEndHour) {
                const totalMins = ((hours - gridStartHour) * 60) + minutes;
                const totalGridMins = (gridEndHour - gridStartHour) * 60;
                const percent = (totalMins / totalGridMins) * 100;

                // Set indicator vertical position
                indicator.style.top = `${percent}%`;

                // Find active weekday column (0 = Sun, 1 = Mon, ..., 6 = Sat)
                const currentDayIndex = now.getDay(); 
                
                // Map the indicator width and horizontal position to exactly match that column
                const activeColumn = document.querySelector(`.grid-column[data-day-index="${currentDayIndex}"]`);
                
                if (activeColumn) {
                    const scrollContainer = document.querySelector('.time-grid-scrollable');
                    const leftColWidth = 60; // Width of time labels col
                    const colWidth = activeColumn.offsetWidth;
                    const leftPos = leftColWidth + (colWidth * currentDayIndex);

                    indicator.style.left = `${leftPos}px`;
                    indicator.style.width = `${colWidth}px`;
                    indicator.style.display = 'block';
                } else {
                    indicator.style.display = 'none';
                }
            } else {
                indicator.style.display = 'none';
            }
        }

        // Initial setup and polling for time indicator
        document.addEventListener('DOMContentLoaded', () => {
            // Apply saved sidebar collapse states immediately
            const container = document.querySelector('.calendar-view-container');
            const toggleLeftBtn = document.getElementById('toggle-left-sidebar');
            const toggleRightBtn = document.getElementById('toggle-right-sidebar');
            const toggleMainBtn = document.getElementById('toggle-main-sidebar');

            if (localStorage.getItem('leftSidebarHidden') === 'true') {
                container.classList.add('left-hidden');
            }
            if (localStorage.getItem('rightSidebarHidden') === 'true') {
                container.classList.add('right-hidden');
            }
            if (localStorage.getItem('mainSidebarCollapsed') === 'true') {
                document.body.classList.add('main-sidebar-collapsed');
            }

            if (toggleLeftBtn) {
                toggleLeftBtn.addEventListener('click', () => {
                    container.classList.toggle('left-hidden');
                    const isHidden = container.classList.contains('left-hidden');
                    localStorage.setItem('leftSidebarHidden', isHidden);
                    // Instantly update time indicator position & width on layout change
                    setTimeout(updateTimeIndicator, 300); // match transition duration
                    updateTimeIndicator();
                });
            }

            if (toggleRightBtn) {
                toggleRightBtn.addEventListener('click', () => {
                    container.classList.toggle('right-hidden');
                    const isHidden = container.classList.contains('right-hidden');
                    localStorage.setItem('rightSidebarHidden', isHidden);
                    // Instantly update time indicator position & width on layout change
                    setTimeout(updateTimeIndicator, 300); // match transition duration
                    updateTimeIndicator();
                });
            }

            const collapseMainBtn = document.getElementById('sidebar-collapse-btn');
            const toggleMainSidebar = () => {
                document.body.classList.toggle('main-sidebar-collapsed');
                const isCollapsed = document.body.classList.contains('main-sidebar-collapsed');
                localStorage.setItem('mainSidebarCollapsed', isCollapsed);
                // Instantly update time indicator position & width on layout change
                setTimeout(updateTimeIndicator, 300); // match transition duration
                updateTimeIndicator();
            };

            if (toggleMainBtn) {
                toggleMainBtn.addEventListener('click', toggleMainSidebar);
            }
            if (collapseMainBtn) {
                collapseMainBtn.addEventListener('click', toggleMainSidebar);
            }

            updateTimeIndicator();
            setInterval(updateTimeIndicator, 60000); // Update every minute
            
            // Scroll to 8:00 AM by default
            const scrollContainer = document.querySelector('.time-grid-scrollable');
            if (scrollContainer) {
                scrollContainer.scrollTop = 60; // 1 hour down
            }
        });

        // Window resize adjustments
        window.addEventListener('resize', updateTimeIndicator);
    </script>

    <!-- Standard Dark Mode script and Scratchpad JS -->
    <script>
        // Theme toggle action
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
            });
        }

        // Scratchpad JS
        const quickNoteTextarea = document.getElementById('quick-note');
        const notesList = document.getElementById('notes-list');

        if (quickNoteTextarea) {
            quickNoteTextarea.addEventListener('keydown', async (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    const content = quickNoteTextarea.value.trim();
                    if (!content) return;

                    quickNoteTextarea.value = '';
                    quickNoteTextarea.disabled = true;

                    try {
                        const response = await fetch('/api/notes', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ content })
                        });

                        if (response.ok) {
                            window.location.reload();
                        } else {
                            alert('Error saving note');
                            quickNoteTextarea.disabled = false;
                        }
                    } catch (err) {
                        console.error(err);
                        quickNoteTextarea.disabled = false;
                    }
                }
            });
        }

        // Delete note
        if (notesList) {
            notesList.addEventListener('click', async (e) => {
                if (e.target.classList.contains('delete-note-btn')) {
                    const btn = e.target;
                    const noteId = btn.getAttribute('data-id');
                    if (!noteId) return;

                    btn.disabled = true;

                    try {
                        const response = await fetch(`/api/notes/${noteId}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': csrfToken }
                        });

                        if (response.ok) {
                            btn.closest('.task-item').remove();
                        } else {
                            alert('Error deleting note');
                            btn.disabled = false;
                        }
                    } catch (err) {
                        console.error(err);
                        btn.disabled = false;
                    }
                }
            });
        }
    </script>
</body>
</html>
