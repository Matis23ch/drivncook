<?php
session_start();

$_SESSION = [];

session_destroy();

header('Location: /drivncook/public/?page=login');
exit;
