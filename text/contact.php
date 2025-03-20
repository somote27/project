<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <!-- cssフォルダ内のstyle.cssを読み込む -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- ナビゲーションバーを追加 -->
    <nav>
      <ul>
        <li><a href="index.html">ホーム</a></li>
        <li><a href="about.html">About</a></li>
        <li><a href="contact.html">Contact</a></li>
        <li><a href="feed.html">タイムライン</a></li>
      </ul>
    </nav>

    <h1>Contact Us</h1>
    <p>お問い合わせはこちらからどうぞ。</p>

    <!-- お問い合わせフォーム -->
    <div class="contact-form">
        <h2>お問い合わせフォーム</h2>
        <!-- フォームのactionをsubmit_form.phpに設定 -->
        <form id="contact-form" action="../submit_form.php" method="POST">
            <label for="name">お名前:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">メールアドレス:</label>
            <input type="email" id="email" name="email" required>

            <label for="message">メッセージ:</label>
            <textarea id="message" name="message" required></textarea>

            <button type="submit">送信</button>
        </form>
    </div>

    <!-- メッセージを表示する場所 -->
    <div id="form-message">
        <!-- ここはsubmit_form.phpで処理されたメッセージが表示される場所 -->
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = htmlspecialchars($_POST['name']);
            $email = htmlspecialchars($_POST['email']);
            $message = htmlspecialchars($_POST['message']);
            
            $errorMessage = "";

            // バリデーション
            if (empty($name)) {
                $errorMessage .= "名前を入力してください。<br>";
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errorMessage .= "正しいメールアドレスを入力してください。<br>";
            }

            if (empty($message)) {
                $errorMessage .= "メッセージを入力してください。<br>";
            }

            if ($errorMessage) {
                echo "<span style='color: red;'>$errorMessage</span>";
            } else {
                echo "<span style='color: green;'>フォームが正常に送信されました！</span>";
                echo "<p>送信された内容:</p>";
                echo "<strong>名前:</strong> $name<br>";
                echo "<strong>メール:</strong> $email<br>";
                echo "<strong>メッセージ:</strong><br>$message<br>";
            }
        }
        ?>
    </div>
</body>
</html>
