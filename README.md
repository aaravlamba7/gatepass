# Campus Gate Pass

Campus Gate Pass has separate student, visitor, guard, and admin pages. Students use a saved QR pass. Visitors request access, wait for admin approval, then receive a time-limited QR pass. Guards record entry and exit.

## Visitor flow

1. A visitor enters their name, contact number, host, purpose, requested duration, and a private access code.
2. The admin signs in at admin.html and approves or rejects the pending request.
3. When approved, the visitor page displays a QR code and its expiry time.
4. The guard scans the visitor QR, checks a photo ID, and records Entry or Exit.
5. The guard portal lists approved visitors. A visitor still marked inside after expiry is highlighted as overdue, with their contact number.

## Files

- index.html — student sign-in and registration.
- visitor.html — visitor request, status, and QR pass.
- admin.html — PIN-protected visitor request approval.
- guard.html — student and visitor scanning, activity, and overdue visitor list.
- app.js, visitor-app.js, visitor-guard.js — browser behavior.
- style.css — page layout and styling.
- visitor_pass_migration.sql — adds visitor tables/functions to the existing Supabase project.
- supabase_setup.sql — fresh database setup, including the visitor schema.
- backend-config.js — public Supabase URL and publishable/anon key.
- BACKEND_SETUP.md — database setup, private PIN configuration, and privacy notes.

## Security notes

Visitor contact details are private. The database functions expose pending requests only to an admin session and approved visitor details only to a guard session. The admin passphrase and guard PIN must be configured privately in Supabase SQL Editor; never commit them to GitHub. The project is a classroom prototype and does not verify a visitor against an official identity or host roster.

Do not put a Supabase secret or service-role key in browser files. backend-config.js must contain only the browser-safe publishable/anon key.
