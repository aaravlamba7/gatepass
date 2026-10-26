# Campus Gate Pass — static demo

This classroom prototype uses HTML, CSS, and a little JavaScript to show sample QR passes and record entry or exit.

## Files and roles

- `index.html` contains the page content and forms.
- `style.css` controls layout, colours, and spacing.
- `app.js` displays sample QR codes, checks entry/exit order, and saves scan history in this browser.

QRCode.js loads from a CDN, so the QR images require an internet connection. A USB QR scanner types its token into the guard form like a keyboard.

Open `index.html` directly or publish these files with GitHub Pages or a static web host. No PHP, database, or build tools are needed.

## Explain it simply

HTML is the page structure, CSS is its appearance, and JavaScript handles clicks and form actions. Browser `localStorage` remembers the sample status on this device only.

## Limits

This is a demonstration, not a secure attendance system. Scan history does not sync to another device and can be cleared. A copied QR can still be shown by someone else; the guard must check the physical college ID. A real no-proxy system needs secure college accounts and a protected shared server.
