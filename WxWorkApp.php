<?php
// 引入 config.php 读取配置信息
require 'config.php';
// 检查 URL 中是否包含 key 参数
if (!isset($_GET['key']) || $_GET['key'] !== KEY) {
    echo json_encode(['error' => '无效的 key 参数。'], JSON_UNESCAPED_UNICODE);
    exit; // 如果不匹配，终止执行
}
// 获取 access_token
function getAccessToken($corpId, $secret) {
    $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid={$corpId}&corpsecret={$secret}";
    $response = file_get_contents($url);
    $result = json_decode($response, true);
    return isset($result['access_token']) ? $result['access_token'] : null;
}

// 设置响应头为 JSON 格式
header('Content-Type: application/json; charset=UTF-8');

// 检查是否提供了 link 参数
if (isset($_GET['link'])) {
    $link = $_GET['link'];

    // 从数据库中查询 posts 表
    $query = "SELECT id, title, author, link, isok, attributes, replies FROM posts WHERE link = ?";
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
        $post_id = $post['id']; 
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
            if (defined('PUSH') && PUSH === 'WxWorkApp') {
                $accessToken = getAccessToken(CORP_ID, SECRET); // 获取 access_token
                if ($accessToken) {
                    $level = htmlspecialchars($scrapedData['level'], ENT_QUOTES, 'UTF-8');
                    $post_time = htmlspecialchars($scrapedData['post_time'], ENT_QUOTES, 'UTF-8');
                    // 图文消息内容
                    $articles = [
                        [
                            "title" => $title,
                            "description" => "作者: " . $author . ' | ' . $level . ' | ' . $post_time,
                            "url" => WEB . '/neirong.html?id=' . $post_id,  // 点击后跳转的链接
                            "picurl" => PIC_URL  // 图片链接
                        ],
                    ];

                    // 发送图文消息
                    $sendResponse = sendNewsMessage($accessToken, AGENT_ID, $articles);
                    if ($sendResponse['errcode'] == 0) {
                        // 消息发送成功
                    } else {
                        echo json_encode(["code" => 400, "msg" => "图文消息发送失败: " . $sendResponse['errmsg']]);
                    }
                } else {
                    echo json_encode(["code" => 400, "msg" => "获取 access_token 失败"]);
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

// 发送图文消息的函数
function sendNewsMessage($accessToken, $agentId, $articles) {
    $url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token={$accessToken}";

    $data = [
        "touser" => "@all",  // 发送给所有用户
        "msgtype" => "news",  // 消息类型为图文
        "agentid" => $agentId,
        "news" => [
            "articles" => $articles  // 图文消息的数组
        ]
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ],
    ];

    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    return json_decode($response, true);
}
?>
