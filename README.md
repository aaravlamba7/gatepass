# Campus Gate Pass

Campus Gate Pass has separate student, visitor, guard, and admin pages. Students use a saved QR pass. Visitors request access, wait for admin approval, then receive a time-limited QR pass. Guards record entry and exit.

## Visitor flow

1. A visitor enters their name, contact number, host, purpose, requested duration, and a private access code.
2. The admin signs in at `pages/admin.html` and approves or rejects the pending request.
3. When approved, the visitor page displays a QR code and its expiry time.
4. The guard scans the visitor QR in `pages/guard.html`, checks a photo ID, and records Entry or Exit.
5. The guard portal lists approved visitors. A visitor still marked inside after expiry is highlighted as overdue, with their contact number.

## Files

- `index.html` — student sign-in and registration (main entry page).
- `pages/` — visitor request, guard desk, and admin approval pages.
- `css/style.css` — page layout and styling.
- `js/` — browser code and the public Supabase URL/key configuration.
- `database/` — setup and migration SQL files.
- `BACKEND_SETUP.md` — database setup and private PIN instructions.

The project follows the required layout: `index.html`, `pages/`, `css/`, and `js/`.

## Security notes

Visitor contact details are private. The database functions expose pending requests only to an admin session and approved visitor details only to a guard session. The admin passphrase and guard PIN must be configured privately in Supabase SQL Editor; never commit them to GitHub. The project is a classroom prototype and does not verify a visitor against an official identity or host roster.

Do not put a Supabase secret or service-role key in browser files. `js/backend-config.js` must contain only the browser-safe publishable/anon key.
