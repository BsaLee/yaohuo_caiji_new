<?php
// 引入 config.php 读取配置信息
require 'config.php';
// 检查 URL 中是否包含 key 参数
if (!isset($_GET['key']) || $_GET['key'] !== KEY) {
    echo json_encode(['error' => '无效的 key 参数。'], JSON_UNESCAPED_UNICODE);
    exit; // 如果不匹配，终止执行
}
// 构建请求的 URL 和请求头
$url = NEWTIE; // NEWTIE 常量作为 URL
$sidCookie = 'sidyaohuo=' . SID; // 拼接cookie

// 初始化 cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url); // 设置请求的 URL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 将响应存储为字符串而不是直接输出
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: $sidCookie")); // 设置请求头中的 Cookie
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 禁用 SSL 证书验证
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 禁用主机验证

// 执行 cURL 请求
$response = curl_exec($ch);

// 检查是否有错误
// if ($response === false) {
//     echo json_encode(['error' => 'cURL Error: ' . curl_error($ch)], JSON_UNESCAPED_UNICODE);
//     exit;
// }

// 关闭 cURL 资源
curl_close($ch);

// 使用 DOMDocument 和 DOMXPath 解析 HTML
libxml_use_internal_errors(true); // 禁止显示 HTML 解析错误
$dom = new DOMDocument();
$dom->loadHTML($response);
libxml_clear_errors();

$xpath = new DOMXPath($dom);

// 查询帖子信息
$posts = [];
$nodes = $xpath->query("//div[contains(@class, 'listdata')]");

foreach ($nodes as $node) {
    $post = [];
    $aTags = $node->getElementsByTagName('a');
    $brTags = $node->getElementsByTagName('br');

    // 获取标题和链接
    if ($aTags->length > 0) {
        $post['title'] = trim($aTags->item(0)->textContent);
        $post['link'] = $aTags->item(0)->getAttribute('href');
        // 提取链接中的数字
        preg_match('/(\d+)\.html/', $post['link'], $linkMatches);
        $post['link'] = isset($linkMatches[1]) ? (int)$linkMatches[1] : null; // 中间的数字
    }

    // 获取作者昵称
    if ($brTags->length > 0 && $brTags->item(0)->nextSibling) {
        $authorText = $brTags->item(0)->nextSibling->textContent;
        $post['author'] = trim(explode('/', $authorText)[0]);
    } else {
        $post['author'] = '未知作者';
    }

    // 获取回复数和阅读数
    if ($aTags->length > 1) {
        $stats = explode('回/', $aTags->item(1)->textContent);
        $post['replies'] = (int)trim($stats[0]);
        $post['views'] = isset($stats[1]) ? (int)rtrim(trim($stats[1]), '阅') : 0;
    } else {
        $post['replies'] = 0;
        $post['views'] = 0;
    }

    // 获取时间
    $timeNode = $xpath->query(".//span[contains(@class, 'right')]", $node);
    $post['time'] = $timeNode->length > 0 ? trim($timeNode->item(0)->textContent) : '未知时间';

    // 获取属性
    $imgNodes = $xpath->query(".//img", $node);
    $attributes = [];
    foreach ($imgNodes as $imgNode) {
        if ($imgNode->hasAttribute('alt')) {
            $attributes[] = $imgNode->getAttribute('alt'); // 提取 alt 属性
        }
    }
    $post['attributes'] = !empty($attributes) ? implode(', ', $attributes) : null; // 无属性输出 null

    $posts[] = $post;
}

// 写入数据库
foreach ($posts as $post) {
    $title = $post['title'];
    $link = $post['link'];
    $author = $post['author'];
    $replies = $post['replies'];
    $views = $post['views'];
    $time = $post['time'];
    $attributes = $post['attributes'];

    // 使用 INSERT IGNORE 语句
    $insertQuery = "INSERT IGNORE INTO posts (title, link, author, replies, views, time, attributes) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($connection, $insertQuery);
    mysqli_stmt_bind_param($stmt, 'sisisss', $title, $link, $author, $replies, $views, $time, $attributes);
    
    if (!mysqli_stmt_execute($stmt)) {
        echo "插入失败: " . mysqli_error($connection) . "<br>";
    }
}

// 关闭数据库连接
mysqli_close($connection);

// 判断是否输出 JSON 数据
header('Content-Type: application/json; charset=UTF-8');
if (NEWTIE_JSON) {
    if (!empty($posts)) {
        echo json_encode($posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['message' => '未找到任何帖子信息。'], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode(['code' => 200], JSON_UNESCAPED_UNICODE); // 请求成功
}
?>
