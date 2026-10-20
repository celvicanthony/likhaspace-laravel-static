
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | LikhaSpace</title>
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
        .stat-card, .panel { background: white; border: 1px solid #eeeafc; border-radius: 14px; padding: 22px; box-shadow: 0 3px 10px #29204d08; }
        .stat-number { font-size: 30px; font-weight: bold; margin-top: 10px; }
        .purple { color: #6c5ce7; }
        .table th { color: #77728b; font-size: 13px; font-weight: 600; }
        .table td { vertical-align: middle; padding-top: 15px; padding-bottom: 15px; }
        .badge-approved { background: #e1f8ec; color: #218653; }
        .badge-pending { background: #fff0d2; color: #9a6500; }
        .badge-rejected { background: #ffe3e6; color: #bb3445; }
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
            <a class="nav-link active" href="{{ url('/admin/dashboard') }}">▦ Dashboard</a>
            <a class="nav-link" href="{{ url('/admin/inventory') }}">▣ Inventory</a>
        </nav>
    </aside>

    <main class="main">
        <header class="topbar">
            <div>
                <h5 class="mb-1 fw-bold">Admin Dashboard</h5>
                <small class="text-secondary">Welcome to your LikhaSpace management portal.</small>
            </div>
            <a href="{{ url('/explore') }}" class="btn btn-outline-primary btn-sm">View Store</a>
        </header>

        <section class="content">
            <div class="mb-4">
                <h3 class="fw-bold">Dashboard Overview</h3>
                <p class="text-secondary">Monitor sales, listings, and customer orders.</p>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <div class="stat-card h-100">
                        <div class="text-secondary">Total Sales</div>
                        <div class="stat-number purple">₱24,500</div>
                        <small class="text-secondary">Sample display data</small>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="stat-card h-100">
                        <div class="text-secondary">Active Listings</div>
                        <div class="stat-number">128</div>
                        <small class="text-success">Approved artworks</small>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="stat-card h-100">
                        <div class="text-secondary">Pending Orders</div>
                        <div class="stat-number">12</div>
                        <small class="text-warning">Awaiting processing</small>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Recent Transactions</h5>
                    <a href="{{ url('/admin/inventory') }}" class="btn btn-sm btn-outline-primary">Manage Inventory</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ORDER ID</th>
                                <th>CUSTOMER</th>
                                <th>ARTWORK</th>
                                <th>AMOUNT</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#LS-1001</td>
                                <td>Maria Santos</td>
                                <td>Dreamscape</td>
                                <td>₱1,500</td>
                                <td><span class="badge badge-approved">Completed</span></td>
                            </tr>
                            <tr>
                                <td>#LS-1002</td>
                                <td>Juan Dela Cruz</td>
                                <td>Quiet Horizon</td>
                                <td>₱2,200</td>
                                <td><span class="badge badge-pending">Pending</span></td>
                            </tr>
                            <tr>
                                <td>#LS-1003</td>
                                <td>Angela Reyes</td>
                                <td>Floral Memory</td>
                                <td>₱950</td>
                                <td><span class="badge badge-approved">Completed</span></td>
                            </tr>
                            <tr>
                                <td>#LS-1004</td>
                                <td>Carlo Mendoza</td>
                                <td>Blue Reverie</td>
                                <td>₱1,800</td>
                                <td><span class="badge badge-pending">Processing</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <small class="text-secondary">Illustration only: transaction figures are sample data.</small>
            </div>
        </section>
    </main>
</body>
</html>
