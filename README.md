# Campus Gate Pass — role-separated demo

The site has separate student and guard pages. A student signs in with their roll number and pass code to see their own QR. The guard page asks for a PIN before showing the scan desk.

## Files

- `index.html` is the student sign-in and personal QR page.
- `guard.html` is the separate guard sign-in and scan page.
- `app.js` contains the sample student list, pass codes, guard PIN, and demo entry/exit behavior.
- `style.css` controls the page layout and colours.

## Demo sign-ins

Ten demo students are included. Their rolls run from `DEMO/2026/001` through `DEMO/2026/010`; their pass codes run from `1001` through `1010` in the same order. The guard PIN is `2468`.
- Guard PIN: `2468`

To add students, add another object to the `students` list near the top of `app.js`. Give each student a unique name, roll number, pass code, and QR token.

QRCode.js loads from a CDN, so QR images need an internet connection. A phone camera scan opens `guard.html` with the matching student token. A USB scanner can type the QR link into the guard form; click **Find student** if it does not submit automatically.

## Important limit

This is still a static classroom demo. The PINs and student pass codes are visible in the public JavaScript file, so they do not provide real security. Scan records are stored in this browser. The student page refreshes when the guard changes a record in another tab of the same browser, but records do not sync between different devices. Real separation and cross-device status updates require server-side authentication and a shared database; static HTML/CSS/JavaScript alone cannot enforce them.
