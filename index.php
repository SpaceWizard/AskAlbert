<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ask-Albert Login</title>
    <link rel='stylesheet prefetch' href='http://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.css'>
    <link rel="stylesheet" href="css/style.css" media="screen" type="text/css" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"> </script>
</head>
<body>
<html lang="en">
<body>
    <div class="container">
        <div class="flat-form">
            <ul class="tabs">
                <li>
                    <a href="#login" class="active">Login</a>
                </li>
                <li>
                    <a href="#register">Register</a>
                </li>
                <li>
                    <a href="#reset">Reset Password</a>
                </li>
            </ul>
            <div id="login" class="form-action show">
                <h1>Login</h1>
                <p>
                    Login to Ask Albert
                </p>
                <form action="home.php">
                    <ul>
                        <li>
                            <input type="text" placeholder="Username" />
                        </li>
                        <li>
                            <input type="password" placeholder="Password" />
                        </li>
                        <li>
                            <input type="submit" value="Login" class="button" />
                        </li>
                    </ul>
                </form>
            </div>
            <!--/#login.form-action-->
            <div id="register" class="form-action hide">
                <h1>Register</h1>
                <p>
                    All the cool kids are talking to Albert. You should do too. Register Now.
                </p>
                <form>
                    <ul>
                        <li>
                            <input type="text" placeholder="Username" />
                        </li>
                        <li>
                            <input type="password" placeholder="Password" />
                        </li>
                        <li>
                            <input type="submit" value="Sign Up" class="button" />
                        </li>
                    </ul>
                </form>
            </div>
            <!--/#register.form-action-->
            <div id="reset" class="form-action hide">
                <h1>Reset Password</h1>
                <p>
                    To reset your password enter your email and we'll send you a link to reset your password.
                </p>
                <form>
                    <ul>
                        <li>
                            <input type="email" placeholder="Email" />
                        </li>
                        <li>
                            <input type="submit" value="Send" class="button" />
                        </li>
                    </ul>
                </form>
            </div>
            <!--/#register.form-action-->
        </div>
    </div>

</body>
</html>

  <script src='http://codepen.io/assets/libs/fullpage/jquery.js'></script>

  <script src="js/index.js"></script>

</body>

</html>