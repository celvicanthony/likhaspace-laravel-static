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

## Artist orders, payments, and delivery

After deploying the new order workflow, run `php artisan migrate`. Each artist configures their GCash or InstaPay recipient name, account number/handle, and optional QR code in **Artist Profile → Private payment details**. Mixed-artist carts become separate orders so each artist receives payment directly.

Buyers see the relevant recipient details only on their own order page, send payment outside the app, and upload a receipt for review. Artists must independently verify funds in their payment account before approving or rejecting the proof in **Artist Studio → Orders & payments**. After approval, upload a delivery file for every artwork in the order; the buyer's private download links unlock when all files are uploaded.

Payout details are encrypted in the database, and QR codes, payment proofs, and delivery files use private storage rather than the public storage directory. Keep the private storage disk persistent and inaccessible from the web root. This is manual payment verification, not a payment gateway; never rely on a screenshot alone or ask buyers for a PIN/password.

Paid orders track a 10% platform commission and 90% artist earnings; sales and public ratings come only from confirmed paid orders and downloaded, verified-buyer reviews. These amounts are accounting allocations, not automatic payouts. PayMongo's hosted checkout and payout APIs require a configured merchant account; automatic marketplace split payouts must be confirmed as enabled for the account before enabling such a flow.

Account profiles store first, middle, and last names separately, with an optional name extension and public display name. Birthdate and gender are optional private account details; public artist profiles and order-facing artist listings use the display name rather than these fields.
