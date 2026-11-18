<?php
require_once __DIR__ . '/../config/helpers.php';
$_SESSION = [];
session_destroy();
redirect(base_url('admin/login.php'));
