<?php
// Database configuration
define('DB_HOST', 'localhost'); // 数据库主机
define('DB_USER', 'yaohuo');  // 数据库用户名
define('DB_PASS', 'yaohuo');  // 数据库密码
define('DB_NAME', 'yaohuo');  // 数据库名称

// 配置开始
define('SID', '11111_2222_3333_4444_5555-2');     // cookie中的sidyaohuo,要和域名配套
define('YUMING', 'www.yaohuo.me');//获取cookie的妖火的域名（请区分www.yaohuo.me和yaohuo.me  cookie不通用）
define('WEB', 'http://127.0.0.1');//你的网站域名
define('NEWTIE', 'https://www.yaohuo.me/bbs/book_list.aspx?action=new&siteid=1000&classid=0&getTotal=2024&page=1'); // 获取新贴的链接
define('NEWTIE_JSON', true); // 新帖采集接口是否输出帖子json True或者False
define('CAIJI_JSON', False); // 帖子内容采集接口是否输出帖子json True或者False
define('TUISONG', true);// 是否推送
define('SHULIANG', 50); //首页展示数据数量
define('KEY', "ADWHJDGAJ786786");//接口密钥 防止被恶意调用(新帖、内容、推送这三个接口带)
define('PUSH', "WxPusher");//官网https://wxpusher.zjiecode.com/admin/main
define('WxPusher', "AT_1111111111111111");//WxPusher的APP_TOKEN
define('topicIds', 34731);//https://wxpusher.zjiecode.com/admin/main/topics/list
define('Download', 10);//每日限制下载次数
define('CAIJI_DEBUG', False);//采集接口打印请求和响应
define('CORP_ID', 'ww3c37cae111421d');//企业微信企业ID
define('AGENT_ID', 100107);//企业微信应用id
define('SECRET', 'dUrSGBExCP111r0SxJQgkyIHyDvC-w');//企业微信应用密钥
define('PIC_URL', 'https://p0.meituan.net/csc/532f101cb25498fd6af30f46486d844d325287.png');//企业微信应用推送图片
// 配置结束
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$connection) {
    die('Connection failed: ' . mysqli_connect_error());
}

// Set character set
mysqli_set_charset($connection, 'utf8mb4'); // 设置字符集为 utf8mb4
?>
