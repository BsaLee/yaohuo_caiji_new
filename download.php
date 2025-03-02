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
            echo json_encode(['code' => 400, 'msg' => '缺少参数: ' . $param]);
            exit;
        }
    }

    // 检查常量 Download
    if (Download === 0) {
        echo json_encode(['code' => 400, 'msg' => '无下载次数，请联系管理员']);
        exit;
    }

    // 连接数据库
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
        echo json_encode(['code' => 400, 'msg' => '今日无下载次数，明日再来吧']);
        $conn->close();
        exit;
    }

    // 获取 `n` 参数作为文件名（修正编码）
    $fileName = rawurldecode($queryParams['n']); // 修正 URL 编码
    $fileName = preg_replace('/[\/:*?"<>|]/', '_', $fileName); // 过滤非法字符
    $downloadDir = __DIR__ . "/Download/";

    // 确保 Download 目录存在
    if (!is_dir($downloadDir)) {
        mkdir($downloadDir, 0777, true);
    }

    $savePath = $downloadDir . $fileName;

    // **使用 cURL 下载文件**
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 自动跟随重定向
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_ENCODING, ""); // 处理 GZIP 压缩
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Cookie: sidyaohuo=' . SID
    ]);
    
    $fileData = curl_exec($ch);
    if ($fileData === false) {
        echo json_encode(['code' => 400, 'msg' => '文件下载失败', 'error' => curl_error($ch)]);
        curl_close($ch);
        $conn->close();
        exit;
    }
    curl_close($ch);

    // **保存文件**
    if (file_put_contents($savePath, $fileData) === false) {
        echo json_encode(['code' => 400, 'msg' => '无法保存文件']);
        $conn->close();
        exit;
    }

    // **返回本地文件下载链接**
    $targetUrl = "https://yh.di39.com/Download/" . urlencode($fileName);

    // **写入数据库**
    $conn->set_charset("utf8mb4");
    $stmt = $conn->prepare("INSERT INTO downloads (siteid, classid, book_id, param_id, RndPath, n, response_data, created_at, IP) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $stmt->bind_param("iiisssss", $queryParams['siteid'], $queryParams['classid'], $queryParams['book_id'], $paramId, $queryParams['RndPath'], $queryParams['n'], $targetUrl, $ipAddress);
    
    if ($stmt->execute()) {
        echo json_encode(['code' => 200, 'msg' => $targetUrl]);
    } else {
        echo json_encode(['code' => 400, 'msg' => '写入数据库失败']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['code' => 400, 'msg' => '无效请求']);
}
