<?php
require 'config/database.php'; require 'config/helpers.php'; require_login();
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('DELETE FROM cvs WHERE id=? AND user_id=?');
$stmt->execute([$id, current_user_id()]);
redirect('dashboard.php');
