-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2024-10-28 22:56:55
-- 服务器版本： 8.0.12
-- PHP 版本： 7.3.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `yaohuo`
--

-- --------------------------------------------------------

--
-- 表的结构 `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` int(11) DEFAULT NULL,
  `author` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `replies` int(11) DEFAULT NULL,
  `views` int(11) DEFAULT NULL,
  `time` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attributes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `isok` int(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `posts`
--

INSERT INTO `posts` (`id`, `title`, `link`, `author`, `replies`, `views`, `time`, `attributes`, `created_at`, `isok`) VALUES
(106, '🚗🚗🚗150*40落地镜 0-16R', 1366009, '?', 6, 0, '今天 上午', NULL, '2024-10-28 13:03:59', NULL),
(105, '10月汇总：全国移动可领 （每个月都可以领取）', 1366010, '゛Gin . a゛', 10, 0, '今天 上午', NULL, '2024-10-28 13:03:59', 12),
(104, '图床分享搬运', 1366011, '妖火⑩年', 3, 0, '今天 上午', NULL, '2024-10-28 13:03:59', 11),
(102, '有没有妖友115网盘有会员', 1366013, '墨夕', 20, 0, '今天 中午', '礼', '2024-10-28 13:03:59', 9),
(103, '京东3-2支付卷（部分商品）+￥6.9毛拖鞋', 1366012, '゛Gin . a゛', 3, 0, '今天 中午', NULL, '2024-10-28 13:03:59', 10),
(101, '每天睡醒都脖子疼，有没有老哥推荐个枕头', 1366014, '初墨', 6, 0, '今天 中午', NULL, '2024-10-28 13:03:59', 8),
(100, '替朋友问问，自慰会引起早泄吗?', 1366015, '〆浔丶妖゛', 18, 0, '今天 中午', NULL, '2024-10-28 13:03:59', 7),
(98, '出几条绿厂用的12a数据线', 1366017, '古川望月', 9, 0, '今天 中午', NULL, '2024-10-28 13:03:59', 5),
(99, '京东plus会员去，京东自营 3元600g盼盼蛋糕', 1366016, '苏灿', 10, 0, '今天 中午', '附', '2024-10-28 13:03:59', 6),
(97, '【拼多多】涂色3d小石膏娃娃 32只 ￥9.9', 1366018, '〆浔丶妖゛', 5, 0, '今天 中午', NULL, '2024-10-28 13:03:59', 4),
(95, '飞鸟听书 2.185会员版v3｜鹿蜀', 1366020, '野火', 3, 0, '今天 中午', '附', '2024-10-28 13:03:59', 2),
(96, '啥事都是一个人抗，所以累成狗', 1366019, '心靈驛站首席大法師', 3, 0, '今天 中午', NULL, '2024-10-28 13:03:59', 3),
(107, '感情问题，刚遇见一个女生，不知道要不要进行下去！', 1366208, '别急，等我。', 35, 0, '今天 晚上', NULL, '2024-10-28 22:18:32', NULL),
(108, 'JD自营九阳 电火锅／绞肉机', 1366207, '▌壹贰叁肆吳┃彦祖', 3, 0, '今天 晚上', NULL, '2024-10-28 22:18:32', NULL),
(109, '电热水器有什么活动', 1366206, '改头换面', 2, 0, '今天 晚上', NULL, '2024-10-28 22:18:32', NULL),
(110, '微信钱包功能消失了！！！！', 1366205, '初中肄业', 321, 0, '今天 晚上', '礼', '2024-10-28 22:18:32', NULL),
(111, '能喝冰的。能喝液氮也不去！英雄联盟总决赛谁赢？', 1366204, 'NikoTesla', 4, 0, '今天 晚上', '投', '2024-10-28 22:18:32', NULL),
(112, '深圳50元一年300m移动宽带（安装费50-10）', 1366203, '妖友', 21, 0, '今天 晚上', NULL, '2024-10-28 22:18:32', NULL),
(113, '磁力播放下载：二驴下载 V1.3.1  会员解锁版', 1366202, '▌壹贰叁肆吳┃彦祖', 2, 0, '今天 晚上', '附', '2024-10-28 22:18:32', NULL),
(114, '早知道比特币这么强就不玩山寨了', 1366201, '宁陵上将纪湘', 9, 0, '今天 晚上', NULL, '2024-10-28 22:18:32', NULL),
(115, '新疆移动怎么改0月租', 1366200, '­؜Ynk', 1, 0, '今天 晚上', '赏', '2024-10-28 22:18:32', NULL),
(116, '李锦记薄盐生抽好价', 1366199, '妖联储', 7, 0, '今天 晚上', NULL, '2024-10-28 22:18:32', NULL),
(117, '45出网易云黑胶会员', 1366198, '南柯一梦', 1, 0, '今天 晚上', NULL, '2024-10-28 22:18:32', NULL),
(118, '买车买油车还是混动呢', 1366197, '喜欢你', 35, 0, '今天 晚上', NULL, '2024-10-28 22:18:32', NULL),
(119, '鸡安最新消息，数科物连，统一降速！影腾。。。。', 1366196, 'life@kkmm', 13, 0, '今天 晚上', '附', '2024-10-28 22:18:32', NULL),
(120, '有没有提取音频中不同人声的项目', 1366195, '黑幕', 2, 0, '今天 晚上', NULL, '2024-10-28 22:18:32', NULL),
(121, '上甘岭更新到23集', 1366194, '功夫大师马保国', 1, 0, '今天 晚上', '附', '2024-10-28 22:18:32', NULL),
(122, '之前是哪位', 1366210, '懵懂', 2, 0, '今天 晚上', NULL, '2024-10-28 22:20:13', NULL),
(123, '做跨境电商的进', 1366209, '不怕死的人才配活着', 7, 0, '今天 晚上', NULL, '2024-10-28 22:20:13', NULL);

-- --------------------------------------------------------

--
-- 表的结构 `scraped_posts`
--

CREATE TABLE `scraped_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `post_time` varchar(100) DEFAULT NULL,
  `content` text,
  `author` varchar(100) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `level` varchar(50) DEFAULT NULL,
  `views` int(11) DEFAULT NULL,
  `attachments` json DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 转存表中的数据 `scraped_posts`
--

INSERT INTO `scraped_posts` (`id`, `title`, `post_time`, `content`, `author`, `author_id`, `level`, `views`, `attachments`, `created_at`) VALUES
(1, '飞鸟听书 2.185会员版v3｜鹿蜀 - 资源共享', '2024-10-28 12:23', '<img class=\"ubbimg\" src=\"https://img.meituan.net/csc/adf81648dce5649dacd97d64b00597421670877.png\" referrerpolicy=\"no-referrer\">', '野火', 11209, '(7级奔跑的春风)', 61, '[{\"link\": \"https://www.yaohuo.me/bbs/download.aspx?siteid=1000&classid=201&book_id=1366020&id=863079&RndPath=UploadFiles&n=%e9%a3%9e%e9%b8%9f%e5%90%ac%e4%b9%a6+2.185%e4%bc%9a%e5%91%98%e7%89%88v3_%e9%b9%bf%e8%9c%80.apk\", \"name\": \"飞鸟听书 2.185会员版v3_鹿蜀.apk\"}]', '2024-10-28 14:01:32'),
(2, '飞鸟听书 2.185会员版v3｜鹿蜀 - 资源共享', '2024-10-28 12:23', '<img class=\"ubbimg\" src=\"https://img.meituan.net/csc/adf81648dce5649dacd97d64b00597421670877.png\" referrerpolicy=\"no-referrer\">', '野火', 11209, '(7级奔跑的春风)', 64, '[{\"link\": \"https://www.yaohuo.me/bbs/download.aspx?siteid=1000&classid=201&book_id=1366020&id=863079&RndPath=UploadFiles&n=%e9%a3%9e%e9%b8%9f%e5%90%ac%e4%b9%a6+2.185%e4%bc%9a%e5%91%98%e7%89%88v3_%e9%b9%bf%e8%9c%80.apk\", \"name\": \"飞鸟听书 2.185会员版v3_鹿蜀.apk\"}]', '2024-10-28 14:03:47'),
(3, '啥事都是一个人抗，所以累成狗 - 妖火茶馆', '2024-10-28 12:22', '“初听是高三，在听已三高”#欧美音乐 #音乐现场 #经典老歌 #lonelyd@42894451548<video class=\"ubbvideo\" src=\"https://aweme.snssdk.com/aweme/v1/play/?video_id=v0300fg10000csddlrnog65s798a9ivg&amp;ratio=1080p\" width=\"100%\" height=\"100%\" poster=\"http://p1.meituan.net/csc/c9ba4376ba2a08fd62f4817dcb39e76917720.jpg\" controls>{不支持在线播放，请更换浏览器}</video><br>听来听去，还是老歌耐听NaNa《Lonely》20多年过去了，满满的回忆。#欧美音乐 #lonely #好听英文歌曲 #Nanad@hxf971010<video class=\"ubbvideo\" src=\"https://aweme.snssdk.com/aweme/v1/play/?video_id=v0200fg10000csce1qnog65ug2vd4gmg&amp;ratio=1080p\" width=\"100%\" height=\"100%\" poster=\"http://p0.meituan.net/csc/d1fb591f938b522950470d86dd16fcc730996.jpg\" controls>{不支持在线播放，请更换浏览器}</video><br>这是《最后的莫西干人》背景词，大家要知道出处。<br>此曲歌词大意：<br>  如果有一天，我去世了，恨我的人，翩翩起舞，爱我的人，眼泪如露。<br> 第二天，我的尸体头朝西埋在地下深处，恨我的人，看着我的坟墓，一脸笑意，爱我的人，不敢回头看那麼一眼。<br> 一年后，我的尸骨已经腐烂，我的坟堆雨打风吹，恨我的人，偶尔在茶余饭后提到我时，仍然一脸恼怒，爱我的人，夜深人静时，无声的眼泪向谁哭诉。<br> 十年后，我没有了尸体，只剩一些残骨。恨我的人，只隐约记得我的名字，已经忘了我的面目，爱我至深的人啊，想起我时，有短暂的沉默，生活把一切都渐渐模糊。<br> 几十年后，我的坟堆雨打风吹去，唯有一片荒芜，恨我的人，把我遗忘，爱我至深的人，也跟着进入了坟墓。<br> 对这个世界来说，我彻底变成了虚无。<br>我奋斗一生，带不走一草一木。我一生执着，带不走一分虚荣爱慕。<br><br>今生，无论贵贱贫富，总有一天都要走到这最后一步。<br>到了后世，霍然回首，我的这一生，形同虚度！我想痛哭，却发不出一点声音，我想忏悔，却已迟暮！', '心靈驛站首席大法師', 30875, '(10级摇曳的星辰)', 405, '[]', '2024-10-28 14:04:03'),
(4, '【拼多多】涂色3d小石膏娃娃 32只 ￥9.9 - 活动线报', '2024-10-28 12:20', '涂色3d小石膏娃娃 32只 9.9 贈12色颜料+图纸+画笔~<br><br>https://p.pinduoduo.com/ypad4cDK<br><br><img class=\"ubbimg\" src=\"https://pic2.ziyuan.wang/user/88188/2024/10/Screenshot_20241028_120651_4b76dc189d534.jpg\" referrerpolicy=\"no-referrer\"><br><img class=\"ubbimg\" src=\"https://pic2.ziyuan.wang/user/88188/2024/10/down_aad425f297f10.png\" referrerpolicy=\"no-referrer\"><br><img class=\"ubbimg\" src=\"https://pic2.ziyuan.wang/user/88188/2024/10/down%20(1)_4521df8752515.png\" referrerpolicy=\"no-referrer\"><br><img class=\"ubbimg\" src=\"https://pic2.ziyuan.wang/user/88188/2024/10/down%20(2)_62e65e4a5ae36.png\" referrerpolicy=\"no-referrer\"><br>', '〆浔丶妖゛', 39895, '(7级奔跑的春风)', 149, '[]', '2024-10-28 14:05:15'),
(5, '出几条绿厂用的12a数据线 - 妖火茶馆', '2024-10-28 12:19', '真我商城直发，全新未拆封，顺丰包邮<br><br>正宗原装12A，又硬又粗，绝非第三方那些只激活图标电流上不去那些软线可比的，适用于绿厂全系列。<br><br>【闲鱼】https://m.tb.cn/h.gCgOu9A?tk=M5Sv3Mk42xq CZ0015 「我在闲鱼发布了【真我USB-A to Type-C 闪充数据线 12A 1米】」<br>点击链接直接打开', '古川望月', 23273, '(6级地上的月影)', 335, '[]', '2024-10-28 16:07:49'),
(6, '京东plus会员去，京东自营 3元600g盼盼蛋糕 - 活动线报', '2024-10-28 12:16', '2-4💰❗️一箱盼盼蛋糕。<br><br>先领<br>https://u.jd.com/K6bJgzr<br>领券加入购物车。<br>https://u.jd.com/KDzqHTl<br>凑单拍1，<br>https://u.jd.com/KObcq0S', '苏灿', 8899, '(4级水面的小草)', 480, '[]', '2024-10-28 16:24:40'),
(7, '替朋友问问，自慰会引起早泄吗? - 妖火茶馆', '2024-10-28 12:14', '<img class=\"ubbimg\" src=\"https://pic2.ziyuan.wang/user/88188/2024/10/mmexport1730088747865_a036c7ff29004.jpg\" referrerpolicy=\"no-referrer\">', '〆浔丶妖゛', 39895, '(7级奔跑的春风)', 1053, '[]', '2024-10-28 16:26:51'),
(8, '每天睡醒都脖子疼，有没有老哥推荐个枕头 - 妖火茶馆', '2024-10-28 12:10', '每天睡醒都脖子疼，有没有老哥推荐个枕头', '初墨', 48113, '(5级呢喃的歌声)', 339, '[]', '2024-10-28 16:35:05'),
(9, '有没有妖友115网盘有会员 - 妖火茶馆', '2024-10-28 12:10', '有偿租用用来搭小雅...，阿里会员感觉不太值，套娃第三方会员<br>或者说合租', '墨夕', 42288, '(5级呢喃的歌声)', 353, '[]', '2024-10-28 16:45:06'),
(10, '京东3-2支付卷（部分商品）+￥6.9毛拖鞋 - 活动线报', '2024-10-28 12:06', 'https://u.jd.com/KrzVpcg<br><br><img class=\"ubbimg\" src=\"https://pic2.ziyuan.wang/user/88188/2024/10/Screenshot_20241028_120057_3a6db9c98f610.jpg\" referrerpolicy=\"no-referrer\"><br><img class=\"ubbimg\" src=\"https://pic2.ziyuan.wang/user/88188/2024/10/Screenshot_20241028_120243_46adbbd44402c.jpg\" referrerpolicy=\"no-referrer\"><br><hr><br>拼多多毛拖鞋1双6.9<br><br>https://p.pinduoduo.com/4Gid1SlQ<br><br><img class=\"ubbimg\" src=\"https://pic2.ziyuan.wang/user/88188/2024/10/Screenshot_20241028_120328_c457f5055032d.jpg\" referrerpolicy=\"no-referrer\"><br><img class=\"ubbimg\" src=\"https://pic2.ziyuan.wang/user/88188/2024/10/IMG_20241028_120402_425cdd1424aeb.png\" referrerpolicy=\"no-referrer\">', '゛Gin . a゛', 2212, '(9级苍瀚的风云)', 157, '[]', '2024-10-28 22:21:04'),
(11, '图床分享搬运 - 妖火茶馆', '2024-10-28 11:57', '<a href=\"https://pic.sptra.org/\">搬来的</a>', '妖火⑩年', 41817, '(5级呢喃的歌声)', 184, '[]', '2024-10-28 22:22:59'),
(12, '10月汇总：全国移动可领 （每个月都可以领取） - 活动线报', '2024-10-28 11:54', '活动<b>一，二，五，十一</b>，短链接地址错误！勿点！<br><br>活动1.拾秋👉http://6-y.cn/nDaTz8<br>活动2.每日刮福气👉http://6-y.cn/nDa5G2<br>活动3.手游cf会员👉http://6-y.cn/nDa1Mv<br>活动4.Q会员👉http://6-y.cn/nDaIqy<br>活动5.签到3次👉http://6-y.cn/nDaNwB<br>活动6.酷狗👉http://6-y.cn/nDaO7o<br>活动7.京东👉http://6-y.cn/nDaQuB<br>活动8.q音乐👉http://6-y.cn/nDaoPo<br>活动9.领1G月包+6次抽奖👉http://6-y.cn/nDaeQh<br>活动10.挖宝👉http://6-y.cn/nDWGiR<br>活动11.云闪👉http://6-y.cn/nDWO89<br>活动12.爱奇艺👉http://6-y.cn/nqHngX', '゛Gin . a゛', 2212, '(9级苍瀚的风云)', 991, '[]', '2024-10-28 22:27:08');

--
-- 转储表的索引
--

--
-- 表的索引 `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `link` (`link`);

--
-- 表的索引 `scraped_posts`
--
ALTER TABLE `scraped_posts`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- 使用表AUTO_INCREMENT `scraped_posts`
--
ALTER TABLE `scraped_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
