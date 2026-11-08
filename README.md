# PT2 WEEK 13 — GITHUB REPOSITORY AND TEAM COLLABORATION SUBMISSION
PROJECT TITLE: LikhaSpace — Digital Artwork Marketplace
COURSE: Laravel Blade & Bootstrap Web Development

## 1. PUBLIC REPOSITORY LINK
GitHub Repository URL:
https://github.com/celvicanthony/likhaspace-laravel-static.git

## 2. GROUP MEMBERS & GITHUB USERNAME IDENTIFIERS
- Celvic Anthony M. Borromeo  | GitHub: @celvicanthony (Project Lead / Full-Stack Architect)
- Janessa Mae C. Ramos        | GitHub: @janessamaeramos (UI/UX Designer / Frontend Engineer)
- Lauren Jane Isleta          | GitHub: @laurenjaneisleta (Content Strategist / Design Integrator)
- Vinz Jerby Acaylar          | GitHub: @vinzjerbyacaylar (Quality Assurance / Systems Analyst)

## 3. SUMMARY OF TECHNICAL DELIVERABLES & CONTRIBUTIONS
- Architecture: 3-tier user ecosystem (Clients/Buyers, independent Artists/Creators managing portfolios, and central Admin).
- Authentication & Security: Replaced temporary role-switching previews with editable default credentials and a hidden administrative clearance portal.
- Figma Design System: Enforced strict color identity utilizing Warm Studio Cream (`#FAF7F2`), soft light violet panel cards (`#F2EEF9`), and deep slate background options.
- Route & Connection Integrity: Standardized all internal links and form submissions using dynamic Laravel `route('...')` helpers to eliminate port errors (`ERR_CONNECTION_REFUSED`).
- Version Control & Merges: Successfully merged team feature branches (including Pull Request #3 for artwork modules) and synchronized git commit histories.

## Prerequisites
- PHP >= 8.1
- Composer
- Internet Connection (Required for Bootstrap 5 CDN & Google Fonts assets)

## Installation & Setup
1. Clone the repository:
   ```bash
   git clone https://github.com/celvicanthony/likhaspace-laravel-static.git
   cd likhaspace-laravel-static
   ```

### XAMPP MySQL setup and existing SQLite data

Start **MySQL** in the XAMPP Control Panel. Configure the local `.env` with `DB_CONNECTION=mysql`, `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_DATABASE=likhaspace_local`, and your local MySQL username/password. Create that database in phpMyAdmin, then run:

```bash
php artisan migrate
php artisan storage:link
php artisan db:import-sqlite
```

`db:import-sqlite` copies application records from `database/database.sqlite` into an empty MySQL database. It preserves user IDs/password hashes and moves identity and encrypted artist payment fields into their related tables. It intentionally refuses to merge into non-empty business tables. The command does not copy uploaded files; on the same project folder, existing local storage files remain in place. Keep the same `APP_KEY` so encrypted payment details can still be decrypted.

The schema keeps authentication/role data in `users`, optional legal identity in `user_profiles`, and private artist payout settings in `artist_payment_settings`. Orders and order items keep historical buyer/artwork-name snapshots so receipts remain accurate if a profile or artwork name later changes.

Public artwork and profile images are stored as files under `storage/app/public`; the `public/storage` link makes them available to the web app. If uploads appear missing after copying the project to another machine, copy the `storage/app/public` files too and run `php artisan storage:link`.

## Artist orders, payments, and delivery

After deploying the new order workflow, run `php artisan migrate`. Each artist configures their GCash or InstaPay recipient name, account number/handle, and optional QR code in **Artist Profile → Private payment details**. Mixed-artist carts become separate orders so each artist receives payment directly.

Authenticated buyers see an artist's recipient details and optional QR only at checkout when that artist has an approved artwork in their cart, and on their own order page. They send payment outside the app and upload a receipt for review. Buyers can cancel their own order only before submitting payment proof; submitted or confirmed payments cannot be cancelled in-app. Artists must independently verify funds in their payment account before approving or rejecting the proof in **Artist Studio → Orders & payments**. After approval, upload a delivery file for every artwork in the order; the buyer's private download links unlock when all files are uploaded.

Payout details are encrypted in the database, and QR codes, payment proofs, and delivery files use private storage rather than the public storage directory. Keep the private storage disk persistent and inaccessible from the web root. This is manual payment verification, not a payment gateway; never rely on a screenshot alone or ask buyers for a PIN/password.

Paid orders track a 10% platform commission and 90% artist earnings; sales and public ratings come only from confirmed paid orders and downloaded, verified-buyer reviews. These amounts are accounting allocations, not automatic payouts. PayMongo's hosted checkout and payout APIs require a configured merchant account; automatic marketplace split payouts must be confirmed as enabled for the account before enabling such a flow.
