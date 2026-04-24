<?php
require 'config/database.php';
require 'config/helpers.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email=?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        redirect('dashboard.php');
    }

    $error = t('wrong_login');
}
?>
<!doctype html>
<html lang="<?= e(current_lang()) ?>">
<head>
    <meta charset="utf-8">
    <title><?= e(t('login')) ?> · CV Forge</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="auth">
    <form method="post" class="card">
        <div style="text-align:right"><?= language_switcher() ?></div>
        <h1><?= e(t('login')) ?></h1>
        <?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
        <label><?= e(t('email')) ?><input type="email" name="email" required></label>
        <label><?= e(t('password')) ?><input type="password" name="password" required></label>
        <button><?= e(t('login')) ?></button>
        <p><?= e(t('no_account')) ?> <a href="register.php"><?= e(t('create_one')) ?></a></p>
    </form>
</body>
</html>
