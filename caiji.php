<?php
// 引入 config.php 读取配置信息
require 'config.php';
header('Content-Type: application/json');
// 检查 URL 中是否包含 key 参数
if (!isset($_GET['key']) || $_GET['key'] !== KEY) {
    echo json_encode(['error' => '无效的 key 参数。'], JSON_UNESCAPED_UNICODE);
    exit; // 如果不匹配，终止执行
}

// 从数据库中读取 isok 为空的最小 id 的 link
$query = "SELECT id, link FROM posts WHERE isok IS NULL ORDER BY id ASC LIMIT 1";
$result = mysqli_query($connection, $query);

// 检查查询是否成功
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $linkId = $row['id']; // 获取 posts 表中的 id
    $link = $row['link'];
} else {
    echo json_encode(["error" => "未找到符合条件的链接。"]);
    exit;
}

// 获取域名信息
$yuming = YUMING; // 从 config.php 中读取域名常量
$url = "https://$yuming/bbs-$link.html"; // 拼接 URL

// 构建请求的 Cookie
$sidCookie = 'sidyaohuo=' . SID;

// 初始化 cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url); // 设置请求的 URL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 将响应存储为字符串而不是直接输出
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: $sidCookie")); // 设置请求头中的 Cookie
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 禁用 SSL 证书验证
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 禁用主机验证

// 检查 CAIJI_DEBUG 常量
if (defined('CAIJI_DEBUG') && CAIJI_DEBUG) {
    // 打印请求的 URL 和头部信息
    echo "请求的 URL: $url\n";
    echo "请求头部: " . print_r(array("Cookie" => $sidCookie), true) . "\n";
}

// 执行 cURL 请求
$response = curl_exec($ch);

// 检查是否有错误
if ($response === false) {
    echo json_encode(["error" => 'cURL Error: ' . curl_error($ch)]);
    exit;
}

// 如果 CAIJI_DEBUG 为 true，打印响应内容
if (defined('CAIJI_DEBUG') && CAIJI_DEBUG) {
    echo "响应内容: " . $response . "\n";
}

// 关闭 cURL 资源
curl_close($ch);

// 加载 DOM 解析库
libxml_use_internal_errors(true);
$dom = new DOMDocument();
@$dom->loadHTML($response);
libxml_clear_errors();

// 提取所需信息
$data = [];

// 提取网页标题
$titleNodes = $dom->getElementsByTagName('title');
$title = $titleNodes->length > 0 ? trim($titleNodes->item(0)->textContent) : '';

// 检查响应内容是否包含"您没有权限操作，或者帖子已删除"或"正在审核中"
if (strpos($response, '您没有权限操作，或者帖子已删除') !== false || strpos($response, '正在审核中') !== false) {
    // 构建 URL
    $url = WEB . '/error.php?id=' . urlencode($linkId) . '&key=' . urlencode(KEY);

    // 执行 GET 请求
    file_get_contents($url);
    echo json_encode(["code" => 400, "msg" => "帖子已删除或正在审核中"]);

    exit; // 退出脚本，避免后续逻辑
}
if (strpos($response, '找不到') !== false || strpos($response, '正在审核中') !== false) {
    // 构建 URL
    $url = WEB . '/error.php?id=' . urlencode($linkId) . '&key=' . urlencode(KEY);

    // 执行 GET 请求
    file_get_contents($url);
    echo json_encode(["code" => 400, "msg" => "帖子已删除或正在审核中"]);

    exit; // 退出脚本，避免后续逻辑
}

// 提取发帖时间
$xpath = new DOMXPath($dom);
$timeNodes = $xpath->query("//span[contains(@class, 'DateAndTime')]");
$postTime = $timeNodes->length > 0 ? trim($timeNodes->item(0)->textContent) : '';

// 提取帖子内容
$contentNodes = $xpath->query("//div[contains(@class, 'bbscontent')]");
$content = '';
if ($contentNodes->length > 0) {
    $contentHtml = $dom->saveHTML($contentNodes->item(0));
    preg_match('/<!--listS-->(.*?)<!--listE-->/', $contentHtml, $matches);
    $content = isset($matches[1]) ? trim($matches[1]) : '';
}

// 提取楼主昵称和用户ID
$authorNodes = $xpath->query("//span[contains(@class, 'louzhunicheng')]/a");
$author = $authorNodes->length > 0 ? trim($authorNodes->item(0)->textContent) : '';

$authorId = '未知';
if ($authorNodes->length > 0) {
    $authorLink = $authorNodes->item(0)->getAttribute('href');
    if (preg_match('/touserid=(\d+)/', $authorLink, $matches)) {
        $authorId = $matches[1];
    }
}

// 提取楼主等级
$levelNodes = $xpath->query("//span[contains(@class, 'dengji')]");
$level = $levelNodes->length > 0 ? trim($levelNodes->item(0)->textContent) : '未知';

// 提取阅读量
$viewsNodes = $xpath->query("//span[contains(@class, 'yueduliang')]");
$views = $viewsNodes->length > 0 ? trim(str_replace(['(阅', ')'], '', $viewsNodes->item(0)->textContent)) : '未知';

// 提取下载文件信息
$attachmentNodes = $xpath->query("//div[contains(@class, 'attachmentinfo')]");
$attachments = [];
foreach ($attachmentNodes as $node) {
    $attachmentNameNode = $xpath->query(".//span[contains(@class, 'attachmentname')]", $node);
    $downloadLinkNode = $xpath->query(".//span[contains(@class, 'downloadurl')]/a", $node);
    if ($attachmentNameNode->length > 0 && $downloadLinkNode->length > 0) {
        $attachmentName = trim($attachmentNameNode->item(0)->textContent);
        $downloadLink = $downloadLinkNode->item(0)->getAttribute('href');
        $attachments[] = [
            'name' => $attachmentName,
            'link' => "https://$yuming$downloadLink" // 拼接链接
        ];
    }
}

// 组装数据
$data = [
    '网页标题' => $title,
    '发帖时间' => $postTime,
    '帖子内容' => $content,
    '楼主用户名' => "$author (用户ID: $authorId)",
    '楼主等级' => $level,
    '阅读量' => $views,
    '附件' => $attachments,
];

// 判断输出格式
if (CAIJI_JSON) {
    // 输出 JSON 格式数据
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data);
} else {
    // 输出请求成功的 JSON
    // echo json_encode(["code" => 200]);
}

// 插入数据到 scraped_posts 表
$insertQuery = "INSERT INTO scraped_posts (title, post_time, content, author, author_id, level, views, attachments) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $connection->prepare($insertQuery);

// 绑定参数
$attachmentsJson = json_encode($attachments, JSON_UNESCAPED_UNICODE);
$stmt->bind_param("ssssisss", $title, $postTime, $content, $author, $authorId, $level, $views, $attachmentsJson);

if (!$stmt->execute()) {
    echo json_encode(["error" => "插入数据失败: " . $stmt->error]);
} else {
    // 获取插入成功的 ID
    $insertedId = $stmt->insert_id;

    // 更新 posts 表的 isok 列
    $updateQuery = "UPDATE posts SET isok = ? WHERE id = ?";
    $updateStmt = $connection->prepare($updateQuery);
    
    // 检查准备语句是否成功
    if ($updateStmt === false) {
        echo json_encode(["error" => "准备更新 SQL 语句失败: " . $connection->error]);
        exit;
    }

    $updateStmt->bind_param("ii", $insertedId, $linkId);

    if (!$updateStmt->execute()) {
        echo json_encode(["error" => "更新 posts 表失败: " . $updateStmt->error]);
    }

    $updateStmt->close();

    // 如果 TUISONG 为 true，发送 link 到 tuisong.php
    if (TUISONG) {
        if (PUSH === 'WxPusher') {
            $tuisongUrl = WEB . '/WxPusher.php?link=' . urlencode($link) . '&key=' . urlencode(KEY); 
            $chTuisong = curl_init();
            curl_setopt($chTuisong, CURLOPT_URL, $tuisongUrl); // 设置请求的 URL
            curl_setopt($chTuisong, CURLOPT_RETURNTRANSFER, true); // 将响应存储为字符串
            $tuisongResponse = curl_exec($chTuisong);
            curl_close($chTuisong);
        }
        if (PUSH2 === 'WxWorkApp') {
            echo json_encode(["code" => 200, "msg" => "数据处理成功"]);
            $tuisongUrl = WEB . '/WxWorkApp.php?link=' . urlencode($link) . '&key=' . urlencode(KEY); 
            $chTuisong = curl_init();
            curl_setopt($chTuisong, CURLOPT_URL, $tuisongUrl); // 设置请求的 URL
            curl_setopt($chTuisong, CURLOPT_RETURNTRANSFER, true); // 将响应存储为字符串
            $tuisongResponse = curl_exec($chTuisong);
        }
    }

    echo json_encode(["code" => 200, "msg" => "数据处理成功"]);
}

// 关闭数据库连接
$stmt->close();
$connection->close();
?>
