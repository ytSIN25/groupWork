<?php
// If user is already logged in, redirect to the right dashboard
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: dashboard_admin.php');
    } else {
        header('Location: dashboard_user.php');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LUMIÈRE - Sign In</title>
  <link rel="stylesheet" href="css/base.css?v=5">
  <link rel="stylesheet" href="css/pages/auth.css?v=5">
  <link rel="stylesheet" href="css/global.css?v=5">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  
  <div class="film-grain"></div>
  <div class="page-transition active" id="pageTransition"><span class="trans-logo">LUMIÈRE</span></div>

  <section class="hp-hero">
    <div class="hero-bg" style="background-image:url('assets/images/hero-bg.png');"></div>
    <div class="hero-glow"></div>

    <div class="auth-wrapper scale-in" id="authWrapper">
      <div class="auth-flip-box">

        <!-- =========== LOGIN CARD =========== -->
        <div class="vintage-card auth-card auth-login">
          <div class="corner-dec corner-tl"></div><div class="corner-dec corner-tr"></div><div class="corner-dec corner-bl"></div><div class="corner-dec corner-br"></div>
          <div class="auth-header">
            <img src="assets/images/logo.svg?v=5" alt="LUMIÈRE Logo">
            <p>Welcome Back, Old Friend.</p>
          </div>
          <form id="loginForm">
            <div class="form-group">
              <label>Email Address</label>
              <input type="email" placeholder="enter your email" required>
            </div>
            <div class="form-group">
              <label>Password</label>
              <input type="password" placeholder="enter your password" required>
            </div>
            <button type="submit" class="btn-coral" style="width:100%; margin-top:10px;">Enter the Cinema</button>
          </form>
          <div class="auth-toggle">New patron? <a onclick="document.getElementById('authWrapper').classList.add('register-mode')">Request a Ticket</a></div>
        </div>

        <!-- =========== REGISTER CARD =========== -->
        <div class="vintage-card auth-card auth-register">
          <div class="corner-dec corner-tl"></div><div class="corner-dec corner-tr"></div><div class="corner-dec corner-bl"></div><div class="corner-dec corner-br"></div>
          <div class="auth-header">
            <img src="assets/images/logo.svg?v=5" alt="LUMIÈRE Logo">
            <p>Purchase a Lifetime Pass.</p>
          </div>
          <form id="registerForm">
            <div class="form-group">
              <label>Full Name</label>
              <input type="text" placeholder="your name" required>
            </div>
            <div class="form-group">
              <label>Email Address</label>
              <input type="email" placeholder="your email" required>
            </div>
            <div class="form-group">
              <label>Choose Password</label>
              <input type="password" placeholder="create a password" required>
            </div>
            <button type="submit" class="btn-coral" style="width:100%; margin-top:10px;">Become a Member</button>
          </form>
          <div class="auth-toggle">Already a member? <a onclick="document.getElementById('authWrapper').classList.remove('register-mode')">Return to Lobby</a></div>
        </div>

      </div>
    </div>
  </section>

  <script src="js/main.js?v=5"></script>
  <script>
    // LOGIN - sends credentials to api_login.php
    document.getElementById('loginForm').addEventListener('submit', async e => {
      e.preventDefault();
      const email = e.target.querySelector('input[type="email"]').value.trim();
      const pass  = e.target.querySelector('input[type="password"]').value;

      try {
        const resp = await fetch('api_login.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email: email, password: pass })
        });

        const data = await resp.json();

        if (data.success && data.user) {
          // Server created a PHP session - redirect accordingly
          if (data.user.role === 'admin') {
            triggerPageTransition('dashboard_admin.php');
          } else {
            triggerPageTransition('dashboard_user.php');
          }
        } else {
          Swal.fire({
            title: 'Access Denied',
            text: data.message || 'Credentials not found in the Cinema Registry.',
            icon: 'error',
            background: '#1A1520',
            color: '#F2E8D5',
            confirmButtonColor: '#E8735A'
          });
        }
      } catch (err) {
        console.error('Login Error:', err);
        Swal.fire({
          title: 'Error',
          text: 'Something went wrong connecting to the server.',
          icon: 'warning'
        });
      }
    });

    // REGISTER - sends new user info to api_register.php
    document.getElementById('registerForm').addEventListener('submit', async e => {
      e.preventDefault();
      const name  = e.target.querySelector('input[type="text"]').value.trim();
      const email = e.target.querySelector('input[type="email"]').value.trim();
      const pass  = e.target.querySelector('input[type="password"]').value;

      try {
        const resp = await fetch('api_register.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ name: name, email: email, password: pass })
        });

        const data = await resp.json();

        if (data.success) {
          // Server auto-logged them in via PHP session
          triggerPageTransition('dashboard_user.php');
        } else {
          Swal.fire({
            title: 'Registration Failed',
            text: data.message || 'Could not create your account.',
            icon: 'error',
            background: '#1A1520',
            color: '#F2E8D5',
            confirmButtonColor: '#E8735A'
          });
        }
      } catch (err) {
        console.error('Register Error:', err);
        Swal.fire({
          title: 'Error',
          text: 'Something went wrong connecting to the server.',
          icon: 'warning'
        });
      }
    });
  </script>
</body>
</html>
