<!DOCTYPE html>
<html>
<head>
    <title itemprop="name">Marketplace Guestbook</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Language" content="en" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <style>
        /* Remove the navbar's default rounded borders */
        .navbar {
            margin-bottom: 20px;
            border-radius: 0;
        }
    </style>
</head>
<body>
    <?php
    if (!isset($user)) {
        $user = Session::getInstance()->getUser();
    }
    ?>
    <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <div class="navbar-header"><?php
                if ($user) {
                    ?><a href="/" class="navbar-brand">Welcome: <?= $user->login; ?></a><?php
                }
                ?>

            </div>
            <div class="collapse navbar-collapse" id="myNavbar">

                <ul class="nav navbar-nav navbar-right"><?php
                    if ($user) {
                        ?><li><a href="/user/logout"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li><?php
                    } else {
                        ?><li><a href="/user/login"><span class="glyphicon glyphicon-log-in"></span> Login</a></li><?php
                    }
                ?></ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid">
        <?= $content; ?>
    </div>
</body>
</html>
