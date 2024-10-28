<?php
// 引入 config.php 读取配置信息
require 'config.php';

// 设置响应头为 JSON 格式
header('Content-Type: application/json; charset=UTF-8');

// 从配置文件获取要查询的数量
$limit = SHULIANG; 

// 从数据库中查询 posts 表中 id 最大的 x 条数据
$query = "SELECT id, title, link, author, replies, views, time, attributes, created_at, isok FROM posts ORDER BY id DESC LIMIT ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $limit);
$stmt->execute();
$result = $stmt->get_result();

// 结果数组
$data = [];

// 检查是否有结果
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row; // 将每一行数据添加到数组
    }
}

// 组合响应数据
$response = [
    'status' => 'success',
    'data' => $data
];

// 输出 JSON 响应
echo json_encode($response);

// 关闭数据库连接
$stmt->close();
$connection->close();
?>
