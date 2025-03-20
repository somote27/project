<?php
session_start();

// ログイン状態の確認
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// データベース接続
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "contact_form_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// ユーザー一覧を取得
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー管理</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="admin_dashboard.php">ダッシュボード</a></li>
                <li><a href="logout.php">ログアウト</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>ユーザー管理</h2>
        <table>
            <tr>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>メッセージ</th>
                <th>アクション</th>
            </tr>

            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['message']) . "</td>";
                    echo "<td><a href='delete_user.php?id=" . $row['id'] . "'>削除</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>ユーザーが見つかりませんでした。</td></tr>";
            }
            ?>
        </table>
    </main>

    <footer>
        <p>&copy; 2025 すべての権利を保有</p>
    </footer>
</body>
</html>

<?php
$conn->close();
?>
