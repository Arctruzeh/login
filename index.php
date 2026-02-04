<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
    </style>
</head>
<body>
    <div class="page-header">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to our site.</h1>
    </div>
    <p>
        <a href="change-password" class="btn btn-warning">Change Your Password</a>
        <a href="logout" class="btn btn-danger">Sign Out of Your Account</a>
    </p>
    <script data-slug="login">
        (function() {
            var slug = document.currentScript.getAttribute('data-slug');
            var link = document.createElement('a');
            link.href = 'https://ajlato.com/projects/' + slug;
            link.innerHTML = '← Back to Portfolio';
            link.style.cssText = 'position: fixed; bottom: 20px; right: 20px; background: #222; color: #fff; padding: 10px 15px; border-radius: 5px; text-decoration: none; font-family: system-ui, sans-serif; font-size: 14px; z-index: 10000; box-shadow: 0 2px 5px rgba(0,0,0,0.2); transition: transform 0.2s;';
            link.onmouseover = function() { link.style.transform = 'scale(1.05)'; };
            link.onmouseout = function() { link.style.transform = 'scale(1)'; };
            document.body.appendChild(link);
        })();
    </script>
</body>
</html>