document.addEventListener('DOMContentLoaded', () => {
    const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content;

    // --- MAIN NAVIGATION SIDEBAR COLLAPSE LOGIC ---
    if (localStorage.getItem('mainSidebarCollapsed') === 'true') {
        document.body.classList.add('main-sidebar-collapsed');
    }

    const toggleMainBtn = document.getElementById('toggle-main-sidebar');
    const collapseMainBtn = document.getElementById('sidebar-collapse-btn');

    const toggleMainSidebar = () => {
        document.body.classList.toggle('main-sidebar-collapsed');
        const isCollapsed = document.body.classList.contains('main-sidebar-collapsed');
        localStorage.setItem('mainSidebarCollapsed', isCollapsed);
    };

    if (toggleMainBtn) {
        toggleMainBtn.addEventListener('click', toggleMainSidebar);
    }
    if (collapseMainBtn) {
        collapseMainBtn.addEventListener('click', toggleMainSidebar);
    }

    // --- SHARED UI LOGIC ---

    // Theme Toggle
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }

    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        document.documentElement.setAttribute('data-theme', savedTheme);
    }

    // Mobile Menu
    const menuBtn = document.getElementById('menu-btn');
    const sidebar = document.querySelector('.sidebar');
    if (menuBtn && sidebar) {
        menuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    }

    // Modal Helpers
    const showModal = (modalId) => {
        const modal = document.getElementById(modalId);
        if (modal) modal.style.display = 'flex';
    };

    const hideModal = (modalId) => {
        const modal = document.getElementById(modalId);
        if (modal) modal.style.display = 'none';
    };

    // --- CUSTOM CONFIRMATION LOGIC ---
    let deletionCallback = null;
    const confirmModal = document.getElementById('confirm-modal');
    const confirmMessage = document.getElementById('confirm-modal-message');
    const confirmCancelBtn = document.getElementById('confirm-cancel-btn');
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');

    const askConfirmation = (message, onConfirm) => {
        if (confirmMessage) confirmMessage.textContent = message;
        deletionCallback = onConfirm;
        showModal('confirm-modal');
    };

    if (confirmCancelBtn) {
        confirmCancelBtn.addEventListener('click', () => {
            hideModal('confirm-modal');
            deletionCallback = null;
        });
    }

    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', async () => {
            if (deletionCallback) {
                await deletionCallback();
                hideModal('confirm-modal');
                deletionCallback = null;
            }
        });
    }

    // --- CHECKBOX INTERACTION LOGIC (REAL-TIME) ---

    // 1. Task Checkboxes
    document.querySelectorAll('.task-checkbox').forEach(cb => {
        cb.addEventListener('click', async () => {
            const taskId = cb.dataset.taskId;
            cb.classList.toggle('checked');
            cb.closest('.task-item')?.classList.toggle('done');
            updateKPIs();
            
            try {
                await fetch(`/api/tasks/${taskId}/toggle`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': getCsrfToken() }
                });
            } catch (err) {
                console.error('Failed to toggle task:', err);
                cb.classList.toggle('checked');
                cb.closest('.task-item')?.classList.toggle('done');
                updateKPIs();
            }
        });
    });

    // 2. Habit Checkboxes (Sync across panels)
    document.querySelectorAll('.habit-checkbox').forEach(cb => {
        cb.addEventListener('click', async () => {
            const habitId = cb.dataset.id;
            const isChecking = !cb.classList.contains('checked');
            
            document.querySelectorAll(`.habit-checkbox[data-id="${habitId}"]`).forEach(el => {
                if (isChecking) el.classList.add('checked');
                else el.classList.remove('checked');
            });

            try {
                await fetch(`/api/habits/${habitId}/toggle`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': getCsrfToken() }
                });
            } catch (err) {
                console.error('Failed to toggle habit:', err);
                document.querySelectorAll(`.habit-checkbox[data-id="${habitId}"]`).forEach(el => {
                    if (isChecking) el.classList.remove('checked');
                    else el.classList.add('checked');
                });
            }
        });
    });

    // 3. Exercise Checkboxes (Workout)
    document.querySelectorAll('.exercise-checkbox').forEach(cb => {
        cb.addEventListener('click', async () => {
            const planId = cb.dataset.planId;
            const exId = cb.dataset.exId;
            cb.classList.toggle('checked');

            try {
                await fetch('/api/workout-plans/toggle-exercise', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                    body: JSON.stringify({ workout_plan_id: planId, exercise_id: exId })
                });
            } catch (err) {
                console.error('Failed to toggle exercise:', err);
                cb.classList.toggle('checked');
            }
        });
    });

    // 4. Wishlist Checkboxes
    document.querySelectorAll('.wishlist-checkbox').forEach(cb => {
        cb.addEventListener('click', async () => {
            const itemId = cb.dataset.id;
            cb.classList.toggle('checked');
            cb.closest('.task-item')?.classList.toggle('done');

            try {
                await fetch(`/api/wishlist/${itemId}/toggle`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': getCsrfToken() }
                });
            } catch (err) {
                console.error('Failed to toggle wishlist item:', err);
                cb.classList.toggle('checked');
                cb.closest('.task-item')?.classList.toggle('done');
            }
        });
    });

    // --- DASHBOARD (TODAY) LOGIC ---
    const blockForm = document.getElementById('block-form');
    const addBlockBtn = document.getElementById('add-block-btn');
    const deleteBlockBtn = document.getElementById('delete-block-btn');

    if (addBlockBtn) {
        addBlockBtn.addEventListener('click', () => {
            if (blockForm) blockForm.reset();
            const idInput = document.getElementById('block-id');
            if (idInput) idInput.value = '';
            const titleEl = document.getElementById('block-modal-title');
            if (titleEl) titleEl.textContent = 'Add Time Block';
            const submitBtn = document.getElementById('block-submit-btn');
            if (submitBtn) submitBtn.textContent = 'Create Block';
            if (deleteBlockBtn) deleteBlockBtn.style.display = 'none';
            showModal('block-modal');
        });
    }

    document.querySelectorAll('.edit-block-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (blockForm) blockForm.reset();
            const idInput = document.getElementById('block-id');
            const titleInput = document.getElementById('block-title');
            const typeInput = document.getElementById('block-type');
            const startsInput = document.getElementById('block-starts');
            const endsInput = document.getElementById('block-ends');
            const notesInput = document.getElementById('block-notes');

            if (idInput) idInput.value = btn.dataset.id;
            if (titleInput) titleInput.value = btn.dataset.title;
            if (typeInput) typeInput.value = btn.dataset.type;
            const formatTime = (t) => t ? t.substring(0, 5) : '';
            if (startsInput) startsInput.value = formatTime(btn.dataset.starts);
            if (endsInput) endsInput.value = formatTime(btn.dataset.ends);
            if (notesInput) notesInput.value = btn.dataset.notes !== 'null' ? btn.dataset.notes : '';

            const titleEl = document.getElementById('block-modal-title');
            if (titleEl) titleEl.textContent = 'Edit Time Block';
            const submitBtn = document.getElementById('block-submit-btn');
            if (submitBtn) submitBtn.textContent = 'Save Changes';
            if (deleteBlockBtn) deleteBlockBtn.style.display = 'block';
            showModal('block-modal');
        });
    });

    const closeBlockModal = document.getElementById('close-block-modal');
    if (closeBlockModal) closeBlockModal.addEventListener('click', () => hideModal('block-modal'));

    if (blockForm) {
        blockForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(blockForm).entries());
            const res = await fetch('/api/blocks', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                body: JSON.stringify(data)
            });
            if (res.ok) window.location.reload();
        });
    }

    if (deleteBlockBtn) {
        deleteBlockBtn.addEventListener('click', () => {
            const id = document.getElementById('block-id')?.value;
            if (id) {
                askConfirmation('Delete this time block?', async () => {
                    await fetch(`/api/blocks/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': getCsrfToken() }
                    });
                    window.location.reload();
                });
            }
        });
    }

    document.querySelectorAll('.btn-carry').forEach(btn => {
        btn.addEventListener('click', async () => {
            const taskId = btn.dataset.taskId;
            try {
                await fetch(`/api/tasks/${taskId}/carry-over`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': getCsrfToken() }
                });
                btn.closest('.task-item')?.remove();
                updateKPIs();
            } catch (err) { console.error('Failed to carry over task:', err); }
        });
    });

    // Task Edit Modal
    const editTaskForm = document.getElementById('edit-task-form');
    const deleteTaskBtn = document.getElementById('delete-task-btn');

    document.querySelectorAll('.edit-task-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const idInput = document.getElementById('edit-task-id');
            const titleInput = document.getElementById('edit-task-title');
            const blockInput = document.getElementById('edit-task-block-id');
            if (idInput) idInput.value = btn.dataset.id;
            if (titleInput) titleInput.value = btn.dataset.title;
            if (blockInput) blockInput.value = btn.dataset.blockId;
            showModal('task-modal');
        });
    });

    const closeTaskModal = document.getElementById('close-task-modal');
    if (closeTaskModal) closeTaskModal.addEventListener('click', () => hideModal('task-modal'));

    if (editTaskForm) {
        editTaskForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('edit-task-id')?.value;
            if (id) {
                const data = Object.fromEntries(new FormData(editTaskForm).entries());
                await fetch(`/api/tasks/${id}`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                    body: JSON.stringify(data)
                });
                window.location.reload();
            }
        });
    }

    if (deleteTaskBtn) {
        deleteTaskBtn.addEventListener('click', () => {
            const id = document.getElementById('edit-task-id')?.value;
            if (id) {
                askConfirmation('Delete this task?', async () => {
                    await fetch(`/api/tasks/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': getCsrfToken() }
                    });
                    window.location.reload();
                });
            }
        });
    }

    const taskForm = document.getElementById('task-form');
    if (taskForm) {
        taskForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(taskForm).entries());
            const res = await fetch('/api/tasks', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                body: JSON.stringify(data)
            });
            if (res.ok) window.location.reload();
        });
    }

    const reviewForm = document.getElementById('review-form');
    if (reviewForm) {
        reviewForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(reviewForm).entries());
            const res = await fetch('/api/reviews', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                body: JSON.stringify(data)
            });
            if (res.ok) window.location.reload();
        });
    }

    const loadSampleBtn = document.getElementById('load-sample-btn');
    if (loadSampleBtn) {
        loadSampleBtn.addEventListener('click', () => {
            askConfirmation('Reset all data and load sample day?', async () => {
                const res = await fetch('/api/load-sample', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': getCsrfToken() }
                });
                if (res.ok) window.location.reload();
            });
        });
    }

    // Time Block Toggle (Hide/Show)
    const toggleBlocksBtn = document.getElementById('toggle-blocks-btn');
    const blocksContent = document.getElementById('time-blocks-content');
    if (toggleBlocksBtn && blocksContent) {
        const isHidden = localStorage.getItem('hide-time-blocks') === 'true';
        if (isHidden) {
            blocksContent.style.display = 'none';
            toggleBlocksBtn.textContent = 'Show';
        }

        toggleBlocksBtn.addEventListener('click', () => {
            const currentlyHidden = blocksContent.style.display === 'none';
            if (currentlyHidden) {
                blocksContent.style.display = 'block';
                toggleBlocksBtn.textContent = 'Hide';
                localStorage.setItem('hide-time-blocks', 'false');
            } else {
                blocksContent.style.display = 'none';
                toggleBlocksBtn.textContent = 'Show';
                localStorage.setItem('hide-time-blocks', 'true');
            }
        });
    }

    function updateKPIs() {
        const items = document.querySelectorAll('.task-item');
        if (items.length === 0) return;
        const total = items.length;
        const done = document.querySelectorAll('.task-item.done').length;
        const activeTasksEl = document.getElementById('kpi-active-tasks');
        const completionEl = document.getElementById('kpi-completion');
        if (activeTasksEl) activeTasksEl.textContent = total - done;
        if (completionEl) completionEl.textContent = Math.round((done / total) * 100) + '%';
    }

    // --- SAVINGS TRACKER LOGIC ---
    const txForm = document.getElementById('transaction-form');
    const addTxBtn = document.getElementById('add-transaction-btn');
    const deleteTxBtn = document.getElementById('delete-tx-btn');

    if (addTxBtn) {
        addTxBtn.addEventListener('click', () => {
            if (txForm) txForm.reset();
            const idInput = document.getElementById('tx-id');
            if (idInput) idInput.value = '';
            const titleEl = document.getElementById('tx-modal-title');
            if (titleEl) titleEl.textContent = 'Add Transaction';
            const submitBtn = document.getElementById('tx-submit-btn');
            if (submitBtn) submitBtn.textContent = 'Save Transaction';
            if (deleteTxBtn) deleteTxBtn.style.display = 'none';
            showModal('transaction-modal');
        });
    }

    document.querySelectorAll('.edit-transaction-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (txForm) txForm.reset();
            const idInput = document.getElementById('tx-id');
            const typeInput = document.getElementById('tx-type');
            const amountInput = document.getElementById('tx-amount');
            const categoryInput = document.getElementById('tx-category');
            const descInput = document.getElementById('tx-description');
            const dateInput = document.getElementById('tx-date');

            if (idInput) idInput.value = btn.dataset.id;
            if (typeInput) typeInput.value = btn.dataset.type;
            if (amountInput) amountInput.value = btn.dataset.amount;
            if (categoryInput) categoryInput.value = btn.dataset.category;
            if (descInput) descInput.value = btn.dataset.description !== 'null' ? btn.dataset.description : '';
            if (dateInput) dateInput.value = btn.dataset.date;

            const titleEl = document.getElementById('tx-modal-title');
            if (titleEl) titleEl.textContent = 'Edit Transaction';
            const submitBtn = document.getElementById('tx-submit-btn');
            if (submitBtn) submitBtn.textContent = 'Save Changes';
            if (deleteTxBtn) deleteTxBtn.style.display = 'block';
            showModal('transaction-modal');
        });
    });

    const closeTxModal = document.getElementById('close-tx-modal');
    if (closeTxModal) closeTxModal.addEventListener('click', () => hideModal('transaction-modal'));

    if (txForm) {
        txForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(txForm).entries());
            await fetch('/api/transactions', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                body: JSON.stringify(data)
            });
            window.location.reload();
        });
    }

    if (deleteTxBtn) {
        deleteTxBtn.addEventListener('click', () => {
            const id = document.getElementById('tx-id')?.value;
            if (id) {
                askConfirmation('Delete this transaction?', async () => {
                    await fetch(`/api/transactions/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': getCsrfToken() }
                    });
                    window.location.reload();
                });
            }
        });
    }

    const wishForm = document.getElementById('wishlist-form');
    const addWishBtn = document.getElementById('add-wishlist-btn');
    const deleteWishBtn = document.getElementById('delete-wish-btn');

    if (addWishBtn) {
        addWishBtn.addEventListener('click', () => {
            if (wishForm) wishForm.reset();
            const idInput = document.getElementById('wish-id');
            if (idInput) idInput.value = '';
            const titleEl = document.getElementById('wish-modal-title');
            if (titleEl) titleEl.textContent = 'Add Wishlist Item';
            const submitBtn = document.getElementById('wish-submit-btn');
            if (submitBtn) submitBtn.textContent = 'Save Item';
            if (deleteWishBtn) deleteWishBtn.style.display = 'none';
            showModal('wishlist-modal');
        });
    }

    document.querySelectorAll('.edit-wishlist-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (wishForm) wishForm.reset();
            const idInput = document.getElementById('wish-id');
            const titleInput = document.getElementById('wish-title');
            const priceInput = document.getElementById('wish-price');
            const priorityInput = document.getElementById('wish-priority');

            if (idInput) idInput.value = btn.dataset.id;
            if (titleInput) titleInput.value = btn.dataset.title;
            if (priceInput) priceInput.value = btn.dataset.price;
            if (priorityInput) priorityInput.value = btn.dataset.priority;

            const titleEl = document.getElementById('wish-modal-title');
            if (titleEl) titleEl.textContent = 'Edit Wishlist Item';
            const submitBtn = document.getElementById('wish-submit-btn');
            if (submitBtn) submitBtn.textContent = 'Save Changes';
            if (deleteWishBtn) deleteWishBtn.style.display = 'block';
            showModal('wishlist-modal');
        });
    });

    const closeWishModal = document.getElementById('close-wish-modal');
    if (closeWishModal) closeWishModal.addEventListener('click', () => hideModal('wishlist-modal'));

    if (wishForm) {
        wishForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(wishForm).entries());
            await fetch('/api/wishlist', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                body: JSON.stringify(data)
            });
            window.location.reload();
        });
    }

    if (deleteWishBtn) {
        deleteWishBtn.addEventListener('click', () => {
            const id = document.getElementById('wish-id')?.value;
            if (id) {
                askConfirmation('Delete this wishlist item?', async () => {
                    await fetch(`/api/wishlist/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': getCsrfToken() }
                    });
                    window.location.reload();
                });
            }
        });
    }

    // --- HABIT TRACKER LOGIC ---
    const habitModal = document.getElementById('habit-modal');
    const closeHabitModal = document.getElementById('close-habit-modal');
    const habitForm = document.getElementById('habit-form');
    const addHabitBtn = document.getElementById('add-habit-btn');

    if (addHabitBtn) {
        addHabitBtn.addEventListener('click', () => {
            if (habitForm) habitForm.reset();
            const idInput = document.getElementById('habit-modal-id');
            if (idInput) idInput.value = '';
            const titleEl = document.getElementById('habit-modal-title');
            if (titleEl) titleEl.textContent = 'Track New Habit';
            showModal('habit-modal');
        });
    }

    document.querySelectorAll('.edit-habit-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (habitForm) habitForm.reset();
            const idInput = document.getElementById('habit-modal-id');
            const titleInput = document.getElementById('habit-modal-title-input');
            const targetInput = document.getElementById('habit-modal-target-input');
            const blockInput = document.getElementById('habit-modal-block-id');

            if (idInput) idInput.value = btn.dataset.id;
            if (titleInput) titleInput.value = btn.dataset.title;
            if (targetInput) targetInput.value = btn.dataset.target !== 'null' ? btn.dataset.target : '';
            if (blockInput) blockInput.value = btn.dataset.blockId !== 'null' ? btn.dataset.blockId : '';
            
            const titleEl = document.getElementById('habit-modal-title');
            if (titleEl) titleEl.textContent = 'Edit Habit';
            showModal('habit-modal');
        });
    });

    if (closeHabitModal) closeHabitModal.addEventListener('click', () => hideModal('habit-modal'));

    if (habitForm) {
        habitForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(habitForm).entries());
            try {
                await fetch('/api/habits', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                    body: JSON.stringify(data)
                });
                window.location.reload();
            } catch (err) { console.error('Failed to add habit:', err); }
        });
    }

    document.querySelectorAll('.delete-habit-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            askConfirmation('Delete this habit?', async () => {
                try {
                    await fetch(`/api/habits/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': getCsrfToken() }
                    });
                    window.location.reload();
                } catch (err) { console.error('Failed to delete habit:', err); }
            });
        });
    });

    // --- WORKOUT PLANNER LOGIC ---
    const addExForm = document.getElementById('add-ex-form');
    document.querySelectorAll('.add-ex-to-plan-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const exIdInput = document.getElementById('selected-ex-id');
            const exTitleEl = document.getElementById('selected-ex-title');
            if (exIdInput) exIdInput.value = btn.dataset.exId;
            if (exTitleEl) exTitleEl.textContent = `Adding: ${btn.dataset.exTitle}`;
            showModal('add-ex-modal');
        });
    });

    const closeAddExModal = document.getElementById('close-add-ex-modal');
    if (closeAddExModal) closeAddExModal.addEventListener('click', () => hideModal('add-ex-modal'));

    if (addExForm) {
        addExForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(addExForm).entries());
            await fetch('/api/workout-plans/exercise', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                body: JSON.stringify(data)
            });
            window.location.reload();
        });
    }

    // --- ADVANCED FEATURES LOGIC ---
    const quickNote = document.getElementById('quick-note');
    if (quickNote) {
        quickNote.addEventListener('keypress', async (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                const content = quickNote.value.trim();
                if (!content) return;
                
                try {
                    await fetch('/api/notes', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                        body: JSON.stringify({ content })
                    });
                    window.location.reload();
                } catch (err) { console.error(err); }
            }
        });
    }

    document.querySelectorAll('.delete-note-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            await fetch(`/api/notes/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': getCsrfToken() }
            });
            window.location.reload();
        });
    });

    const budgetBtn = document.getElementById('set-budget-btn');
    const budgetForm = document.getElementById('budget-form');
    if (budgetBtn) budgetBtn.addEventListener('click', () => showModal('budget-modal'));
    const closeBudgetModal = document.getElementById('close-budget-modal');
    if (closeBudgetModal) closeBudgetModal.addEventListener('click', () => hideModal('budget-modal'));

    if (budgetForm) {
        budgetForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(budgetForm).entries());
            await fetch('/api/user/budget', {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                body: JSON.stringify(data)
            });
            window.location.reload();
        });
    }

    const subBtn = document.getElementById('add-sub-btn');
    const subForm = document.getElementById('sub-form');
    if (subBtn) subBtn.addEventListener('click', () => showModal('sub-modal'));
    const closeSubModal = document.getElementById('close-sub-modal');
    if (closeSubModal) closeSubModal.addEventListener('click', () => hideModal('sub-modal'));

    if (subForm) {
        subForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(subForm).entries());
            await fetch('/api/subscriptions', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                body: JSON.stringify(data)
            });
            window.location.reload();
        });
    }

    document.querySelectorAll('.delete-sub-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            askConfirmation('Delete this subscription?', async () => {
                await fetch(`/api/subscriptions/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': getCsrfToken() }
                });
                window.location.reload();
            });
        });
    });

    // --- CREDENTIALS VAULT LOGIC ---
    const credForm = document.getElementById('credential-form');
    const addCredBtn = document.getElementById('add-credential-btn');
    
    if (addCredBtn) {
        addCredBtn.addEventListener('click', () => {
            if (credForm) credForm.reset();
            const idInput = document.getElementById('cred-id');
            const titleEl = document.getElementById('cred-modal-title');
            const submitBtn = document.getElementById('cred-submit-btn');
            if (idInput) idInput.value = '';
            if (titleEl) titleEl.textContent = 'Add Credential';
            if (submitBtn) submitBtn.textContent = 'Save Credential';
            showModal('credential-modal');
        });
    }

    document.querySelectorAll('.edit-credential-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (credForm) credForm.reset();
            const idInput = document.getElementById('cred-id');
            const platInput = document.getElementById('cred-platform');
            const userInput = document.getElementById('cred-username');
            const passInput = document.getElementById('cred-password');
            const urlInput = document.getElementById('cred-url');
            const notesInput = document.getElementById('cred-notes');

            if (idInput) idInput.value = btn.dataset.id;
            if (platInput) platInput.value = btn.dataset.platform;
            if (userInput) userInput.value = btn.dataset.username !== 'null' ? btn.dataset.username : '';
            if (passInput) passInput.value = btn.dataset.password;
            if (urlInput) urlInput.value = btn.dataset.url !== 'null' ? btn.dataset.url : '';
            if (notesInput) notesInput.value = btn.dataset.notes !== 'null' ? btn.dataset.notes : '';

            const titleEl = document.getElementById('cred-modal-title');
            if (titleEl) titleEl.textContent = 'Edit Credential';
            const submitBtn = document.getElementById('cred-submit-btn');
            if (submitBtn) submitBtn.textContent = 'Save Changes';
            showModal('credential-modal');
        });
    });

    const closeCredModal = document.getElementById('close-cred-modal');
    if (closeCredModal) closeCredModal.addEventListener('click', () => hideModal('credential-modal'));

    if (credForm) {
        credForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(credForm).entries());
            await fetch('/api/credentials', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                body: JSON.stringify(data)
            });
            window.location.reload();
        });
    }

    document.querySelectorAll('.delete-credential-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            askConfirmation('Delete this credential?', async () => {
                await fetch(`/api/credentials/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': getCsrfToken() }
                });
                window.location.reload();
            });
        });
    });

    // 4. Analytics (Charts)
    const ctx = document.getElementById('savings-chart');
    if (ctx && typeof Chart !== 'undefined') {
        fetch('/api/analytics/data')
            .then(res => res.json())
            .then(data => {
                const canvasCtx = ctx.getContext('2d');
                const incomeGradient = canvasCtx.createLinearGradient(0, 0, 0, 300);
                incomeGradient.addColorStop(0, 'rgba(15, 118, 110, 0.15)');
                incomeGradient.addColorStop(1, 'rgba(15, 118, 110, 0.0)');
                const expenseGradient = canvasCtx.createLinearGradient(0, 0, 0, 300);
                expenseGradient.addColorStop(0, 'rgba(239, 68, 68, 0.12)');
                expenseGradient.addColorStop(1, 'rgba(239, 68, 68, 0.0)');
                
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.savings.labels,
                        datasets: [
                            {
                                label: 'Income',
                                data: data.savings.income,
                                borderColor: '#0f766e',
                                backgroundColor: incomeGradient,
                                borderWidth: 3.5,
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: '#ffffff',
                                pointBorderColor: '#0f766e',
                                pointBorderWidth: 2.5,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointHoverBorderWidth: 3
                            },
                            {
                                label: 'Expenses',
                                data: data.savings.expenses,
                                borderColor: '#df3b3b',
                                backgroundColor: expenseGradient,
                                borderWidth: 3.5,
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: '#ffffff',
                                pointBorderColor: '#df3b3b',
                                pointBorderWidth: 2.5,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointHoverBorderWidth: 3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    font: { family: 'Satoshi, sans-serif', size: 13, weight: '500' },
                                    color: '#555550',
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    padding: 20
                                }
                            },
                            tooltip: {
                                backgroundColor: '#1e1e1e',
                                titleFont: { family: 'Satoshi, sans-serif', size: 13, weight: 'bold' },
                                bodyFont: { family: 'Satoshi, sans-serif', size: 12 },
                                padding: 12,
                                cornerRadius: 8,
                                displayColors: true
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f0f0eb', drawBorder: false },
                                ticks: {
                                    font: { family: 'Satoshi, sans-serif', size: 11, weight: '500' },
                                    color: '#888880',
                                    callback: function(value) { return '$' + value; }
                                }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { family: 'Satoshi, sans-serif', size: 11, weight: '500' }, color: '#888880' }
                            }
                        }
                    }
                });
            });
    }

    // 5. Pomodoro Timer
    let pomoInterval = null;
    let pomoSeconds = 25 * 60;
    const pomoDisplay = document.getElementById('pomo-display');
    const pomoStart = document.getElementById('pomo-start');
    const pomoPause = document.getElementById('pomo-pause');
    const pomoReset = document.getElementById('pomo-reset');

    const updatePomoDisplay = () => {
        const mins = Math.floor(pomoSeconds / 60);
        const secs = pomoSeconds % 60;
        if (pomoDisplay) pomoDisplay.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;
    };

    if (pomoStart) {
        pomoStart.addEventListener('click', () => {
            if (pomoInterval) return;
            pomoInterval = setInterval(() => {
                if (pomoSeconds > 0) {
                    pomoSeconds--;
                    updatePomoDisplay();
                } else {
                    clearInterval(pomoInterval);
                    pomoInterval = null;
                    alert('Session complete!');
                }
            }, 1000);
        });
    }

    if (pomoPause) {
        pomoPause.addEventListener('click', () => {
            clearInterval(pomoInterval);
            pomoInterval = null;
        });
    }

    if (pomoReset) {
        pomoReset.addEventListener('click', () => {
            clearInterval(pomoInterval);
            pomoInterval = null;
            pomoSeconds = 25 * 60;
            updatePomoDisplay();
        });
    }
});
