<?php
session_start();
require_once "../config.php"; // 必要な設定やデータベース接続

$error = '';
$success = '';

// パスワードの強度チェック関数
function check_password_strength($password) {
    $errors = [];
    
    // 最小文字数チェック（8文字以上）
    if (strlen($password) < 8) {
        $errors[] = "パスワードは8文字以上である必要があります。";
    }

    // 大文字のチェック
    if (!preg_match("/[A-Z]/", $password)) {
        $errors[] = "パスワードには少なくとも1つの大文字を含める必要があります。";
    }

    // 数字のチェック
    if (!preg_match("/\d/", $password)) {
        $errors[] = "パスワードには少なくとも1つの数字を含める必要があります。";
    }

    // 記号のチェック
    if (!preg_match("/[\W_]/", $password)) {
        $errors[] = "パスワードには少なくとも1つの記号（例：!@#$%^&*）を含める必要があります。";
    }

    return $errors;
}

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // データベース接続
    $conn = new mysqli("localhost", "root", "", "contact_form_db");
    if ($conn->connect_error) {
        die("接続失敗: " . $conn->connect_error);
    }

    // トークンと有効期限を確認するクエリ
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW() LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    // 有効なトークンが見つかった場合、ユーザー情報を取得
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // POSTリクエストの場合（フォーム送信時）
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            // パスワードが一致するか確認
            if ($password !== $confirm_password) {
                $error = "パスワードが一致しません。再度、確認用パスワードを入力してください。";
            }

            // パスワード強度チェック
            $strength_errors = check_password_strength($password);

            if (!empty($strength_errors)) {
                $error = implode("<br>", $strength_errors);  // エラーメッセージを改行で区切って表示
            }

            // 条件を満たしていればパスワードを更新
            if (empty($error)) {
                // パスワードのハッシュ化
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // パスワード更新、トークンと有効期限をクリア
                $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
                $stmt->bind_param("si", $hashed_password, $user['id']);
                $stmt->execute();

                $success = "パスワードが正常にリセットされました。";
            }
        }
    } else {
        $error = "無効なリセットリンクまたはリンクの有効期限が切れています。";
    }

    $stmt->close();
    $conn->close();
} else {
    $error = "無効なリセットリンクです。";
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>新しいパスワードの設定</title>
    <style>
        /* 入力フォームとボタンの基本スタイル */
        input, button {
            padding: 8px;  /* パディングを少し小さく */
            margin: 8px 0;  /* マージンも少し小さく */
            width: 100%;
            max-width: 250px;  /* 最大幅を小さく設定 */
            font-size: 14px;   /* フォントサイズを少し小さく */
        }

        .password-section {
            margin-bottom: 15px;  /* 各セクションの下の余白を少し小さく */
        }

        .show-password-label {
            display: inline-block;
            margin-top: 5px;
            font-size: 13px;   /* ラベルのフォントサイズを少し小さく */
        }

        button {
            font-size: 15px;  /* ボタンのフォントサイズ */
            padding: 10px;    /* ボタンのパディング */
            width: auto;      /* ボタンの幅を自動調整 */
        }
    </style>
</head>
<body>
    <h2>新しいパスワードの設定</h2>

    <!-- エラーメッセージが表示された場合 -->
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <!-- 成功メッセージが表示された場合 -->
    <?php if ($success): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <!-- エラーがある場合でもフォームが再表示される -->
    <form method="POST" action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>">
        <div class="password-section">
            <label for="password">新しいパスワード:</label>
            <input type="password" id="password" name="password" required value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''; ?>"><br>
        </div>

        <div class="password-section">
            <label for="confirm_password">確認用パスワード:</label>
            <input type="password" id="confirm_password" name="confirm_password" required value="<?php echo isset($_POST['confirm_password']) ? htmlspecialchars($_POST['confirm_password']) : ''; ?>"><br>
        </div>

        <!-- パスワードの表示/非表示切り替え -->
        <div class="password-section">
            <label class="show-password-label">
                <input type="checkbox" id="show_password"> パスワードを表示
            </label>
        </div>

        <button type="submit">パスワードを変更</button>
    </form>

    <script>
        // パスワードの表示/非表示を切り替える
        document.getElementById('show_password').addEventListener('change', function() {
            var passwordField = document.getElementById('password');
            var confirmPasswordField = document.getElementById('confirm_password');
            if (this.checked) {
                passwordField.type = 'text';
                confirmPasswordField.type = 'text';
            } else {
                passwordField.type = 'password';
                confirmPasswordField.type = 'password';
            }
        });
    </script>
</body>
</html>
