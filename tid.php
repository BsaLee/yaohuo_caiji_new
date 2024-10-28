<?php
// 引入 config.php 读取配置信息
require 'config.php';

// 设置响应头为 JSON 格式
header('Content-Type: application/json; charset=UTF-8');

// 检查是否提供了 link 参数
if (isset($_GET['link'])) {
    $link = $_GET['link'];

    // 从数据库中查询 posts 表
    $query = "SELECT title, author, link, isok, attributes, replies FROM posts WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $link);
    $stmt->execute();
    $result = $stmt->get_result();

    // 检查是否有结果
    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
        $isokId = $post['isok'];

        // 使用 isok 查询 scraped_posts 表
        $scrapedQuery = "SELECT post_time, content, author_id, level, views, attachments FROM scraped_posts WHERE id = ?";
        $scrapedStmt = $connection->prepare($scrapedQuery);
        $scrapedStmt->bind_param("i", $isokId);
        $scrapedStmt->execute();
        $scrapedResult = $scrapedStmt->get_result();

        // 检查是否有结果
        if ($scrapedResult->num_rows > 0) {
            $scrapedData = $scrapedResult->fetch_assoc();
            
            // 组合最终数据
            $response = [
                'status' => 'success',
                'data' => [
                    'title' => $post['title'],
                    'author' => $post['author'],
                    'link' => $post['link'],
                    'isok' => $post['isok'],
                    'attributes' => $post['attributes'],
                    'replies' => $post['replies'],
                    'post_time' => $scrapedData['post_time'],
                    'content' => $scrapedData['content'],
                    'author_id' => $scrapedData['author_id'],
                    'level' => $scrapedData['level'],
                    'views' => $scrapedData['views'],
                    'attachments' => json_decode($scrapedData['attachments']), // 解码 JSON 格式的附件
                ]
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => '文章未采集完成',
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => '新帖未收录',
        ];
    }

    // 输出 JSON 响应
    echo json_encode($response);
} else {
    // 如果没有提供 link 参数，返回错误信息
    $response = [
        'status' => 'error',
        'message' => '未提供 link 参数。',
    ];
    echo json_encode($response);
}
?>
