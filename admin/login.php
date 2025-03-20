<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ログイン</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- 修正したCSSのパス -->
</head>
<body>
    <div class="login-container">
        <h2>管理者ログイン</h2>
        <form method="POST" action="authenticate.php"> <!-- actionのパスも修正 -->
            <label for="username">ユーザー名:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">パスワード:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">ログイン</button>
        </form>
    </div>
</body>
</html>
