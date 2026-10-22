<?php
require_once __DIR__ . '/bootstrap.php';

$id = (string) ($_GET['id'] ?? $_POST['id'] ?? '');
$key = (string) ($_GET['key'] ?? $_POST['key'] ?? '');
if (!preg_match('/^[a-f0-9]{32}$/', $id) || !preg_match('/^[a-f0-9]{48}$/', $key)) {
    http_response_code(403);
    exit('This organizer link is not valid. Keep the organizer link private.');
}

try {
    $statement = db()->prepare('SELECT id, name, admin_key_hash, active, created_at FROM sessions WHERE id = ?');
    $statement->execute([$id]);
    $session = $statement->fetch();
} catch (Throwable $error) {
    showDatabaseError($error);
}

if (!$session || !hash_equals($session['admin_key_hash'], hash('sha256', $key))) {
    http_response_code(403);
    exit('Organizer access denied. Use the private organizer link from when the session was created.');
}

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    try {
        $statement = db()->prepare('SELECT student_name, roll_number, signed_in_at FROM attendance WHERE session_id = ? ORDER BY signed_in_at ASC');
        $statement->execute([$id]);
        $rows = $statement->fetchAll();
    } catch (Throwable $error) {
        showDatabaseError($error);
    }
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="attendance-' . $id . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Name', 'Roll number', 'Sign-in time']);
    foreach ($rows as $row) {
        fputcsv($output, [$row['student_name'], $row['roll_number'], $row['signed_in_at']]);
    }
    fclose($output);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'close' && (int) $session['active'] === 1) {
    try {
        $statement = db()->prepare('UPDATE sessions SET active = 0 WHERE id = ?');
        $statement->execute([$id]);
    } catch (Throwable $error) {
        showDatabaseError($error);
    }
    header('Location: dashboard.php?id=' . urlencode($id) . '&key=' . urlencode($key));
    exit;
}

try {
    $statement = db()->prepare('SELECT student_name, roll_number, signed_in_at FROM attendance WHERE session_id = ? ORDER BY signed_in_at DESC');
    $statement->execute([$id]);
    $attendees = $statement->fetchAll();
} catch (Throwable $error) {
    showDatabaseError($error);
}

$joinUrl = siteUrl('join.php?id=' . rawurlencode($id));
$organizerUrl = siteUrl('dashboard.php?id=' . rawurlencode($id) . '&key=' . rawurlencode($key));
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Attendance · <?= e($session['name']) ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php require __DIR__ . '/header.php'; ?>
  <main class="container">
    <section class="session-header">
      <div>
        <p class="eyebrow">ORGANIZER DASHBOARD</p>
        <h1><?= e($session['name']) ?></h1>
        <p class="status <?= (int) $session['active'] === 1 ? 'open' : 'closed' ?>"><?= (int) $session['active'] === 1 ? 'Sign-in is open' : 'Session closed' ?></p>
      </div>
      <div class="dashboard-actions">
        <a class="button button-secondary" href="dashboard.php?id=<?= e($id) ?>&amp;key=<?= e($key) ?>&amp;export=csv">Download CSV</a>
        <?php if ((int) $session['active'] === 1): ?>
        <form method="post" class="inline-form" onsubmit="return confirm('Close this sign-in session?')">
          <input type="hidden" name="id" value="<?= e($id) ?>">
          <input type="hidden" name="key" value="<?= e($key) ?>">
          <input type="hidden" name="action" value="close">
          <button class="button button-danger" type="submit">Close session</button>
        </form>
        <?php endif; ?>
      </div>
    </section>

    <section class="dashboard-grid">
      <div class="form-card qr-card">
        <h2>Share the QR code</h2>
        <p>Participants can scan it with their phone camera to open the sign-in form.</p>
        <div class="qr-box" id="qrCode" aria-label="Session sign-in QR code"></div>
        <label for="joinUrl">Sign-in link</label>
        <div class="copy-row"><input id="joinUrl" type="text" value="<?= e($joinUrl) ?>" readonly><button class="button button-secondary" type="button" id="copyButton">Copy</button></div>
      </div>

      <div class="form-card attendance-card">
        <div class="attendance-title"><div><h2>Attendance list</h2><p><?= count($attendees) ?> sign-in<?= count($attendees) === 1 ? '' : 's' ?></p></div></div>
        <?php if (!$attendees): ?>
          <p class="empty">No one has signed in yet.</p>
        <?php else: ?>
          <div class="table-wrap"><table><thead><tr><th>Name</th><th>Roll number</th><th>Time</th></tr></thead><tbody>
          <?php foreach ($attendees as $attendee): ?>
            <tr><td><?= e($attendee['student_name']) ?></td><td><?= e($attendee['roll_number']) ?></td><td><?= e(date('d M, g:i a', strtotime($attendee['signed_in_at']))) ?></td></tr>
          <?php endforeach; ?>
          </tbody></table></div>
        <?php endif; ?>
      </div>
    </section>
    <p class="small-note organizer-note">Keep this organizer page private. Anyone with its organizer link can view this session's attendance.</p>
  </main>
  <?php require __DIR__ . '/footer.php'; ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script>
    const qrTarget = document.getElementById('qrCode');
    if (window.QRCode) {
      new QRCode(qrTarget, { text: <?= json_encode($joinUrl, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, width: 190, height: 190 });
    } else {
      qrTarget.textContent = 'QR generator did not load. Copy the sign-in link instead.';
    }
    document.getElementById('copyButton').addEventListener('click', async () => {
      const input = document.getElementById('joinUrl');
      try {
        await navigator.clipboard.writeText(input.value);
        document.getElementById('copyButton').textContent = 'Copied';
      } catch (_) {
        input.select();
        document.execCommand('copy');
      }
    });
  </script>
</body>
</html>
