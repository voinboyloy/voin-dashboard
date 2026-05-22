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
    <title>Voin - Credentials Vault</title>
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
        .credential-item {
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            background: var(--surface-color);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .credential-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .platform-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--accent-teal-soft);
            color: var(--accent-teal);
            display: grid;
            place-items: center;
            font-weight: 700;
            font-size: 1.2rem;
        }
        .password-field {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--bg-color);
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            font-family: monospace;
            font-size: 0.9rem;
            position: relative;
        }
        .password-toggle {
            cursor: pointer;
            opacity: 0.6;
            transition: opacity 0.2s;
        }
        .password-toggle:hover {
            opacity: 1;
        }
        .credential-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .form-group label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #8c8c88;
            font-weight: 700;
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
                    <div class="brand-text" style="display: flex; flex-direction: column;">
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
                    <h1>Credentials vault</h1>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" id="add-credential-btn">+ Add Platform</button>
                    <button class="theme-toggle" id="theme-toggle" aria-label="Switch theme" style="width:44px; height:44px; border-radius:50%; background:var(--surface-color); border:1px solid var(--border-color); display:grid; place-items:center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    </button>
                </div>
            </header>

            <div class="dashboard-viewport">
                <div class="panel-header">
                    <h3 class="panel-title">Your Secure Keys</h3>
                    <p class="panel-subtitle">Manage passwords for social media and other platforms</p>
                </div>

                <div class="credential-grid">
                    @forelse($credentials as $cred)
                    <article class="credential-item">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="platform-icon">{{ strtoupper(substr($cred->platform, 0, 1)) }}</div>
                                <div>
                                    <h4 style="font-weight: 700; color: var(--text-primary);">{{ $cred->platform }}</h4>
                                    <p class="muted tiny" style="color: var(--text-secondary);">{{ $cred->url ?: 'No URL provided' }}</p>
                                </div>
                            </div>
                            <div style="display: flex; gap: 4px;">
                                <button class="btn-ghost edit-credential-btn" 
                                    data-id="{{ $cred->id }}"
                                    data-platform="{{ $cred->platform }}"
                                    data-username="{{ $cred->username }}"
                                    data-password="{{ $cred->password }}"
                                    data-url="{{ $cred->url }}"
                                    data-notes="{{ $cred->notes }}"
                                    style="padding: 6px; color: var(--text-secondary);">
                                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                <button class="btn-ghost delete-credential-btn" data-id="{{ $cred->id }}" style="padding: 6px; color: var(--color-error);">
                                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700; color: var(--text-secondary); margin-bottom: 4px; display: block;">Username / Email</label>
                            <p style="font-weight: 500; color: var(--text-primary);">{{ $cred->username ?: '—' }}</p>
                        </div>

                        <div>
                            <label style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700; color: var(--text-secondary); margin-bottom: 4px; display: block;">Password</label>
                            <div class="password-field">
                                <span class="password-value" data-password="{{ $cred->password }}">••••••••</span>
                                <div style="margin-left: auto; display: flex; gap: 8px;">
                                    <svg class="password-toggle" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="cursor: pointer;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg class="copy-password" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="cursor: pointer; opacity: 0.6;"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                </div>
                            </div>
                        </div>

                        @if($cred->notes)
                        <div style="margin-top: 4px;">
                            <p class="muted tiny" style="background: #f8fafc; padding: 8px; border-radius: 6px; border: 1px dashed var(--border-color);">{{ $cred->notes }}</p>
                        </div>
                        @endif
                    </article>
                    @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 64px; background: var(--surface-color); border-radius: 16px; border: 2px dashed var(--border-color);">
                        <div style="font-size: 3rem; margin-bottom: 16px;">🔐</div>
                        <h3 class="panel-title">No credentials saved yet</h3>
                        <p class="muted" style="margin-top: 8px;">Start by adding your first platform password.</p>
                        <button class="btn btn-primary" style="margin-top: 24px;" id="add-first-key-btn">+ Add First Key</button>
                    </div>
                    @endforelse
                </div>
            </div>
        </main>
    </div>

    <!-- Modals -->
    <div id="credential-modal" class="modal"><div class="card" style="max-width: 450px; width: 90%;">
        <div class="panel-header" style="display:flex; justify-content:space-between; align-items:center; border:none; padding:0;">
            <h3 class="panel-title" id="cred-modal-title">Add Credential</h3>
            <button class="btn-ghost" id="close-cred-modal" style="font-size:1.5rem; padding:4px;">×</button>
        </div>
        <form id="credential-form" style="margin-top:16px;">
            <input type="hidden" name="id" id="cred-id">
            <div class="form-group"><label>Platform Name</label><input type="text" name="platform" id="cred-platform" class="input" placeholder="e.g. Facebook, My Bank" required></div>
            <div class="form-group"><label>Username / Email</label><input type="text" name="username" id="cred-username" class="input" placeholder="e.g. john@example.com"></div>
            <div class="form-group"><label>Password</label><input type="password" name="password" id="cred-password" class="input" placeholder="Your secret key" required></div>
            <div class="form-group"><label>URL</label><input type="url" name="url" id="cred-url" class="input" placeholder="https://..."></div>
            <div class="form-group"><label>Notes</label><textarea name="notes" id="cred-notes" class="input" placeholder="Recovery codes, etc." rows="3" style="resize: none;"></textarea></div>
            
            <button type="submit" class="btn btn-primary" id="cred-submit-btn" style="width:100%; margin-top:24px;">Save Credential</button>
        </form>
    </div></div>

    <div id="confirm-modal" class="modal"><div class="card" style="text-align:center;">
        <h3 class="panel-title">Confirm Delete</h3>
        <p class="muted tiny" id="confirm-modal-message" style="margin: 16px 0; color: var(--text-secondary);">This action is permanent.</p>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <button type="button" id="confirm-cancel-btn" class="btn btn-secondary">Cancel</button>
            <button type="button" id="confirm-delete-btn" class="btn btn-primary" style="background:#df3b3b; border-color:#df3b3b; box-shadow:none;">Delete</button>
        </div>
    </div></div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Re-binding the add-first-key-btn if it exists
            const addFirstBtn = document.getElementById('add-first-key-btn');
            const mainAddBtn = document.getElementById('add-credential-btn');
            if (addFirstBtn && mainAddBtn) {
                addFirstBtn.addEventListener('click', () => mainAddBtn.click());
            }

            // Inline logic for revealing passwords
            document.querySelectorAll('.password-toggle').forEach(btn => {
                btn.addEventListener('click', () => {
                    const container = btn.closest('.password-field');
                    const valEl = container.querySelector('.password-value');
                    const actualPassword = valEl.dataset.password;
                    
                    if (valEl.textContent === '••••••••') {
                        valEl.textContent = actualPassword;
                        btn.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                    } else {
                        valEl.textContent = '••••••••';
                        btn.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
                    }
                });
            });

            document.querySelectorAll('.copy-password').forEach(btn => {
                btn.addEventListener('click', () => {
                    const valEl = btn.closest('.password-field').querySelector('.password-value');
                    navigator.clipboard.writeText(valEl.dataset.password);
                    
                    // Visual feedback
                    const originalColor = btn.style.color;
                    btn.style.color = 'var(--accent-teal)';
                    setTimeout(() => btn.style.color = originalColor, 1000);
                });
            });
        });
    </script>
</body>
</html>
