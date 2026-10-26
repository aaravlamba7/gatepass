# CampusPass gate-pass prototype

A small PHP and MySQL project for a college gate. Students have a QR pass; a guard checks the student's physical college ID and records entry or exit.

## What each part does

- **HTML** gives the pages their headings, forms, pass cards, and links.
- **CSS** controls the colours, spacing, and layout.
- **PHP** runs on the server. It checks the guard PIN, looks up a scanned QR token, blocks an invalid entry/exit sequence, and saves the scan.
- **MySQL** stores students, their current IN/OUT status, and the time-stamped gate history.
- **QRCode.js** draws the QR image in the browser. A USB QR scanner works like a keyboard: it types the scanned token into the guard form.

## Setup

1. Create a MySQL database and import `schema.sql` using phpMyAdmin.
2. Copy `config.example.php` to `config.php` and fill in the database host, database name, username, password, and a guard PIN.
3. Upload the folder contents to a PHP web host, keeping `config.php` private and never committing it to GitHub.
4. Open `index.php`; use **Guard desk** to try a scan. The example PIN in the config template is `2468`.

The seed records are fictional demo students. Do not put real student names, roll numbers, QR tokens, or database passwords in a public repository.

## Important project limitation

A QR code is a bearer pass and can be copied. To reduce proxy use, the guard must compare the person's face and physical college ID with the name and roll number shown after scanning. This prototype records that the guard checked the ID; it cannot independently prove that the check was honest. A production college system would need college-managed accounts, access controls, and an approved identity-check process.

The student page shows both sample passes only to make a classroom demo easy. A real deployment must authenticate each student and display only their own QR pass. The QR-generation script loads QRCode.js from a CDN, so that script needs an internet connection when the page is first displayed.
