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
    <title>Voin - Review Log</title>
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
        .ai-insights-panel {
            background: linear-gradient(135deg, rgba(15, 118, 110, 0.05) 0%, rgba(45, 212, 191, 0.05) 100%);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 24px;
            margin-bottom: 32px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        [data-theme="dark"] .ai-insights-panel {
            background: linear-gradient(135deg, rgba(45, 212, 191, 0.03) 0%, rgba(15, 118, 110, 0.03) 100%);
        }
        .ai-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .ai-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-primary);
        }
        .ai-sparkle {
            color: var(--accent-teal);
            animation: pulse 2s infinite ease-in-out;
        }
        .ai-content {
            font-size: 0.95rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }
        .ai-content h3, .ai-content h4 {
            color: var(--text-primary);
            margin-top: 16px;
            margin-bottom: 8px;
            font-weight: 700;
        }
        .ai-content ul {
            margin-left: 20px;
            margin-bottom: 12px;
        }
        .ai-content li {
            margin-bottom: 4px;
        }
        .skeleton {
            background: linear-gradient(90deg, var(--border-color) 25%, var(--bg-color) 50%, var(--border-color) 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            border-radius: var(--radius-md);
            height: 16px;
            margin-bottom: 12px;
        }
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.15); opacity: 0.8; }
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
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
                    <h1>Review log</h1>
                </div>
                <div class="header-actions">
                    <button class="theme-toggle" id="theme-toggle" aria-label="Switch theme" style="width:44px; height:44px; border-radius:50%; background:var(--surface-color); border:1px solid var(--border-color); display:grid; place-items:center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    </button>
                </div>
            </header>

            <div class="dashboard-viewport">
                <!-- AI Insights Panel -->
                <div class="ai-insights-panel">
                    <div class="ai-header">
                        <div class="ai-title">
                            <svg class="ai-sparkle" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m11.314 11.314l.707-.707M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8z"/>
                            </svg>
                            <span>AI Weekly Coach Insights</span>
                        </div>
                        <button class="btn btn-primary" id="btn-generate-ai" style="min-height: 36px; height: 36px; padding: 0 14px; font-size: 0.8rem;">
                            Generate Weekly Summary
                        </button>
                    </div>
                    <div id="ai-insights-content" class="ai-content">
                        <p style="font-size: 0.9rem; opacity: 0.8;">Click the button to analyze your daily reviews, logs, and task completion metrics from the past 7 days to receive personalized productivity feedback.</p>
                    </div>
                </div>

                <div class="section-panel">
                    <div class="panel-header" style="border-bottom: none; padding-bottom: 0;">
                        <h3 class="panel-title">Past Reviews</h3>
                        <p class="panel-subtitle">History of your self-review scores and highlights</p>
                    </div>
                    <div class="card" style="margin-top: 16px;">
                        @forelse($reviews as $review)
                        <div class="timeline-item">
                            <div class="timeline-time" style="font-weight: 700;">
                                {{ \Carbon\Carbon::parse($review->review_date)->format('M d') }}<br>
                                <span style="font-size: 0.8rem; opacity: 0.6;">{{ \Carbon\Carbon::parse($review->review_date)->format('Y') }}</span>
                            </div>
                            <div class="timeline-content">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                                    <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary);">Focus Score: <span style="color: #0f766e;">{{ $review->focus_score }}/10</span></h4>
                                    <div style="width: 120px; height: 8px; background: var(--border-color); border-radius: 4px; overflow: hidden; box-shadow: inset 0 1px 1px rgba(0,0,0,0.05);">
                                        <div style="width: {{ $review->focus_score * 10 }}%; height: 100%; background: #0f766e; border-radius: 4px;"></div>
                                    </div>
                                </div>
                                @if($review->daily_focus)
                                <p style="font-size: 0.85rem; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;">🎯 Primary Focus: <span style="font-weight: 500; color: var(--text-secondary);">{{ $review->daily_focus }}</span></p>
                                @endif
                                <p style="font-size: 0.9rem; color: var(--text-secondary); line-height: 1.5; margin-top: 6px;">{{ $review->summary ?? 'No summary provided.' }}</p>
                            </div>
                        </div>
                        @empty
                        <div style="text-align: center; padding: 48px;">
                            <p style="font-size: 0.9rem; color: var(--text-secondary);">No reviews found yet. Complete your first day to see it here.</p>
                        </div>
                        @endforelse
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
    <script>
        document.getElementById('btn-generate-ai')?.addEventListener('click', function() {
            const btn = this;
            const contentDiv = document.getElementById('ai-insights-content');
            
            // Set loading state
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner" style="display:inline-block; width:12px; height:12px; border:2px solid #fff; border-top-color:transparent; border-radius:50%; animation:spin 0.6s linear infinite; margin-right:6px; vertical-align:middle;"></span> Generating...`;
            
            contentDiv.innerHTML = `
                <div class="skeleton" style="width: 40%"></div>
                <div class="skeleton" style="width: 85%"></div>
                <div class="skeleton" style="width: 70%"></div>
                <div class="skeleton" style="width: 90%"></div>
            `;

            fetch('/api/reviews/weekly-summary')
                .then(res => res.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerText = 'Regenerate Insights';
                    
                    // Simple Markdown-like formatter for bullet points, headers and bold text
                    let formatted = data.summary
                        .replace(/^### (.*$)/gim, '<h3>$1</h3>')
                        .replace(/^#### (.*$)/gim, '<h4>$1</h4>')
                        .replace(/^\*\*(.*)\*\*/gim, '<strong>$1</strong>')
                        .replace(/^\* (.*$)/gim, '<li>$1</li>')
                        .replace(/^- (.*$)/gim, '<li>$1</li>')
                        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\n\n/g, '<br><br>');
                    
                    // Wrap continuous <li> tags in <ul>
                    formatted = formatted.replace(/(<li>.*?<\/li>)+/g, '<ul>$&</ul>');
                    
                    contentDiv.innerHTML = formatted;
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerText = 'Generate Weekly Summary';
                    contentDiv.innerHTML = `<p style="color: var(--color-error);">Failed to generate insights. Please try again. Error: ${err.message}</p>`;
                });
        });
    </script>
</body>
</html>
