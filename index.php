<?php
session_start();

// Already signed in? Skip the forms.
if (isset($_SESSION['user_id'])) {
    header('Location: homepage.php');
    exit();
}

// One-time message from register.php (a validation error or a success note).
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Open the form the message belongs to; default to sign in.
$activeForm = $flash['form'] ?? 'signin';

/** Print the flash message if it belongs to the given form. */
function showMessage(?array $flash, string $form): void
{
    if ($flash === null || $flash['form'] !== $form) {
        return;
    }
    $type = $flash['type'] === 'success' ? 'success' : 'error';
    echo '<p class="message ' . $type . '" role="alert">'
        . htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8')
        . '</p>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register & Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" id="signup"<?= $activeForm === 'signup' ? '' : ' style="display:none;"' ?>>
      <h1 class="form-title">Register</h1>
      <?php showMessage($flash, 'signup'); ?>
      <form method="post" action="register.php">
        <div class="input-group">
           <i class="fas fa-user"></i>
           <input type="text" name="fName" id="signup-fname" placeholder="First Name" autocomplete="given-name" required>
           <label for="signup-fname">First Name</label>
        </div>
        <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" name="lName" id="signup-lname" placeholder="Last Name" autocomplete="family-name" required>
            <label for="signup-lname">Last Name</label>
        </div>
        <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" id="signup-email" placeholder="Email" autocomplete="email" required>
            <label for="signup-email">Email</label>
        </div>
        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" id="signup-password" placeholder="Password (8+ characters)" autocomplete="new-password" minlength="8" required>
            <label for="signup-password">Password</label>
        </div>
       <input type="submit" class="btn" value="Sign Up" name="signUp">
      </form>
      <p class="or">
        ----------or--------
      </p>
      <div class="icons">
        <i class="fab fa-google"></i>
        <i class="fab fa-facebook"></i>
      </div>
      <div class="links">
        <p>Already Have Account ?</p>
        <button id="signInButton">Sign In</button>
      </div>
    </div>

    <div class="container" id="signIn"<?= $activeForm === 'signin' ? '' : ' style="display:none;"' ?>>
        <h1 class="form-title">Sign In</h1>
        <?php showMessage($flash, 'signin'); ?>
        <form method="post" action="register.php">
          <div class="input-group">
              <i class="fas fa-envelope"></i>
              <input type="email" name="email" id="signin-email" placeholder="Email" autocomplete="email" required>
              <label for="signin-email">Email</label>
          </div>
          <div class="input-group">
              <i class="fas fa-lock"></i>
              <input type="password" name="password" id="signin-password" placeholder="Password" autocomplete="current-password" required>
              <label for="signin-password">Password</label>
          </div>
          <p class="recover">
            <a href="#">Recover Password</a>
          </p>
         <input type="submit" class="btn" value="Sign In" name="signIn">
        </form>
        <p class="or">
          ----------or--------
        </p>
        <div class="icons">
          <i class="fab fa-google"></i>
          <i class="fab fa-facebook"></i>
        </div>
        <div class="links">
          <p>Don't have account yet?</p>
          <button id="signUpButton">Sign Up</button>
        </div>
      </div>
      <script src="script.js"></script>
</body>
</html>
