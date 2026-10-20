
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory | LikhaSpace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f7ff; color: #25233a; font-family: Arial, sans-serif; }
        .sidebar { width: 240px; min-height: 100vh; background: white; border-right: 1px solid #eee; position: fixed; padding: 22px 16px; }
        .brand { font-size: 23px; font-weight: bold; color: #201d32; margin-bottom: 30px; }
        .nav-link { color: #68657c; padding: 12px 14px; border-radius: 9px; margin-bottom: 7px; }
        .nav-link:hover, .nav-link.active { background: #6c5ce7; color: white; }
        .main { margin-left: 240px; }
        .topbar { background: white; padding: 18px 28px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
        .content { padding: 28px; }
        .panel { background: white; border: 1px solid #eeeafc; border-radius: 14px; padding: 22px; box-shadow: 0 3px 10px #29204d08; }
        .badge-approved { background: #e1f8ec; color: #218653; }
        .badge-pending { background: #fff0d2; color: #9a6500; }
        .badge-rejected { background: #ffe3e6; color: #bb3445; }
        .table th { color: #77728b; font-size: 13px; font-weight: 600; }
        .table td { vertical-align: middle; padding-top: 15px; padding-bottom: 15px; }
        .artwork-icon { background: #f0edff; color: #6c5ce7; width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 9px; margin-right: 10px; }
        @media(max-width: 768px) {
            .sidebar { width: 170px; }
            .main { margin-left: 170px; }
            .content { padding: 14px; }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="brand">🟣 LikhaSpace</div>
        <hr>
        <nav class="nav flex-column">
            <a class="nav-link" href="{{ url('/explore') }}">Explore Art</a>
            <a class="nav-link" href="{{ url('/view-product') }}">View Product</a>
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
            <a href="{{ url('/admin/dashboard') }}" class="btn btn-outline-primary btn-sm">Dashboard</a>
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
