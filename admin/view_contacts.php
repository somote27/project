<?php
// セッション開始
session_start();
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

// お問い合わせ内容を取得するクエリ
$sql = "SELECT * FROM contacts ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お問い合わせ内容一覧</title>
</head>
<body>
    <h2>お問い合わせ内容一覧</h2>
    <table border="1">
        <tr>
            <th>名前</th>
            <th>メールアドレス</th>
            <th>メッセージ</th>
            <th>送信日時</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>" . htmlspecialchars($row['message']) . "</td>
                        <td>" . $row['created_at'] . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>お問い合わせはありません。</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
