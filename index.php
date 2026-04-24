<?php require 'config/helpers.php'; redirect(empty($_SESSION['user_id']) ? 'login.php' : 'dashboard.php');
