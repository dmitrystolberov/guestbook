<!DOCTYPE html>
<html>
<head>
    <title itemprop="name">Guestbook for Metro Market</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Language" content="en" />
</head>
<body>
    <header><?php
        if (!isset($user)) {
            $user = Session::getInstance()->getUser();
        }
        if ($user) {
            ?><a href="/user/logout">Logout</a><?php
        } else {
            ?><a href="/user/login">Sign in</a><?php
        }
    ?></header>
    <div>
        <?= $content; ?>
    </div>
    <footer>

    </footer>
</body>
</html>
