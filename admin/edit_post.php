<?php
// セッション開始
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    echo "投稿IDが指定されていません。";
    exit;
}

$id = $_GET['id'];

// データベース接続
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "contact_form_db";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// 投稿を取得するクエリ
$sql = "SELECT * FROM posts WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "投稿が見つかりませんでした。";
    exit;
}

$post = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    // 更新クエリ
    $update_sql = "UPDATE posts SET title = '$title', content = '$content' WHERE id = $id";

    if ($conn->query($update_sql) === TRUE) {
        echo "投稿が更新されました。";
        header("Location: post_management.php");
        exit;
    } else {
        echo "エラー: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>投稿編集</title>
</head>
<body>
    <h2>投稿編集</h2>
    <form method="POST">
        <label for="title">タイトル:</label><br>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required><br><br>

        <label for="content">内容:</label><br>
        <textarea id="content" name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea><br><br>

        <button type="submit">更新</button>
    </form>
</body>
</html>

<?php
$conn->close();
?>
