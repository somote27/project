<?php
session_start();
require_once "../config.php"; // reCAPTCHAキーを読み込む

// セッションリセット処理
if (isset($_GET['reset']) && $_GET['reset'] === 'true') {
    session_unset();
    session_destroy();
    header("Location: user_login.php");
    exit;
}

// ログイン試行回数の制限
$first_lock_threshold = 5;  // 最初のロック試行回数
$second_lock_threshold = 10; // 2回目のロック試行回数
$first_lock_time = 15 * 60;  // 15分間ロック
$second_lock_time = 30 * 60; // 30分間ロック

// ログイン試行回数をセッションに保存
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// ロック状態のチェック
if ($_SESSION['login_attempts'] >= $first_lock_threshold) {
    $lock_time = $_SESSION['lock_time'] ?? 0;
    if (time() < $lock_time) {
        $minutes_remaining = ceil(($lock_time - time()) / 60);
        die("試行回数が多すぎます。あと $minutes_remaining 分後に再試行してください。");
    } else {
        $_SESSION['login_attempts'] = 0; // ロック解除
    }
}

$error = '';
$show_recaptcha = ($_SESSION['login_attempts'] % 3 == 0 && $_SESSION['login_attempts'] > 0);
$warning_message = '';  // 警告メッセージの変数

// 試行回数が閾値に達していない場合、警告メッセージを表示
if ($_SESSION['login_attempts'] > 0 && $_SESSION['login_attempts'] < $first_lock_threshold) {
    $remaining_attempts = $first_lock_threshold - $_SESSION['login_attempts'];
    $warning_message = "あと $remaining_attempts 回間違えると、一時的にロックされます。";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? null;

    // reCAPTCHAの確認
    if ($show_recaptcha) {
        $verify_url = "https://www.google.com/recaptcha/api/siteverify";
        $data = [
            'secret' => RECAPTCHA_SECRET_KEY,
            'response' => $recaptcha_response
        ];
        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($data)
            ]
        ];
        $context  = stream_context_create($options);
        $result = file_get_contents($verify_url, false, $context);
        $recaptcha_success = json_decode($result)->success ?? false;

        if (!$recaptcha_success) {
            $error = "reCAPTCHAの確認に失敗しました。";
        }
    }

    // reCAPTCHAのエラーがない場合、ユーザー認証を実行
    if (!$error) {
        $conn = new mysqli("localhost", "root", "", "contact_form_db");
        if ($conn->connect_error) die("接続失敗: " . $conn->connect_error);

        // ユーザー情報を取得
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // ログイン成功
                session_regenerate_id(true);
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['login_attempts'] = 0;

                // Remember Me チェック
                if (isset($_POST['remember_me']) && $_POST['remember_me'] == 'on') {
                    // 2週間有効なクッキーにセッションIDを保存
                    setcookie('session_id', session_id(), time() + (60 * 60 * 24 * 14), "/");
                }

                header("Location: user_dashboard.php");
                exit;
            } else {
                // パスワード間違い
                $_SESSION['login_attempts']++;
                if ($_SESSION['login_attempts'] == $first_lock_threshold) {
                    $_SESSION['lock_time'] = time() + $first_lock_time;
                } elseif ($_SESSION['login_attempts'] == $second_lock_threshold) {
                    $_SESSION['lock_time'] = time() + $second_lock_time;
                }
                $error = "パスワードが間違っています。";
            }
        } else {
            // ユーザーが見つからない場合
            $_SESSION['login_attempts']++;
            $error = "ユーザーが見つかりません。";
        }
        $stmt->close();
        $conn->close();
    }
}

// クッキーにセッションIDがある場合、自動ログイン
if (isset($_COOKIE['session_id']) && !isset($_SESSION['user_logged_in'])) {
    // セッションが開始されていない場合にのみ session_start() を呼び出す
    if (session_id() == '') {
        session_id($_COOKIE['session_id']);
        session_start();
    }
    $_SESSION['user_logged_in'] = true;
    // ここで自動ログインのユーザーIDを設定する場合、ユーザー情報をデータベースから取得する処理が必要です
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザーログイン</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="css/style.css">
    <script>
        // パスワード表示状態を保持する関数
        window.onload = function() {
            var passwordField = document.getElementById("password");
            var passwordVisible = localStorage.getItem('password-visible');
            if (passwordVisible === 'true') {
                passwordField.type = "text"; // パスワード可視化状態
            } else {
                passwordField.type = "password"; // パスワード非表示状態
            }
        };

        // パスワード表示/非表示切替（チェックボックス）
        function togglePasswordVisibility() {
            var passwordField = document.getElementById("password");
            var passwordVisible = document.getElementById("password-checkbox").checked;
            if (passwordVisible) {
                passwordField.type = "text";
                localStorage.setItem('password-visible', 'true');
            } else {
                passwordField.type = "password";
                localStorage.setItem('password-visible', 'false');
            }
        }
    </script>
</head>
<body>
    <h2>ユーザーログイン</h2>

    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($warning_message): ?>
        <p style="color: orange;"><?php echo $warning_message; ?></p>
    <?php endif; ?>

    <form method="POST" action="user_login.php">
        <label for="email">メールアドレス:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required><br>

        <label for="password">パスワード:</label>
        <input type="password" id="password" name="password" value="" required><br>

        <!-- パスワードの表示/非表示切替チェックボックス -->
        <label for="password-checkbox">
            <input type="checkbox" id="password-checkbox" onclick="togglePasswordVisibility()"> パスワードを表示
        </label><br>

        <label for="remember_me">ログイン状態を保持する</label>
        <input type="checkbox" id="remember_me" name="remember_me" <?php if (isset($_POST['remember_me'])) echo 'checked'; ?>><br>

        <?php if ($show_recaptcha): ?>
            <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>
        <?php endif; ?>
        <br>
        <button type="submit">ログイン</button>
    </form>

    <a href="user_login.php?reset=true">ログイン試行をリセット</a><br>
    <a href="reset_password_request.php">パスワードを忘れた場合？</a>
</body>
</html>
