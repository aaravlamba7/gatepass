# Shared backend setup (Supabase)

This project keeps its HTML/CSS/JavaScript frontend on GitHub Pages and uses Supabase Postgres as its shared backend. PHP and Firebase Hosting are not used. The database stores student accounts, guard sessions, current IN/OUT status, and scan history. The student page checks the database every three seconds, so a scan at the guard can update a student's page on another device.

## One-time setup

1. Create a Supabase project in your own account. Save the database password privately; it is not needed in this website.
2. In the project's **SQL Editor**, open `supabase_setup.sql`, paste the whole file, and run it once. It creates the tables, restricted database functions, and ten demo students plus one demo guard.
3. In **Project Settings → API**, copy the Project URL and the **Publishable key** (or legacy `anon` key).
4. Open `backend-config.js` and paste those two public values into `supabaseUrl` and `anonKey`.
5. Commit and push the project files to GitHub. GitHub Pages will continue hosting the frontend; Supabase supplies the shared API/database.

**Never put a `service_role` or secret key in `backend-config.js`.** The browser file is public. The SQL functions are designed to check student/guard sessions and reject direct access to the tables.

## Demo sign-ins

- Student rolls: `DEMO/2026/001` through `DEMO/2026/010`
- Student pass codes: `1001` through `1010` in the same order
- Guard PIN: `2468`

## Add a student

In the Supabase SQL Editor, run a statement like this with a unique roll, pass code, and QR token:

```sql
insert into public.students (name, roll_number, pass_code_hash, qr_token)
values (
  'Student Name',
  'SAU/FET/BTECH/2026/123',
  extensions.crypt('a-new-pass-code', extensions.gen_salt('bf')),
  gen_random_uuid()::text
);
```

The student can then sign in with the roll and pass code; their page generates their QR from the stored unique token.

## What the backend does

- Checks student roll/pass-code sign-in and guard PIN sign-in on the server.
- Gives each successful sign-in a time-limited session.
- Allows only a guard session to look up a QR and record movement.
- Checks the current status before allowing Entry or Exit and writes the scan and status change together.
- Lets the student page read the current status from the shared database on any device.

The QR token is stable in this classroom version, so a screenshot could be copied. Keep the physical college ID check enabled. A production gate system should use stronger accounts, rate limits, expiring QR codes, and institutional review.
