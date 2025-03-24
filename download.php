<?php
require 'config.php'; // 包含数据库连接和常量定义

// 接收 POST 请求的 URL 参数
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
    $url = $_POST['url'];

    // 校验 URL 参数名
    $expectedParams = ['siteid', 'classid', 'book_id', 'id', 'RndPath', 'n'];
    $urlParts = parse_url($url);
    parse_str($urlParts['query'], $queryParams);

    foreach ($expectedParams as $param) {
        if (!array_key_exists($param, $queryParams)) {
            // 如果参数缺失，直接返回错误，不传递给 d1.php
            echo json_encode(['code' => 400, 'msg' => '缺少参数: ' . $param]);
            exit;
        }
    }

    // 检查常量 Download
    if (Download === 0) {
        // 如果无下载次数，直接返回错误，不传递给 d1.php
        echo json_encode(['code' => 400, 'msg' => '无下载次数，请联系管理员']);
        exit;
    }

    // 从数据库中查找是否存在相应的 param_id
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }

    $paramId = $queryParams['id'];
    $stmt = $conn->prepare("SELECT response_data FROM downloads WHERE param_id = ?");
    $stmt->bind_param("i", $paramId);
    $stmt->execute();
    $stmt->bind_result($responseData);
    $stmt->fetch();
    $stmt->close();

    if ($responseData) {
        echo json_encode(['code' => 200, 'msg' => $responseData]);
        $conn->close();
        exit;
    }

    // 查找今天的下载记录
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT COUNT(*) FROM downloads WHERE created_at >= ? AND created_at < DATE_ADD(?, INTERVAL 1 DAY)");
    $stmt->bind_param("ss", $today, $today);
    $stmt->execute();
    $stmt->bind_result($downloadCount);
    $stmt->fetch();
    $stmt->close();

    if ($downloadCount >= Download) {
        // 如果今日无下载次数，直接返回错误，不传递给 d1.php
        echo json_encode(['code' => 400, 'msg' => '今日无下载次数，明日再来吧']);
        $conn->close();
        exit;
    }

    // 执行 GET 请求
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 禁用 SSL 证书验证
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 禁用主机验证
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Cookie: sidyaohuo=' . SID
    ]);
    $htmlResponse = curl_exec($ch);

    if ($htmlResponse === false) {
        // 如果请求失败，将接收到的参数传递给 d1.php 并打印返回结果
        $result = passToD1($_POST); // 传递原始的 POST 数据
        echo $result; // 打印 d1.php 的返回结果
        curl_close($ch);
        exit;
    }

    curl_close($ch);

    // 提取目标 URL
    $startPos = strpos($htmlResponse, '<a href="');
    if ($startPos === false) {
        // 如果无法提取链接，将接收到的参数传递给 d1.php 并打印返回结果
        $result = passToD1($_POST); // 传递原始的 POST 数据
        echo $result; // 打印 d1.php 的返回结果
        exit;
    }

    $startPos += 9; // 9 是 "<a href=" 的长度
    $endPos = strpos($htmlResponse, '">here</a>', $startPos);
    if ($endPos === false) {
        // 如果无法提取链接，将接收到的参数传递给 d1.php 并打印返回结果
        $result = passToD1($_POST); // 传递原始的 POST 数据
        echo $result; // 打印 d1.php 的返回结果
        exit;
    }

    $fullUrl = urldecode(substr($htmlResponse, $startPos, $endPos - $startPos));

    // 移除 "/bbs/link.html?target=" 部分
    $targetUrl = str_replace('/bbs/link.html?target=', '', $fullUrl);

    // 写入数据库
    $stmt = $conn->prepare("INSERT INTO downloads (siteid, classid, book_id, param_id, RndPath, n, response_data, created_at, IP) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
    $ipAddress = $_SERVER['REMOTE_ADDR']; // 获取用户 IP
    $stmt->bind_param("iiisssss", $queryParams['siteid'], $queryParams['classid'], $queryParams['book_id'], $paramId, $queryParams['RndPath'], $queryParams['n'], $targetUrl, $ipAddress);

    if ($stmt->execute()) {
        echo json_encode(['code' => 200, 'msg' => $targetUrl]); // 将 URL 放到 msg 中
    } else {
        // 如果写入数据库失败，将接收到的参数传递给 d1.php 并打印返回结果
        $result = passToD1($_POST); // 传递原始的 POST 数据
        echo $result; // 打印 d1.php 的返回结果
    }

    $stmt->close();
    $conn->close();
} else {
    // 如果请求无效，直接返回错误，不传递给 d1.php
    echo json_encode(['code' => 400, 'msg' => '无效请求']);
}

/**
 * 将参数传递给 d1.php 并返回其响应
 */
function passToD1($params)
{
    global $d1Url; // 如果需要全局变量，可以使用 global，但推荐直接使用常量

    // 使用 cURL 将参数传递给 d1.php
    $ch = curl_init();
    $fullUrl = WEB . '/d1.php'; // 拼接完整的 URL
    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params); // 传递 POST 数据
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取响应结果

    // 执行请求并获取响应
    $response = curl_exec($ch);

    // 检查是否有错误发生
    if (curl_errno($ch)) {
        $error = 'cURL 错误: ' . curl_error($ch);
        curl_close($ch);
        return json_encode(['code' => 500, 'msg' => $error]); // 返回错误信息
    }

    curl_close($ch);

    // 返回 d1.php 的响应
    return $response;
}
