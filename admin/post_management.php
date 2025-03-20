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

// 接続チェック
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// 投稿を取得するクエリ
$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>投稿管理</title>
    <link rel="stylesheet" href="css/style.css"> <!-- CSSのリンク追加 -->
</head>
<body>
    <div class="container">
        <h2>投稿管理</h2>
        <a href="create_post.php" class="btn">新しい投稿を作成</a> <!-- 新規投稿のリンク -->
        <table>
            <thead>
                <tr>
                    <th>タイトル</th>
                    <th>作成日時</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    // 投稿が存在する場合
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['title']) . "</td>
                                <td>" . htmlspecialchars($row['created_at']) . "</td>
                                <td>
                                    <a href='edit_post.php?id=" . $row['id'] . "'>編集</a> | 
                                    <a href='delete_post.php?id=" . $row['id'] . "'>削除</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>投稿がありません</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// 接続終了
$conn->close();
?>
