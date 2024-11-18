<?php
require 'config.php'; // 引入 config.php 文件，确保连接数据库
header('Content-Type: application/json'); // 设置响应头为 JSON 格式
$connection->set_charset("utf8mb4");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$searchTerm = isset($_GET['search']) ? $_GET['search'] : ''; // 获取搜索词
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // 获取页码，默认为第一页
$limit = 10; // 每页显示的记录数
$offset = ($page - 1) * $limit; // 计算偏移量

if (!empty($searchTerm)) {
    $searchTerm = '%' . $searchTerm . '%'; // 加上通配符进行模糊匹配

    // 查询匹配的 id 在 scraped_posts 表
    $query = "SELECT id FROM scraped_posts WHERE title LIKE ? OR content LIKE ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ss", $searchTerm, $searchTerm); // 绑定参数
    $stmt->execute();
    $result = $stmt->get_result();

    // 处理查询结果
    $ids = [];
    while ($row = $result->fetch_assoc()) {
        $ids[] = $row['id']; // 收集匹配到的 id
    }

    // 如果有匹配的 id，进行查询 posts 表获取相关数据
    if (!empty($ids)) {
        $idsPlaceholders = implode(',', array_fill(0, count($ids), '?'));
        
        // 查询 posts 表中的数据并获取 isok 字段
        $query = "SELECT id, title, link, author, replies, views, time, attributes, created_at, isok 
                  FROM posts WHERE isok IN ($idsPlaceholders) LIMIT $limit OFFSET $offset";
        $stmt = $connection->prepare($query);
        $stmt->bind_param(str_repeat('i', count($ids)), ...$ids); // 绑定多个 id
        $stmt->execute();
        $result = $stmt->get_result();

        // 获取数据
        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row; // 收集数据
        }

        // 查询总的匹配数
        $totalQuery = "SELECT COUNT(id) as total FROM posts WHERE id IN ($idsPlaceholders)";
        $stmt = $connection->prepare($totalQuery);
        $stmt->bind_param(str_repeat('i', count($ids)), ...$ids); // 绑定多个 id
        $stmt->execute();
        $totalResult = $stmt->get_result();
        $totalRow = $totalResult->fetch_assoc();
        $totalPosts = $totalRow['total']; // 总匹配数量

        // 返回分页数据
        echo json_encode([
            'status' => 'success',
            'data' => $posts,
            'pagination' => [
                'total' => $totalPosts, // 总记录数
                'current_page' => $page, // 当前页码
                'limit' => $limit, // 每页记录数
                'total_pages' => ceil($totalPosts / $limit) // 总页数
            ]
        ]);
    } else {
        // 没有找到匹配的帖子
        echo json_encode([
            'status' => 'success',
            'data' => [],
            'pagination' => [
                'total' => 0,
                'current_page' => $page,
                'limit' => $limit,
                'total_pages' => 0
            ]
        ]);
    }
} else {
    // 如果没有传入搜索关键字
    echo json_encode([
        'status' => 'error',
        'message' => '没有提供搜索词'
    ]);
}
?>
