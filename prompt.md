# Study + Work Dashboard Prompt

Build a **Study + Work Dashboard** web application with the **same UI direction and feature set** as the current prototype.

## Goal
Create a clean, production-ready dashboard for a user who already follows a rigid schedule that balances:
- Full-time work during the day.
- Evening study sessions.
- Daily review before ending the day.
- Carry-over items for unfinished tasks.

The app should feel like a focused control panel, not a marketing landing page.

## Tech stack
- Frontend: native HTML, CSS, and JavaScript only.
- Backend: Laravel.
- Rendering approach: Laravel Blade + Vite, with native JavaScript handling interactivity.
- Do not use React, Vue, Alpine, Livewire, or jQuery.

## Core product requirements
The dashboard must support:
- Fixed daily time blocks.
- Tasks assigned to specific time blocks.
- Separate work, study, and review task types.
- Daily review notes.
- Focus score for the day.
- Carry-over items that move unfinished tasks into tomorrow.
- A compact dashboard overview with KPI cards.

## UI direction
Recreate the same UI style and information architecture with these characteristics:
- Left sidebar navigation on desktop.
- Sticky top header in the main content area.
- One primary vertical scroll region for dashboard content.
- Four KPI cards across the top: planned blocks, active tasks, completion percentage, carry-over count.
- Main content split into dashboard panels/cards.
- Clean, Swiss-influenced product UI.
- Neutral warm surfaces with a restrained teal primary accent.
- Compact typography suitable for a web app, not large hero-style marketing typography.
- Rounded cards, subtle borders, soft shadows.
- Responsive layout that collapses well on mobile.
- Dark mode and light mode, with a manual theme toggle.

## Exact dashboard sections
Include these sections in the main screen:

### 1. Sidebar
Include:
- Brand area with a simple custom SVG logo.
- App name: `Axis`.
- Subtitle: `Study + work control panel`.
- Navigation items: Today, Weekly load, Review log.
- Carry-over summary list.
- Routine summary showing fixed anchors like work block, dinner reset, evening study, and daily review.

### 2. Header
Include:
- Page title: `Today dashboard`.
- Short subtitle explaining that the screen combines tasks, time blocks, review notes, and next-day carry-over.
- Button to load a sample day.
- Theme toggle button.
- Mobile menu button for small screens.

### 3. KPI row
Show four cards:
- Planned blocks.
- Active tasks.
- Completion.
- Carry-over.

Each card should include:
- Small uppercase label.
- Large metric value with tabular numerals.
- Short supporting helper text.

### 4. Time blocks panel
A large panel that lists the day as timeline-style blocks.
Each block should show:
- Start time.
- End time.
- Block title.
- Short descriptive note.
- Category chip for work, study, or review.
- Progress summary such as `1/3 done`.
- Button to add another block.

### 5. Add task and review panel
This panel should contain:
- A task input field.
- A block selector.
- A task-type selector.
- Save button.
- A daily review textarea.
- A focus score input.
- Save review button.

### 6. Task lanes panel
Display tasks grouped by their block.
Each block lane should show:
- Block title.
- Time range.
- Task list.
- Completion toggle.
- Carry button for each task.
- Empty state when no tasks exist.

### 7. Carry-over panel
Display unfinished tasks that should move to tomorrow.
Include:
- Progress bar or visual indicator.
- Item name.
- Source block.
- Small chip such as `Tomorrow`.
- Friendly empty state when no carry-over items exist.

## Sample content
Seed the interface with realistic example data:
- Morning setup.
- Core work block.
- Afternoon work block.
- Evening study sprint.
- Daily review.
- Example tasks like shipping an API fix, reviewing a pull request, completing a Laravel lesson, and writing daily reflection.

## Functional behavior
Implement the following interactions in native JavaScript:
- Load seeded example data on startup.
- Add a new task.
- Toggle task completion.
- Add a new time block.
- Save a daily review.
- Automatically collect unfinished tasks into carry-over when review is saved.
- Allow manually moving a task into carry-over.
- Update KPI cards dynamically as state changes.
- Support mobile sidebar open/close.
- Support light/dark theme switching.

## Laravel architecture
Use Laravel for persistence and structure.
Create these main parts:

### Routes
Include routes for:
- Dashboard page.
- CRUD for tasks.
- CRUD for time blocks.
- Store daily reviews.
- Toggle task completion.
- Mark task as carry-over.

### Suggested tables
Create migrations for:
- `time_blocks`: id, user_id, title, block_type, starts_at, ends_at, notes, sort_order.
- `tasks`: id, user_id, time_block_id, title, category, status, is_done, carry_over_date, review_note.
- `daily_reviews`: id, user_id, review_date, focus_score, summary, timestamps.

### Relationships
Use:
- User has many time blocks.
- User has many tasks.
- User has many daily reviews.
- TimeBlock has many tasks.
- Task belongs to time block.

### Blade integration
- Render the dashboard with Blade.
- Pass initial state as JSON from Laravel controllers into the page.
- Keep interactivity in native JavaScript.
- Use CSRF-protected fetch requests for task, block, review, and carry-over actions.

## Design system requirements
Follow these visual rules:
- Use a compact app-style type scale.
- Use a warm neutral background and surfaces.
- Use teal as the primary accent.
- Use subtle alpha borders instead of harsh gray borders.
- Use soft shadows and medium rounded corners.
- Use 44px minimum touch targets.
- Keep body text readable and compact.
- Use semantic HTML and visible focus states.
- Respect `prefers-reduced-motion`.
- Maintain good contrast in both light and dark modes.

## Responsive behavior
- Desktop: fixed left sidebar plus main dashboard content.
- Tablet: preserve card structure with reduced columns.
- Mobile: collapse sidebar into an off-canvas panel.
- Stack KPI cards and dashboard panels into a single column on smaller screens.

## Important constraints
- Do not turn this into a landing page.
- Do not add generic SaaS marketing sections.
- Do not use oversized hero text.
- Do not use flashy gradients or AI-style purple/blue glow effects.
- Do not use multiple competing accent colors.
- Do not introduce frameworks beyond Laravel and native JS.
- Keep the app practical, structured, and slightly elegant.

## Deliverables
Generate:
- Laravel routes.
- Migrations.
- Models.
- Controllers.
- Blade view for the dashboard.
- CSS file for the dashboard UI.
- Native JavaScript file for dashboard interactions.
- Seed or sample data setup.

## Quality bar
The result should feel like:
- A disciplined productivity dashboard.
- Built for someone balancing work and evening study on a repeatable schedule.
- Fast, clear, and realistic to extend.
- Visually consistent with the existing prototype UI.
