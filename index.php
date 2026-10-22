<?php
require_once __DIR__ . '/bootstrap.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>QR Sign-In System</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php require __DIR__ . '/header.php'; ?>
  <main class="container">
    <section class="hero">
      <div>
        <p class="eyebrow">CLASS AND EVENT ATTENDANCE</p>
        <h1>QR Sign-In System</h1>
        <p class="lead">Create a session, share its QR code, and record attendance without passing around a paper sheet.</p>
        <p class="note">Participants scan the code with their phone camera, then enter their name and roll number.</p>
      </div>
      <div class="form-card">
        <h2>Create a session</h2>
        <p>Enter a class or event name to get started.</p>
        <form action="create_session.php" method="post">
          <label for="session_name">Session name</label>
          <input id="session_name" name="session_name" type="text" maxlength="100" placeholder="For example: Web Design Lab" required>
          <button class="button" type="submit">Create session</button>
        </form>
      </div>
    </section>

    <section class="steps-section" id="about">
      <h2>How it works</h2>
      <div class="steps">
        <article class="step"><span>1</span><h3>Create a session</h3><p>The organizer sets up a class or event sign-in.</p></article>
        <article class="step"><span>2</span><h3>Scan the QR code</h3><p>Participants scan it using their phone camera.</p></article>
        <article class="step"><span>3</span><h3>Record attendance</h3><p>Sign-ins are saved to the session attendance list.</p></article>
      </div>
    </section>
  </main>
  <?php require __DIR__ . '/footer.php'; ?>
</body>
</html>
