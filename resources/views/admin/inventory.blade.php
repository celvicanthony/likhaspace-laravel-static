
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <title>Inventory | LikhaSpace</title>
    <script>
        (() => {
            const savedTheme = localStorage.getItem('likhaspace-theme');
            document.documentElement.dataset.theme = savedTheme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root, [data-theme="light"] { color-scheme: light; --page: #fff8ef; --surface: #fff; --surface-soft: #f4efff; --ink: #211d4d; --muted: #77738f; --line: #ece6f8; --purple: #7254e8; }
        [data-theme="dark"] { color-scheme: dark; --page: #15131e; --surface: #201d2c; --surface-soft: #2a253a; --ink: #f5f1ff; --muted: #b5aecb; --line: #393449; --purple: #a18aff; }
        .brand-logo-dark { display: none; }
        [data-theme="dark"] .brand-logo-light { display: none; }
        [data-theme="dark"] .brand-logo-dark { display: inline; }
        body { background: var(--page); color: var(--ink); font-family: Arial, sans-serif; }
        h1, h2, h3, h4, h5, h6 { color: var(--ink); }
        .sidebar { width: 240px; min-height: 100vh; background: var(--surface-soft); border-right: 1px solid var(--line); position: fixed; padding: 22px 16px; }
        .brand { margin-bottom: 30px; }
        .nav-link { color: var(--muted); padding: 12px 14px; border-radius: 9px; margin-bottom: 7px; }
        .nav-link:hover, .nav-link.active { background: var(--purple); color: white; }
        .main { margin-left: 240px; }
        .topbar { background: var(--surface); padding: 18px 28px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; gap: 12px; }
        .content { padding: 28px; }
        .panel { background: var(--surface); border: 1px solid var(--line); border-radius: 14px; padding: 22px; box-shadow: 0 3px 10px #29204d08; }
        .badge-approved { background: #e1f8ec; color: #218653; }
        .badge-pending { background: #fff0d2; color: #9a6500; }
        .badge-rejected { background: #ffe3e6; color: #bb3445; }
        .table { --bs-table-bg: var(--surface); --bs-table-color: var(--ink); --bs-table-hover-bg: var(--surface-soft); --bs-table-hover-color: var(--ink); }
        .table th { color: var(--muted); font-size: 13px; font-weight: 600; }
        .table td { vertical-align: middle; padding-top: 15px; padding-bottom: 15px; }
        .artwork-icon { background: var(--surface-soft); color: var(--purple); width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 9px; margin-right: 10px; }
        .theme-toggle { width: 38px; height: 38px; flex: 0 0 38px; border: 1px solid var(--line); border-radius: 50%; background: var(--surface); color: var(--ink); }
        .form-control, .form-select { border-color: var(--line); background-color: var(--surface); color: var(--ink); }
        .form-control::placeholder { color: var(--muted); }
        .text-secondary, .text-muted { color: var(--muted) !important; }
        .btn-primary { border-color: var(--purple); background: var(--purple); }
        .btn-primary:hover { border-color: var(--purple); filter: brightness(.94); background: var(--purple); }
        .btn-outline-primary { border-color: var(--purple); color: var(--purple); }
        .btn-outline-primary:hover { border-color: var(--purple); background: var(--purple); color: #fff; }
        @media(max-width: 768px) {
            .sidebar { position: static; width: 100%; min-height: auto; padding: 12px 14px; border-right: 0; border-bottom: 1px solid var(--line); }
            .brand { margin: 0 0 10px; }
            .brand img { width: 125px !important; }
            .sidebar nav { flex-direction: row !important; overflow-x: auto; }
            .sidebar nav .nav-link { flex: 0 0 auto; margin: 0 4px 0 0; padding: 9px 11px; }
            .sidebar nav .small { display: none; }
            .main { margin-left: 0; }
            .topbar { padding: 14px; }
            .content { padding: 18px 14px; }
        }
        @media(max-width: 575px) {
            .topbar { align-items: flex-start; }
            .topbar h5 { font-size: 1rem; }
            .theme-toggle { width: 34px; height: 34px; flex-basis: 34px; }
            .content { padding: 16px 12px; }
            .panel { padding: 16px; }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="brand"><a href="{{ url('/explore') }}" aria-label="LikhaSpace storefront"><img class="brand-logo-light" src="{{ asset('likhaSpace_Logo.png') }}" alt="LikhaSpace" style="width: 145px; height: auto;"><img class="brand-logo-dark" src="{{ asset('likhaSpace_Logo_Dark.png') }}" alt="" aria-hidden="true" style="width: 145px; height: auto;"></a></div>
        <hr>
        <nav class="nav flex-column">
            <a class="nav-link" href="{{ route('explore') }}">Explore Art</a>
            <a class="nav-link" href="{{ route('product.show') }}">View Product</a>
            <div class="small fw-bold text-secondary mt-4 mb-2 px-2">ADMIN PORTAL</div>
            <a class="nav-link" href="{{ url('/admin/dashboard') }}">▦ Dashboard</a>
            <a class="nav-link active" href="{{ url('/admin/inventory') }}">▣ Inventory</a>
        </nav>
    </aside>

    <main class="main">
        <header class="topbar">
            <div>
                <h5 class="mb-1 fw-bold">Inventory Management</h5>
                <small class="text-secondary">Manage artwork listings and review submissions.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="theme-toggle" data-theme-toggle aria-label="Switch color theme" title="Switch color theme"><i class="bi bi-moon-stars"></i></button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary btn-sm">Dashboard</a>
            </div>
        </header>

        <section class="content">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <h3 class="fw-bold">Artwork Inventory</h3>
                    <p class="text-secondary mb-0">Review artwork information and approval statuses.</p>
                </div>
                <button class="btn btn-primary" type="button" onclick="alert('Connect this button to your artwork creation form later.')">+ Add Artwork</button>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <div class="panel">
                        <div class="text-secondary">All Artworks</div>
                        <h3 class="fw-bold mt-2">128</h3>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="panel">
                        <div class="text-secondary">Pending Approval</div>
                        <h3 class="fw-bold text-warning mt-2">8</h3>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="panel">
                        <div class="text-secondary">Rejected</div>
                        <h3 class="fw-bold text-danger mt-2">3</h3>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="row g-2 mb-3">
                    <div class="col-12 col-md-8">
                        <input id="searchInput" type="text" class="form-control" placeholder="Search artwork or artist...">
                    </div>
                    <div class="col-12 col-md-4">
                        <select id="statusFilter" class="form-select">
                            <option value="">All statuses</option>
                            <option value="approved">Approved</option>
                            <option value="pending">Pending</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="inventoryTable">
                        <thead>
                            <tr>
                                <th>ARTWORK</th>
                                <th>ARTIST</th>
                                <th>PRICE</th>
                                <th>DATE SUBMITTED</th>
                                <th>STATUS</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr data-status="approved">
                                <td><span class="artwork-icon">🎨</span>Dreamscape</td>
                                <td>Maria Santos</td>
                                <td>₱1,500</td>
                                <td>Oct 15, 2026</td>
                                <td><span class="badge badge-approved">Approved</span></td>
                                <td><button class="btn btn-sm btn-outline-secondary" onclick="alert('Artwork details can be connected here.')">View</button></td>
                            </tr>
                            <tr data-status="pending">
                                <td><span class="artwork-icon">🖼️</span>Quiet Horizon</td>
                                <td>Juan Dela Cruz</td>
                                <td>₱2,200</td>
                                <td>Oct 18, 2026</td>
                                <td><span class="badge badge-pending">Pending</span></td>
                                <td>
                                    <button class="btn btn-sm btn-success" onclick="updateStatus(this, 'approved')">Approve</button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="updateStatus(this, 'rejected')">Reject</button>
                                </td>
                            </tr>
                            <tr data-status="rejected">
                                <td><span class="artwork-icon">🌸</span>Floral Memory</td>
                                <td>Angela Reyes</td>
                                <td>₱950</td>
                                <td>Oct 19, 2026</td>
                                <td><span class="badge badge-rejected">Rejected</span></td>
                                <td><button class="btn btn-sm btn-outline-secondary" onclick="alert('Artwork details can be connected here.')">View</button></td>
                            </tr>
                            <tr data-status="pending">
                                <td><span class="artwork-icon">🌌</span>Blue Reverie</td>
                                <td>Carlo Mendoza</td>
                                <td>₱1,800</td>
                                <td>Oct 20, 2026</td>
                                <td><span class="badge badge-pending">Pending</span></td>
                                <td>
                                    <button class="btn btn-sm btn-success" onclick="updateStatus(this, 'approved')">Approve</button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="updateStatus(this, 'rejected')">Reject</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <small class="text-secondary">Sample artworks only. Status changes on this page are temporary and are not saved to a database.</small>
                </div>
            </div>
        </section>
    </main>

    <script>
        document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
            const updateIcon = () => {
                button.innerHTML = document.documentElement.dataset.theme === 'dark' ? '<i class="bi bi-sun"></i>' : '<i class="bi bi-moon-stars"></i>';
            };
            updateIcon();
            button.addEventListener('click', () => {
                const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
                document.documentElement.dataset.theme = nextTheme;
                localStorage.setItem('likhaspace-theme', nextTheme);
                updateIcon();
            });
        });

        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');

        function filterRows() {
            const search = searchInput.value.toLowerCase();
            const status = statusFilter.value;

            document.querySelectorAll('#inventoryTable tbody tr').forEach(row => {
                const matchesText = row.innerText.toLowerCase().includes(search);
                const matchesStatus = !status || row.dataset.status === status;
                row.style.display = matchesText && matchesStatus ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', filterRows);
        statusFilter.addEventListener('change', filterRows);

        function updateStatus(button, newStatus) {
            const row = button.closest('tr');
            row.dataset.status = newStatus;

            const statusCell = row.cells[4];
            const badgeClass = {
                approved: 'badge-approved',
                pending: 'badge-pending',
                rejected: 'badge-rejected'
            };

            statusCell.innerHTML = '<span class="badge ' + badgeClass[newStatus] + '">' +
                newStatus.charAt(0).toUpperCase() + newStatus.slice(1) + '</span>';

            row.cells[5].innerHTML =
                '<button class="btn btn-sm btn-outline-secondary" onclick="alert(\'Artwork details can be connected here.\')">View</button>';

            filterRows();
        }
    </script>
</body>
</html>
