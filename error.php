<?php
// 引入配置文件
include 'config.php';

// 获取 GET 参数
$id = $_GET['id'] ?? null;
$key = $_GET['key'] ?? null;

// 验证 key 是否正确
if ($key !== KEY) { // 假设 KEY 是 config.php 中定义的常量
    echo json_encode(["code" => 400, "msg" => "错误：无效的 key"]);
    exit;
}

// 默认值
$title = '网页标题';
$postTime = '1999-01-01';
$content = '帖子已删除或正在审核';
$author = '路桥';
$authorId = 12817; // 楼主用户ID
$level = '888';
$views = 888; // 阅读量
$attachments = '';

try {
    // 创建数据库连接
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($connection->connect_error) {
        echo json_encode(["code" => 400, "msg" => "数据库连接失败: " . $connection->connect_error]);
        exit;
    }

    // 插入数据到 scraped_posts 表
    $insertQuery = "INSERT INTO scraped_posts (title, post_time, content, author, author_id, level, views, attachments) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($insertQuery);
    if ($stmt === false) {
        echo json_encode(["code" => 400, "msg" => "准备 SQL 语句失败: " . $connection->error]);
        exit;
    }

    // 将附件转换为 JSON 格式
    $attachmentsJson = json_encode($attachments, JSON_UNESCAPED_UNICODE);
    $stmt->bind_param("ssssisss", $title, $postTime, $content, $author, $authorId, $level, $views, $attachmentsJson);

    if (!$stmt->execute()) {
        echo json_encode(["code" => 400, "msg" => "插入数据失败: " . $stmt->error]);
        exit;
    }

    // 获取刚插入的 scraped_posts 表的 ID
    $insertedId = $stmt->insert_id;

    // 更新 posts 表的 isok 列为刚才插入的 ID
    $updateQuery = "UPDATE posts SET isok = ? WHERE id = ?";
    $updateStmt = $connection->prepare($updateQuery);
    if ($updateStmt === false) {
        echo json_encode(["code" => 400, "msg" => "准备更新 SQL 语句失败: " . $connection->error]);
        exit;
    }

    $updateStmt->bind_param("ii", $insertedId, $id);

    if (!$updateStmt->execute()) {
        echo json_encode(["code" => 400, "msg" => "更新 posts 表失败: " . $updateStmt->error]);
        exit;
    }

    echo json_encode(["code" => 200, "msg" => "操作成功"]);

    // 关闭语句和连接
    $stmt->close();
    $updateStmt->close();
    $connection->close();
} catch (Exception $e) {
    echo json_encode(["code" => 400, "msg" => "系统错误: " . $e->getMessage()]);
}
?>
