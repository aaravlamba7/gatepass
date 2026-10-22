# QR Sign-In System

A small PHP and MySQL attendance project. An organizer creates a session and gets a private dashboard link plus a QR code. Participants scan the QR code, enter their name and roll number, and their sign-in appears in the shared attendance list. The organizer can close the session and download a CSV.

## Files

- `index.php` - home page and create-session form
- `create_session.php` - creates a session and private organizer link
- `join.php` - public participant sign-in page
- `dashboard.php` - QR code, attendance list, close-session button, and CSV export
- `schema.sql` - database tables
- `config.example.php` - example database settings

## Set up on InfinityFree

1. Create a free InfinityFree account and add a website/subdomain.
2. In its Control Panel, open **MySQL Databases**, create a database, and write down the database name, username, hostname, and password. The MySQL hostname is supplied by InfinityFree; it is not usually `localhost`.
3. Open **phpMyAdmin**, select the new database, choose **Import**, and import `schema.sql` from this project.
4. Make a copy of `config.example.php` named `config.php`. Put the database details into `config.php`.
5. Upload the website PHP and CSS files plus `config.php` into the site's `htdocs` folder using the InfinityFree File Manager or FTP. Do not upload `README.md` or `schema.sql` to `htdocs`.
6. Open the assigned site URL and create a test session. Scan the QR code using a phone to try a sign-in.

Keep `config.php` private. Never upload it to the public GitHub repository or share the database password in chat. `.gitignore` is set to exclude it.

## Put the source on GitHub

Create a public repository, then upload the project files from this folder. Include `schema.sql`, `config.example.php`, `.gitignore`, and this README. Do not include `config.php`.

The two URLs to add to the proposal are:

- **Working website:** the assigned InfinityFree site URL
- **Source code:** the GitHub repository URL

GitHub hosts the source code; InfinityFree runs the PHP website. GitHub Pages cannot run this PHP/MySQL project.
