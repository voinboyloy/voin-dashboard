<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Calendar - Axis</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-axis-bg text-axis-text font-sans flex overflow-hidden">

    <!-- Sidebar (Shared logic - simplified for Calendar) -->
    <aside id="sidebar" class="w-64 bg-axis-surface border-r border-axis-border flex flex-col transition-transform duration-300 z-40 lg:relative lg:translate-x-0 absolute inset-y-0 left-0 -translate-x-full">
        <div class="h-16 flex items-center px-6 border-b border-axis-border flex-shrink-0 justify-between">
            <div class="flex items-center gap-2">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="text-axis-teal">
                    <rect width="24" height="24" rx="6" fill="currentColor" fill-opacity="0.1"/>
                    <path d="M7 17L12 7L17 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div class="font-semibold tracking-tight">Axis</div>
            </div>
            <button id="close-sidebar" class="lg:hidden p-1 text-axis-muted hover:text-axis-text">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-4 flex-1 overflow-y-auto">
            <nav class="space-y-1 mb-8">
                <div class="px-2 text-xs font-medium text-axis-muted uppercase tracking-wider mb-2">Views</div>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-2 py-1.5 text-sm rounded-md text-axis-muted hover:bg-axis-border hover:text-axis-text">Today</a>
                <a href="{{ route('weekly-load') }}" class="flex items-center gap-3 px-2 py-1.5 text-sm rounded-md text-axis-muted hover:bg-axis-border hover:text-axis-text">Weekly load</a>
                <a href="{{ route('calendar') }}" class="flex items-center gap-3 px-2 py-1.5 text-sm rounded-md bg-axis-teal/10 text-axis-teal font-medium">Calendar</a>
                <a href="{{ route('savings-tracker') }}" class="flex items-center gap-3 px-2 py-1.5 text-sm rounded-md text-axis-muted hover:bg-axis-border hover:text-axis-text">Savings</a>
                <a href="{{ route('workout-planner') }}" class="flex items-center gap-3 px-2 py-1.5 text-sm rounded-md text-axis-muted hover:bg-axis-border hover:text-axis-text">Workout</a>
                <a href="{{ route('credentials-vault') }}" class="flex items-center gap-3 px-2 py-1.5 text-sm rounded-md text-axis-muted hover:bg-axis-border hover:text-axis-text">Vault</a>
                <a href="{{ route('review-log') }}" class="flex items-center gap-3 px-2 py-1.5 text-sm rounded-md text-axis-muted hover:bg-axis-border hover:text-axis-text">Review log</a>
            </nav>
        </div>
        <div class="p-4 border-t border-axis-border flex-shrink-0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-axis-muted hover:text-axis-text w-full text-left px-2">Log out</button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
        <header class="h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 border-b border-axis-border bg-axis-surface/80 backdrop-blur-sm sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <button id="open-sidebar" class="lg:hidden p-1 -ml-1 text-axis-muted hover:text-axis-text">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="text-lg font-semibold tracking-tight">Calendar</h1>
                    <div class="text-xs text-axis-muted">Schedule and events synchronized with Notion.</div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex gap-1 items-center bg-axis-border/50 rounded-md p-1">
                    <a href="?month={{ $month->copy()->subMonth()->format('Y-m') }}" class="p-1 hover:bg-axis-border rounded text-axis-muted">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                    </a>
                    <span class="text-sm font-medium px-2">{{ $month->format('F Y') }}</span>
                    <a href="?month={{ $month->copy()->addMonth()->format('Y-m') }}" class="p-1 hover:bg-axis-border rounded text-axis-muted">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                    </a>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            <div class="max-w-6xl mx-auto">

                <div class="bg-axis-surface rounded-xl border border-axis-border shadow-sm overflow-hidden">
                    <div class="grid grid-cols-7 border-b border-axis-border bg-axis-border/20">
                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
                            <div class="py-2 text-center text-xs font-medium text-axis-muted">{{ $dayName }}</div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-7 auto-rows-[120px]">
                        @foreach($days as $index => $day)
                            <div class="border-b border-r border-axis-border p-2 flex flex-col group {{ !$day['isCurrentMonth'] ? 'bg-axis-border/10 opacity-50' : '' }} {{ ($index + 1) % 7 == 0 ? 'border-r-0' : '' }}">
                                <div class="flex justify-between items-start">
                                    <span class="text-sm font-medium {{ $day['date']->isToday() ? 'bg-axis-teal text-white w-6 h-6 rounded-full flex items-center justify-center -ml-1 -mt-1' : '' }}">
                                        {{ $day['date']->format('j') }}
                                    </span>
                                    <button onclick="openEventModal('{{ $day['date']->format('Y-m-d') }}')" class="opacity-0 group-hover:opacity-100 text-axis-muted hover:text-axis-teal transition-opacity">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                                    </button>
                                </div>
                                <div class="mt-2 flex-1 overflow-y-auto space-y-1 pr-1 custom-scrollbar">
                                    @foreach($day['events'] as $event)
                                        <div class="text-xs px-2 py-1 rounded truncate flex justify-between group/event cursor-pointer
                                            {{ $event->type === 'deadline' ? 'bg-red-500/10 text-red-600 dark:text-red-400' :
                                               ($event->type === 'reminder' ? 'bg-yellow-500/10 text-yellow-600 dark:text-yellow-400' : 'bg-axis-teal/10 text-axis-teal') }}"
                                            onclick="editEvent({{ $event->id }}, '{{ addslashes($event->title) }}', '{{ $event->event_date->format('Y-m-d') }}', '{{ $event->type }}', '{{ addslashes($event->description) }}')">
                                            <span>{{ $event->title }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- Event Modal -->
    <div id="event-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-axis-surface rounded-xl border border-axis-border shadow-xl w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-lg" id="modal-title">Add Event</h3>
                <button onclick="closeEventModal()" class="text-axis-muted hover:text-axis-text">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="event-form" onsubmit="saveEvent(event)">
                <input type="hidden" id="event-id">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Title</label>
                        <input type="text" id="event-title" required class="w-full bg-axis-bg border border-axis-border rounded-md px-3 py-2 text-sm focus:outline-none focus:border-axis-teal">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Date</label>
                            <input type="date" id="event-date" required class="w-full bg-axis-bg border border-axis-border rounded-md px-3 py-2 text-sm focus:outline-none focus:border-axis-teal">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Type</label>
                            <select id="event-type" class="w-full bg-axis-bg border border-axis-border rounded-md px-3 py-2 text-sm focus:outline-none focus:border-axis-teal">
                                <option value="event">Event</option>
                                <option value="deadline">Deadline</option>
                                <option value="reminder">Reminder</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Description (Optional)</label>
                        <textarea id="event-desc" rows="3" class="w-full bg-axis-bg border border-axis-border rounded-md px-3 py-2 text-sm focus:outline-none focus:border-axis-teal"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" id="btn-delete-event" onclick="deleteEvent()" class="hidden px-4 py-2 text-sm text-red-500 hover:bg-red-500/10 rounded-md font-medium">Delete</button>
                    <button type="button" onclick="closeEventModal()" class="px-4 py-2 text-sm text-axis-muted hover:bg-axis-border rounded-md font-medium">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm bg-axis-teal text-white rounded-md font-medium shadow-sm hover:bg-opacity-90">Save Event</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/20 z-30 hidden lg:hidden backdrop-blur-sm"></div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Mobile Sidebar
        const sidebar = document.getElementById('sidebar');
        const openBtn = document.getElementById('open-sidebar');
        const closeBtn = document.getElementById('close-sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        openBtn.addEventListener('click', toggleSidebar);
        closeBtn.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);

        // Modal Logic
        const modal = document.getElementById('event-modal');
        const form = document.getElementById('event-form');
        const modalTitle = document.getElementById('modal-title');
        const deleteBtn = document.getElementById('btn-delete-event');

        function openEventModal(date) {
            form.reset();
            document.getElementById('event-id').value = '';
            document.getElementById('event-date').value = date;
            modalTitle.textContent = 'Add Event';
            deleteBtn.classList.add('hidden');
            modal.classList.remove('hidden');
        }

        function editEvent(id, title, date, type, desc) {
            document.getElementById('event-id').value = id;
            document.getElementById('event-title').value = title;
            document.getElementById('event-date').value = date;
            document.getElementById('event-type').value = type;
            document.getElementById('event-desc').value = desc;

            modalTitle.textContent = 'Edit Event';
            deleteBtn.classList.remove('hidden');
            modal.classList.remove('hidden');
        }

        function closeEventModal() {
            modal.classList.add('hidden');
        }

        async function saveEvent(e) {
            e.preventDefault();
            const id = document.getElementById('event-id').value;
            const payload = {
                id: id ? id : null,
                title: document.getElementById('event-title').value,
                event_date: document.getElementById('event-date').value,
                type: document.getElementById('event-type').value,
                description: document.getElementById('event-desc').value,
            };

            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.textContent = 'Saving...';
            submitBtn.disabled = true;

            try {
                const response = await fetch('/events', {
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
                    submitBtn.textContent = 'Save Event';
                    submitBtn.disabled = false;
                }
            } catch (err) {
                console.error(err);
                submitBtn.textContent = 'Save Event';
                submitBtn.disabled = false;
            }
        }

        async function deleteEvent() {
            const id = document.getElementById('event-id').value;
            if (!id) return;
            if (!confirm('Are you sure you want to delete this event?')) return;

            deleteBtn.textContent = 'Deleting...';
            deleteBtn.disabled = true;

            try {
                const response = await fetch(`/events/${id}`, {
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
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 4px; }
        [data-theme="dark"] .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); }
    </style>
</body>
</html>
