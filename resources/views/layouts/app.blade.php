<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LikhaSpace - Digital Artwork Marketplace</title>
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts: Poppins & Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-purple: #6C5CE7;
            --bg-cream: #FAF9FF;
            --dark-slate: #1E1E2E;
            --soft-lavender: #EDE7F6;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-cream);
            color: var(--dark-slate);
            min-height: 100vh;
        }

        h1, h2, h3, h4, h5, h6, .brand-font {
            font-family: 'Poppins', sans-serif;
        }

        /* LikhaSpace Custom Utilities */
        .bg-likha-purple { background-color: var(--primary-purple) !important; color: white; }
        .btn-likha-purple { background-color: var(--primary-purple); color: white; border: none; }
        .btn-likha-purple:hover { background-color: #5b4cc4; color: white; }
        .btn-outline-purple { border: 1.5px solid var(--primary-purple); color: var(--primary-purple); }
        .btn-outline-purple:hover { background-color: var(--primary-purple); color: white; }
        .text-purple { color: var(--primary-purple); }
        .bg-card-placeholder { background-color: var(--soft-lavender); }

        /* Persistent Sidebar Styling */
        .sidebar {
            width: 240px;
            background: #ffffff;
            min-height: 100vh;
            border-right: 1px solid #e9ecef;
        }
        .nav-link.active {
            background-color: var(--soft-lavender);
            color: var(--primary-purple) !important;
            font-weight: 600;
            border-radius: 8px;
        }
        .nav-link {
            color: #6c757d;
            border-radius: 8px;
            padding: 10px 16px;
            margin-bottom: 4px;
        }
        .nav-link:hover { color: var(--primary-purple); }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar Navigation -->
    <div class="sidebar p-3 d-none d-md-block position-fixed">
        <a href="{{ route('explore') }}" class="d-flex align-items-center mb-4 text-decoration-none">
            <div class="bg-likha-purple rounded-3 p-2 me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <span class="fw-bold fs-5 brand-font">L</span>
            </div>
            <span class="fs-4 fw-bold brand-font text-dark">LikhaSpace</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('explore') }}" class="nav-link {{ request()->routeIs('explore') ? 'active' : '' }}">
                    <i class="bi bi-compass me-2"></i> Explore Art
                </a>
            </li>
            <li>
                <a href="{{ route('product.show') }}" class="nav-link {{ request()->routeIs('product.show') ? 'active' : '' }}">
                    <i class="bi bi-image me-2"></i> View Product
                </a>
            </li>
            <li class="mt-3 text-muted small fw-bold px-3">ADMIN PORTAL</li>
            <li>
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('admin.inventory') }}" class="nav-link {{ request()->routeIs('admin.inventory') ? 'active' : '' }}">
                    <i class="bi bi-box-seam me-2"></i> Inventory
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="flex-grow-1" style="margin-left: 240px;">
        <!-- Top Navbar for Mobile & Search -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4 py-3 sticky-top">
            <div class="container-fluid">
                <span class="navbar-brand d-md-none fw-bold text-purple brand-font">LikhaSpace</span>
                
                <div class="d-flex ms-auto align-items-center gap-3">
                    <button class="btn btn-outline-purple btn-sm" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i class="bi bi-person me-1"></i> Sign In
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-likha-purple btn-sm">
                        Admin Portal
                    </a>
                </div>
            </div>
        </nav>

        <!-- Dynamic View Content Injection -->
        <main class="p-4">
            @yield('content')
        </main>
    </div>
</div>

<!-- Sign In Modal Include -->
@include('auth.login-modal')

<!-- Bootstrap 5 JS Bundle CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>