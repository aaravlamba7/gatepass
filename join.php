<?php
require_once __DIR__ . '/bootstrap.php';

$id = (string) ($_GET['id'] ?? $_POST['id'] ?? '');
if (!preg_match('/^[a-f0-9]{32}$/', $id)) {
    http_response_code(404);
    exit('This sign-in link is not valid.');
}

try {
    $statement = db()->prepare('SELECT id, name, active FROM sessions WHERE id = ?');
    $statement->execute([$id]);
    $session = $statement->fetch();
} catch (Throwable $error) {
    showDatabaseError($error);
}

if (!$session) {
    http_response_code(404);
    exit('Session not found. Check that you scanned the latest QR code.');
}

$message = '';
$messageClass = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (int) $session['active'] === 1) {
    $studentName = trim((string) ($_POST['student_name'] ?? ''));
    $rollNumber = strtoupper(trim((string) ($_POST['roll_number'] ?? '')));

    if ($studentName === '' || mb_strlen($studentName) > 100 || $rollNumber === '' || mb_strlen($rollNumber) > 30) {
        $message = 'Enter a name (up to 100 characters) and roll number (up to 30 characters).';
        $messageClass = 'error';
    } else {
        try {
            $statement = db()->prepare('INSERT INTO attendance (session_id, student_name, roll_number) VALUES (?, ?, ?)');
            $statement->execute([$id, $studentName, $rollNumber]);
            $message = 'Your sign-in has been recorded.';
            $messageClass = 'success';
        } catch (PDOException $error) {
            if ($error->getCode() === '23000') {
                $message = 'This roll number has already signed in for this session.';
                $messageClass = 'error';
            } else {
                showDatabaseError($error);
            }
        } catch (Throwable $error) {
            showDatabaseError($error);
        }
    }
}

$isClosed = (int) $session['active'] !== 1;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign in · <?= e($session['name']) ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php require __DIR__ . '/header.php'; ?>
  <main class="container narrow">
    <section class="form-card page-card">
      <p class="eyebrow">PARTICIPANT SIGN-IN</p>
      <h1><?= e($session['name']) ?></h1>
      <?php if ($isClosed): ?>
        <p class="notice error">This session is closed. Sign-in is no longer available.</p>
      <?php else: ?>
        <p>Enter your details to record your attendance.</p>
        <?php if ($message !== ''): ?><p class="notice <?= e($messageClass) ?>" role="status"><?= e($message) ?></p><?php endif; ?>
        <?php if ($messageClass !== 'success'): ?>
        <form method="post" action="join.php?id=<?= e($id) ?>">
          <input type="hidden" name="id" value="<?= e($id) ?>">
          <label for="student_name">Your name</label>
          <input id="student_name" name="student_name" type="text" maxlength="100" autocomplete="name" required>
          <label for="roll_number">Roll number</label>
          <input id="roll_number" name="roll_number" type="text" maxlength="30" required>
          <button class="button" type="submit">Sign in</button>
        </form>
        <?php endif; ?>
      <?php endif; ?>
      <p class="small-note">Your roll number can only be used once in this session.</p>
    </section>
  </main>
  <?php require __DIR__ . '/footer.php'; ?>
</body>
</html>
