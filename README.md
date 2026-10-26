# Campus Gate Pass — shared-backend version

The frontend is HTML/CSS/JavaScript hosted on GitHub Pages. Supabase Postgres provides the shared database and server-side functions, so student status and guard scans sync across devices. The project is connected to its Supabase backend; demo accounts are listed below.

## Files

- `index.html` is the student sign-in and QR pass page.
- `guard.html` is the separate guard sign-in and scan page.
- `app.js` calls the shared database functions and refreshes student status every three seconds.
- `backend-config.js` holds only the public Supabase Project URL and publishable key.
- `supabase_setup.sql` creates tables, restricted server-side functions, and ten sample students.
- `BACKEND_SETUP.md` explains the backend design and adding students.
- `style.css` controls layout and colours.

## Demo sign-ins

Rolls `DEMO/2026/001` through `DEMO/2026/010` use pass codes `1001` through `1010`, respectively. The guard PIN is `2468`.

QRCode.js loads from a CDN, so QR images need an internet connection. A phone camera scan opens `guard.html` with the student token. A USB scanner can type the QR link into the guard form.

Do not put Supabase `service_role` or secret keys in the browser files. The QR token is stable for this classroom version, so the guard must check the physical college ID.

