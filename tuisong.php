<?php
// 引入 config.php 读取配置信息
require 'config.php';

// 检查 URL 中是否包含 key 参数
if (!isset($_GET['key']) || $_GET['key'] !== KEY) {
    echo json_encode(['error' => '无效的 key 参数。'], JSON_UNESCAPED_UNICODE);
    exit; // 如果不匹配，终止执行
}

// 设置响应头为 JSON 格式
header('Content-Type: application/json; charset=UTF-8');

// 检查是否提供了 link 参数
if (isset($_GET['link'])) {
    $link = $_GET['link'];

    // 从数据库中查询 posts 表
    $query = "SELECT title, author, link, isok, attributes, replies FROM posts WHERE link = ?";
    $stmt = $connection->prepare($query);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => '数据库查询失败。']);
        exit;
    }
    $stmt->bind_param("s", $link);
    $stmt->execute();
    $result = $stmt->get_result();

    // 检查是否有结果
    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
        $isokId = $post['isok'];
        $title = $post['title'];
        $author = $post['author'];
        $views = isset($post['views']) ? $post['views'] : 0; // 如果没有，默认值为0
        $replies = $post['replies'];
        $post_id = $post['isok']; // 假设 isok 用作 post_id
        $attributes = $post['attributes'];

        // 使用 isok 查询 scraped_posts 表
        $scrapedQuery = "SELECT post_time, content, author_id, level, views, attachments FROM scraped_posts WHERE id = ?";
        $scrapedStmt = $connection->prepare($scrapedQuery);
        $scrapedStmt->bind_param("i", $isokId);
        $scrapedStmt->execute();
        $scrapedResult = $scrapedStmt->get_result();

        // 检查是否有结果
        if ($scrapedResult->num_rows > 0) {
            $scrapedData = $scrapedResult->fetch_assoc();
            $content = $scrapedData['content'];

            // 如果 attributes 不为空，则添加提示信息
            if (!empty($attributes)) {
                $content .= '<p>这是一个 "' . htmlspecialchars($attributes, ENT_QUOTES, 'UTF-8') . '" 贴, 更多信息请点击原文链接查看</p>';
            }

            // 组合最终数据
            $response = [
                'status' => 'success',
                'data' => [
                    'title' => $title,
                    'author' => $author,
                    'link' => $post['link'],
                    'isok' => $post['isok'],
                    'attributes' => $attributes,
                    'replies' => $replies,
                    'post_time' => $scrapedData['post_time'],
                    'content' => $content,
                    'author_id' => $scrapedData['author_id'],
                    'level' => $scrapedData['level'],
                    'views' => $scrapedData['views'],
                    'attachments' => json_decode($scrapedData['attachments']),
                ]
            ];

            // 检查 PUSH 常量
            if (defined('PUSH') && PUSH === 'WxPusher') {
                $appToken = WxPusher; // 读取 WxPusher 作为推送密钥

                // 构造推送消息数据，确保所有字符串已转换为UTF-8
                $json_message = json_encode([
                    'appToken' => $appToken,
                    'content' => '<h1>[妖火新帖] ' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1><br/><p style="color:red;">作者: ' . htmlspecialchars($author, ENT_QUOTES, 'UTF-8'). '</p><br/><p>' . $content . '</p>',
                    'summary' => '[YH] ' . mb_substr($title, 0, 20),
                    'contentType' => 2,
                    'topicIds' => [topicIds], // 替换为常量 topicIds
                    'url' => WEB . '/neirong.html?id=' . $post_id, // 修改 URL
                    'verifyPay' => false,
                    'verifyPayType' => 0
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                // 发送数据到指定接口
                $ch = curl_init('https://wxpusher.zjiecode.com/api/send/message');
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json_message);
                $response = curl_exec($ch);

                if ($response === false) {
                    echo "消息发送失败: " . curl_error($ch) . "<br>";
                } else {
                    $response_data = json_decode($response, true);
                    if ($response_data['code'] !== 1000) {
                        echo "消息发送失败: " . htmlspecialchars($response_data['msg'], ENT_QUOTES, 'UTF-8') . "<br>";
                    } else {
                        //echo "消息发送成功<br>";
                    }
                }
            }
        } else {
            $response = [
                'status' => 'error',
                'message' => '未找到相关的 scraped_posts 数据。',
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => '未找到符合条件的 posts 数据。',
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
