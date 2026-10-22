
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LikhaSpace')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --likha-purple: #7057e8;
            --likha-dark: #1d1b68;
            --likha-lavender: #f3efff;
            --likha-lavender-strong: #e4dcff;
            --likha-cream: #fff8f0;
            --likha-border: #ded6ff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #fff;
            color: var(--likha-dark);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }

        h1, h2, h3, h4, h5, h6, .brand-font {
            font-family: 'Poppins', sans-serif;
        }

        a {
            transition: color .18s ease, background .18s ease;
        }

        /* HEADER */
        .site-header {
            min-height: 88px;
            padding: 14px 2.2rem;
            background: var(--likha-cream);
            border: 1px solid #e9e1d9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            text-decoration: none;
            color: #171717;
            font-family: 'Poppins', sans-serif;
            font-size: 1.35rem;
            font-weight: 600;
        }

        .brand:hover {
            color: #171717;
        }

        .brand-mark {
            width: 39px;
            height: 39px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .brand-name span {
            color: var(--likha-purple);
        }

        .signin-button {
            min-width: 114px;
            border: 0;
            border-radius: 28px;
            padding: 10px 24px;
            color: #fff;
            background: var(--likha-purple);
            font-weight: 700;
            transition: .2s ease;
        }

        .signin-button:hover {
            background: #5941d0;
            color: #fff;
        }

        /* PAGE LAYOUT */
        .site-shell {
            display: grid;
            grid-template-columns: 250px minmax(0, 1fr);
            min-height: 610px;
        }

        /* SIDEBAR */
        .site-sidebar {
            background: var(--likha-lavender);
            border-right: 1px solid #e4dcf6;
            padding: 18px 12px 24px;
        }

        .account-block {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 16px 12px;
            margin-bottom: 4px;
            color: var(--likha-dark);
        }

        .account-icon {
            font-size: 2.2rem;
            line-height: 1;
        }

        .account-block .account-name {
            font-size: .78rem;
            font-weight: 800;
        }

        .account-block .account-handle {
            font-size: .7rem;
            color: #5c5794;
        }

        .side-nav {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .side-nav a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 11px 16px;
            border-radius: 14px;
            color: var(--likha-dark);
            text-decoration: none;
            font-weight: 750;
            transition: background .18s ease, color .18s ease;
        }

        .side-nav a i {
            font-size: 1.15rem;
            width: 24px;
            text-align: center;
        }

        .side-nav a:hover,
        .side-nav a.active {
            background: #e0d8f8;
            color: #7057e8;
        }

        /* MAIN CONTENT */
        .site-main {
            min-width: 0;
            padding: 22px 18px 20px;
        }

        /* HOME PAGE STYLES */
        .home-top {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(290px, .9fr);
            gap: 24px;
            align-items: start;
            margin: 0 0 28px;
        }

        .hero-panel {
            padding: 0;
        }

        .hero-title {
            color: var(--likha-dark);
            font-size: clamp(2rem, 3.3vw, 2.75rem);
            font-weight: 800;
            line-height: 1.38;
            letter-spacing: -.8px;
            margin: 0 0 12px;
        }

        .hero-title .accent {
            color: var(--likha-purple);
        }

        .hero-actions {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .discover-button {
            display: inline-flex;
            align-items: center;
            gap: 24px;
            padding: 10px 16px;
            border-radius: 13px;
            color: white;
            background: var(--likha-purple);
            text-decoration: none;
            font-size: 1.15rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .discover-button:hover {
            color: white;
            background: #5b43d1;
        }

        .discover-button i {
            font-size: 1.5rem;
        }

        .artist-count {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--likha-dark);
            font-size: .72rem;
        }

        .avatar-stack {
            display: flex;
            align-items: center;
        }

        .avatar-stack span {
            width: 31px;
            height: 31px;
            border-radius: 50%;
            border: 2px solid #fff;
            margin-left: -9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #7b70df;
            color: #fff;
            font-size: 1rem;
        }

        .avatar-stack span:first-child {
            margin-left: 0;
            background: #8981d5;
        }

        .avatar-stack span:nth-child(2) {
            background: #29256f;
        }

        .artist-count strong {
            display: block;
            font-size: .78rem;
        }

        .section-eyebrow {
            font-size: .72rem;
            color: var(--likha-dark);
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .section-heading {
            font-size: 1.25rem;
            font-weight: 800;
            margin: 0;
        }

        .popular-list {
            margin-top: 7px;
        }

        .popular-artist {
            min-height: 53px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 6px 12px;
            border: 1px solid var(--likha-border);
            text-decoration: none;
            color: var(--likha-dark);
            margin-bottom: 1px;
            background: #fff;
        }

        .popular-artist:first-child {
            border-radius: 4px 4px 0 0;
        }

        .popular-artist:last-child {
            border-radius: 0 0 4px 4px;
        }

        .artist-avatar {
            font-size: 2rem;
            line-height: 1;
            color: var(--likha-dark);
        }

        .popular-artist:nth-child(2) .artist-avatar {
            color: #7957e9;
        }

        .popular-artist:nth-child(3) .artist-avatar,
        .popular-artist:nth-child(4) .artist-avatar {
            color: #817bd7;
        }

        .artist-info {
            min-width: 0;
            flex: 1;
            font-size: .73rem;
            line-height: 1.35;
        }

        .artist-info strong {
            display: block;
            font-weight: 800;
        }

        .artist-arrow {
            font-size: 1.2rem;
        }

        .collections-heading {
            margin: 0 0 18px 14px;
        }

        .collection-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .collection-card {
            min-height: 280px;
            background: #fff;
            border: 1px solid #efedf3;
            border-radius: 17px;
            padding: 17px 20px 16px;
            box-shadow: 0 4px 13px rgba(20, 18, 40, .19);
            display: flex;
            flex-direction: column;
        }

        .collection-card h3 {
            font-size: 1.15rem;
            font-weight: 800;
            margin: 0 0 7px;
        }

        .collection-card p {
            font-size: .72rem;
            line-height: 1.4;
            margin: 0;
            color: #15154d;
        }

        .art-placeholder {
            height: 86px;
            margin: auto 0 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
        }

        .art-tile {
            width: 68px;
            height: 68px;
            border-radius: 8px;
            transform: rotate(-16deg);
            background: var(--likha-dark);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 2px 0 rgba(0, 0, 0, .04);
        }

        .art-tile + .art-tile {
            background: #201e23;
            transform: rotate(15deg);
            margin-left: -8px;
        }

        .collection-bottom {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 9px;
            margin-top: 0;
        }

        .collection-bottom a {
            color: var(--likha-dark);
            font-size: .7rem;
            text-decoration: none;
            white-space: nowrap;
        }

        .collection-bottom a:hover {
            text-decoration: underline;
        }

        .round-arrow {
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: var(--likha-dark);
            color: #fff;
            text-decoration: none;
            font-size: 1.1rem;
        }

        .round-arrow:hover {
            color: #fff;
            background: var(--likha-purple);
        }

        /* FOOTER */
        .site-footer {
            background: #e8e0ff;
            min-height: 130px;
            padding: 14px 5.5%;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            color: var(--likha-dark);
            font-size: .72rem;
        }

        .footer-column strong {
            display: block;
            margin-bottom: 3px;
            font-weight: 800;
        }

        .footer-column a {
            display: block;
            text-decoration: none;
            color: inherit;
            line-height: 1.7;
        }

        .footer-column a:hover {
            text-decoration: underline;
        }

        .footer-social {
            text-align: center;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 17px;
            margin-top: 5px;
            font-size: 1rem;
            color: #111;
        }

        .social-icons .bi-instagram {
            color: #e13b91;
        }

        .social-icons .bi-youtube {
            color: #f00;
        }

        .copyright {
            grid-column: 1 / 2;
            align-self: end;
            margin-top: -18px;
        }

        /* RESPONSIVE DESIGN */
        @media (max-width: 1100px) {
            .collection-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .home-top {
                grid-template-columns: minmax(0, 1fr) minmax(270px, .9fr);
            }

            .site-shell {
                grid-template-columns: 220px minmax(0, 1fr);
            }

            .site-sidebar {
                padding-left: 10px;
                padding-right: 10px;
            }
        }

        @media (max-width: 760px) {
            .site-header {
                padding: 12px 16px;
                min-height: 72px;
            }

            .site-shell {
                display: block;
            }

            .site-sidebar {
                min-height: 0;
                border-right: 0;
                border-bottom: 1px solid #e4dcf6;
                padding: 8px;
            }

            .account-block {
                display: none;
            }

            .side-nav {
                flex-direction: row;
                overflow-x: auto;
                gap: 4px;
            }

            .side-nav a {
                white-space: nowrap;
                padding: 9px 11px;
                font-size: .8rem;
            }

            .side-nav a i {
                width: auto;
            }

            .site-main {
                padding: 20px 14px;
            }

            .home-top {
                grid-template-columns: 1fr;
            }

            .popular-panel {
                margin-top: 8px;
            }

            .collection-grid {
                grid-template-columns: 1fr;
            }

            .collection-card {
                min-height: 255px;
            }

            .site-footer {
                grid-template-columns: 1fr 1fr;
                padding: 18px;
            }

            .footer-social {
                text-align: left;
            }

            .social-icons {
                justify-content: flex-start;
            }

            .copyright {
                grid-column: 1 / -1;
                margin-top: 0;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    <!-- HEADER -->
    <header class="site-header">
        <a href="{{ route('home') }}" class="brand" aria-label="LikhaSpace home">
            <span class="brand-mark" aria-hidden="true">
                <svg viewBox="0 0 42 42" width="39" height="39"
                    fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 5H29C32 5 34 7 34 10V31C34 34 32 36 29 36H10C7 36 5 34 5 31V10C5 7 7 5 10 5Z"
                        stroke="#8C5BFF" stroke-width="3.2"/>
                    <path d="M26 4L37 25C39 29 36 35 31 35H22"
                        stroke="#36BFEF" stroke-width="3.2" stroke-linecap="round"/>
                    <path d="M21 9C21 9 13 22 13 27C13 31 16 34 20 34C24 34 27 31 27 27C27 22 21 9 21 9Z"
                        stroke="#8B5BEF" stroke-width="3" stroke-linejoin="round"/>
                </svg>
            </span>

            <span class="brand-name">Likha<span>Space</span></span>
        </a>

        <button
            type="button"
            class="signin-button"
            data-bs-toggle="modal"
            data-bs-target="#loginModal">
            Sign In
        </button>
    </header>

    <!-- SIDEBAR AND PAGE CONTENT -->
    <div class="site-shell">

        <aside class="site-sidebar">

            <div class="account-block">
                <i class="bi bi-person-circle account-icon"></i>

                <div>
                    <div class="account-name">My Account</div>
                    <div class="account-handle">@username</div>
                </div>
            </div>

            <nav class="side-nav" aria-label="Main navigation">

                <!-- Home opens the home page -->
                <a href="{{ route('home') }}"
                   class="{{ request()->routeIs('home') ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill"></i>
                    Home
                </a>

                <!-- Explore Art opens a separate page -->
                <a href="{{ route('explore') }}"
                   class="{{ request()->routeIs('explore') ? 'active' : '' }}">
                    <i class="bi bi-image"></i>
                    Explore Art
                </a>

                <!-- Categories opens Explore Art without scrolling to Home -->
                <a href="{{ route('explore') }}">
                    <i class="bi bi-grid"></i>
                    Categories
                </a>

                <a href="{{ route('favorites') }}"
   class="{{ request()->routeIs('favorites') ? 'active' : '' }}">
    <i class="bi bi-heart"></i>
    My Favorites
</a>

<a href="{{ route('cart') }}"
   class="{{ request()->routeIs('cart') ? 'active' : '' }}">
    <i class="bi bi-cart3"></i>
    My Cart
</a>


                <a href="#about">
                    <i class="bi bi-info-circle"></i>
                    About
                </a>

                <a href="#"
                   data-bs-toggle="modal"
                   data-bs-target="#loginModal">
                    <i class="bi bi-gear"></i>
                    Settings
                </a>

            </nav>
        </aside>

        <!-- Each page displays its own content here -->
        <main class="site-main">
            @yield('content')
        </main>

    </div>

    <!-- FOOTER -->
    <footer class="site-footer" id="about">

        <div class="footer-column">
            <strong>Quick Links</strong>

            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('explore') }}">Explore Art</a>
            <a href="{{ route('explore') }}">Categories</a>
            <a href="#about">About</a>
        </div>

        <div class="footer-column">
            <strong>Help</strong>

            <a href="#about">FAQ</a>
            <a href="#about">Refund</a>
            <a href="#about">Payment</a>
            <a href="#about">Contacts</a>
        </div>

        <div class="footer-column footer-social">
            <strong>Stay Connected</strong>

            <div class="social-icons" aria-label="Social media links">
                <i class="bi bi-tiktok"></i>
                <i class="bi bi-instagram"></i>
                <i class="bi bi-youtube"></i>
                <i class="bi bi-twitter-x"></i>
            </div>
        </div>

        <div class="copyright">
            © 2026 LikhaSpace. All rights reserved.
        </div>

    </footer>

    <!-- SHARED LOGIN MODAL -->
    @include('auth.login-modal')

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')

</body>
</html>
