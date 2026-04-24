<?php
require 'config/database.php';
require 'config/helpers.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        $error = t('register_error');
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO users (name,email,password_hash) VALUES (?,?,?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['name'] = $name;
            redirect('dashboard.php');
        } catch (PDOException $e) {
            $error = t('email_exists');
        }
    }
}
?>
<!doctype html>
<html lang="<?= e(current_lang()) ?>">
<head>
    <meta charset="utf-8">
    <title><?= e(t('register')) ?> · CV Forge</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="auth">
    <form method="post" class="card">
        <div style="text-align:right"><?= language_switcher() ?></div>
        <h1><?= e(t('create_account')) ?></h1>
        <?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
        <label><?= e(t('name')) ?><input name="name" required></label>
        <label><?= e(t('email')) ?><input type="email" name="email" required></label>
        <label><?= e(t('password')) ?><input type="password" name="password" minlength="8" required></label>
        <button><?= e(t('register')) ?></button>
        <p><?= e(t('already_registered')) ?> <a href="login.php"><?= e(t('login')) ?></a></p>
    </form>
</body>
</html>
