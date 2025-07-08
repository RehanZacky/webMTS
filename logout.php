<?php
session_start();
session_destroy();
header("Location: secure/user/admin/login.php");
exit;
