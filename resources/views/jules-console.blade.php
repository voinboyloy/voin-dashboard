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
    <title>Voin - Jules Console</title>
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
        .console-grid {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 28px;
            align-items: start;
        }
        @media (max-width: 1024px) {
            .console-grid {
                grid-template-columns: 1fr;
            }
        }
        .terminal-panel {
            background: #111827;
            color: #f3f4f6;
            font-family: 'Courier New', Courier, monospace;
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            min-height: 450px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .terminal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .terminal-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }
        .terminal-output {
            flex: 1;
            overflow-y: auto;
            max-height: 320px;
            font-size: 0.85rem;
            line-height: 1.5;
            padding-right: 8px;
        }
        .terminal-line {
            margin-bottom: 10px;
            opacity: 0.9;
        }
        .terminal-line.timestamp {
            color: #9ca3af;
            font-size: 0.75rem;
        }
        .terminal-line.success {
            color: #34d399;
        }
        .terminal-line.info {
            color: #60a5fa;
        }
        .terminal-input-row {
            display: flex;
            gap: 12px;
            margin-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 16px;
        }
        .terminal-input-row input {
            background: #1f2937;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #ffffff;
            font-family: inherit;
        }
        .session-item {
            padding: 16px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            background: var(--surface-color);
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .session-item:hover, .session-item.active {
            border-color: var(--accent-teal);
            background: var(--accent-teal-soft);
        }
        .session-prompt {
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--text-primary);
            margin-bottom: 4px;
        }
        .session-meta {
            font-size: 0.78rem;
            color: var(--text-secondary);
            display: flex;
            justify-content: space-between;
        }
        .spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid currentColor;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
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
                            <a href="{{ route('dashboard') }}" class="nav-link">
                                <span class="nav-text">Today</span>
                                <span class="nav-link-meta">Rigid plan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('weekly-load') }}" class="nav-link">
                                <span class="nav-text">Weekly load</span>
                                <span class="nav-link-meta">7 blocks</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('calendar') }}" class="nav-link">
                                <span class="nav-text">Calendar</span>
                                <span class="nav-link-meta">Schedule</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('review-log') }}" class="nav-link">
                                <span class="nav-text">Review log</span>
                                <span class="nav-link-meta">Daily notes</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('monthly-tasks') }}" class="nav-link">
                                <span class="nav-text">Monthly tasks</span>
                                <span class="nav-link-meta">Review & Add</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('jules.index') }}" class="nav-link active">
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
                            <a href="{{ route('savings-tracker') }}" class="nav-link">
                                <span class="nav-text">Savings tracker</span>
                                <span class="nav-link-meta">Cash flow</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('workout-planner') }}" class="nav-link">
                                <span class="nav-text">Workout planner</span>
                                <span class="nav-link-meta">Routine</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('credentials-vault') }}" class="nav-link">
                                <span class="nav-text">Credentials vault</span>
                                <span class="nav-link-meta">Secure keys</span>
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
                    <button class="btn btn-secondary" id="toggle-main-sidebar" title="Main menu" style="min-height: unset; height: 32px; width: 32px; padding: 0; border-radius: 8px; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; background: var(--surface-color); margin-right: -4px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                    </button>
                    <h1>Jules Agent Console</h1>
                </div>
                <div class="header-actions">
                    <button class="theme-toggle" id="theme-toggle" aria-label="Switch theme" style="width:44px; height:44px; border-radius:50%; background:var(--surface-color); border:1px solid var(--border-color); display:grid; place-items:center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    </button>
                </div>
            </header>

            <div class="dashboard-viewport">
                <div class="console-grid">
                    <!-- Session Control Panel -->
                    <div class="section-panel">
                        <div class="card" style="margin-bottom: 24px;">
                            <h3 class="panel-title" style="margin-bottom: 12px;">Trigger AI Coding Agent</h3>
                            <form id="create-session-form">
                                <div class="form-group">
                                    <label for="prompt">Prompt (Coding Task)</label>
                                    <textarea id="prompt" required placeholder="e.g. Add validation to TaskController and write unit tests for tasks..." rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="repo_path">GitHub Repository Context</label>
                                    <input type="text" id="repo_path" value="{{ $defaultRepo }}" placeholder="sources/github/username/repo">
                                </div>
                                <button type="submit" class="btn btn-primary" style="width: 100%;">
                                    Start Session with Jules
                                </button>
                            </form>
                        </div>

                        <h3 class="panel-title" style="margin-bottom: 12px;">Coding Sessions</h3>
                        <div id="sessions-list">
                            @forelse($sessions as $session)
                            <div class="session-item" data-id="{{ $session->id }}">
                                <div class="session-prompt">{{ $session->prompt }}</div>
                                <div class="session-meta">
                                    <span>Repo: {{ basename($session->repo_path) }}</span>
                                    <span style="font-weight: 700; color: var(--accent-teal);">{{ $session->status }}</span>
                                </div>
                            </div>
                            @empty
                            <div style="padding: 24px; text-align: center; border: 1px dashed var(--border-color); border-radius: var(--radius-md);">
                                <p style="font-size: 0.85rem; color: var(--text-secondary);">No coding sessions started yet.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Interactive Terminal Feed -->
                    <div class="terminal-panel">
                        <div>
                            <div class="terminal-header">
                                <div style="display: flex; align-items: center;">
                                    <span class="terminal-dot" style="background: #ef4444;"></span>
                                    <span class="terminal-dot" style="background: #f59e0b;"></span>
                                    <span class="terminal-dot" style="background: #10b981;"></span>
                                    <span style="font-weight: 700; font-size: 0.8rem; margin-left: 8px;">Jules Terminal Feed</span>
                                </div>
                                <span id="active-session-status" style="font-size: 0.75rem; font-weight: 700; color: #10b981;">Select a session</span>
                            </div>

                            <div id="terminal-output" class="terminal-output">
                                <div class="terminal-line">Waiting for session selection...</div>
                            </div>
                        </div>

                        <form id="terminal-input-form" class="terminal-input-row" style="display: none;">
                            <input type="text" id="terminal-message" placeholder="Type a message to Jules..." style="flex: 1; min-height: 38px; border-radius: 8px; padding: 0 12px;">
                            <button type="submit" class="btn btn-primary" style="min-height: 38px; height: 38px; padding: 0 16px;">Send</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        let activeSessionId = null;

        // Create Session Form Submission
        document.getElementById('create-session-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const prompt = document.getElementById('prompt').value;
            const repo_path = document.getElementById('repo_path').value;

            btn.disabled = true;
            btn.innerHTML = `<span class="spinner" style="margin-right: 6px;"></span> Starting Agent...`;

            fetch('/api/jules/sessions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ prompt, repo_path })
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || 'Failed to start session');
                }
                return data;
            })
            .then(data => {
                btn.disabled = false;
                btn.innerText = 'Start Session with Jules';
                document.getElementById('prompt').value = '';
                
                // Refresh Page or append element
                location.reload();
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerText = 'Start Session with Jules';
                alert('Error creating session: ' + err.message);
            });
        });

        // Load Session Activities into Terminal Feed
        function loadSession(id) {
            activeSessionId = id;
            const output = document.getElementById('terminal-output');
            const statusLabel = document.getElementById('active-session-status');
            const inputForm = document.getElementById('terminal-input-form');

            // Select session item styling
            document.querySelectorAll('.session-item').forEach(item => {
                item.classList.remove('active');
                if (item.getAttribute('data-id') == id) item.classList.add('active');
            });

            output.innerHTML = `<div class="terminal-line"><span class="spinner" style="margin-right: 6px;"></span> Connecting to Jules Agent...</div>`;
            inputForm.style.display = 'none';

            fetch(`/api/jules/sessions/${id}`)
            .then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || 'Failed to load session');
                }
                return data;
            })
            .then(data => {
                statusLabel.innerText = data.session.status || 'ACTIVE';
                inputForm.style.display = 'flex';
                output.innerHTML = '';

                if (data.activities.length === 0) {
                    output.innerHTML = `<div class="terminal-line">No activities recorded. Agent starting...</div>`;
                    return;
                }

                data.activities.forEach(act => {
                    const time = new Date(act.timestamp).toLocaleTimeString();
                    let lineClass = 'info';
                    if (act.type === 'pull_request_created' || act.type === 'success') lineClass = 'success';
                    
                    output.innerHTML += `
                        <div class="terminal-line timestamp">[${time}]</div>
                        <div class="terminal-line ${lineClass}">&gt; ${act.description}</div>
                    `;
                });
                
                output.scrollTop = output.scrollHeight;
            })
            .catch(err => {
                output.innerHTML = `<div class="terminal-line" style="color: #ef4444;">Failed to load session details: ${err.message}</div>`;
            });
        }

        // Add Click Listeners to Session Items
        document.querySelectorAll('.session-item').forEach(item => {
            item.addEventListener('click', function() {
                loadSession(this.getAttribute('data-id'));
            });
        });

        // Terminal Chat Input Form Submission
        document.getElementById('terminal-input-form').addEventListener('submit', function(e) {
            e.preventDefault();
            if (!activeSessionId) return;

            const input = document.getElementById('terminal-message');
            const message = input.value;
            if (!message) return;

            const output = document.getElementById('terminal-output');
            const time = new Date().toLocaleTimeString();

            // Append user message immediately
            output.innerHTML += `
                <div class="terminal-line timestamp">[${time}]</div>
                <div class="terminal-line" style="color: #f59e0b;">&gt; User: ${message}</div>
            `;
            output.scrollTop = output.scrollHeight;
            input.value = '';

            fetch(`/api/jules/sessions/${activeSessionId}/message`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message })
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || 'Failed to deliver message');
                }
                return data;
            })
            .then(data => {
                if (data.reply) {
                    output.innerHTML += `
                        <div class="terminal-line timestamp">[${new Date().toLocaleTimeString()}]</div>
                        <div class="terminal-line success">&gt; ${data.reply}</div>
                    `;
                    output.scrollTop = output.scrollHeight;
                }
            })
            .catch(err => {
                output.innerHTML += `<div class="terminal-line" style="color: #ef4444;">Error delivering message: ${err.message}</div>`;
                output.scrollTop = output.scrollHeight;
            });
        });
    </script>
</body>
</html>
