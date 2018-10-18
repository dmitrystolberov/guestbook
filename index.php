<?php
session_start();
include ('components/Request.php');
include ('components/Session.php');
include ('controllers/BaseController.php');
include ('controllers/UserController.php');
include ('controllers/CommentController.php');
include ('models/DbConnection.php');
include ('models/BaseModel.php');
include ('models/User.php');
include ('models/Comment.php');
include ('models/Image.php');

Request::getInstance()->run();