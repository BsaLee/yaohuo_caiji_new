# 妖火新帖采集最新版
- 最近更新 2024年11月18日
- 增加下载附件功能
- 增加每日下载次数限制
- 多渠道推送WxPusher和WxWorkApp
- 增加搜索功能（支持搜索标题和正文）
## 环境要求
- **Nginx**: 1.15 或更高版本
- **MySQL**: 8.0 或更高版本
- **PHP**: 8.0 或更高版本

配置信息请查看 `config.php` 文件。

## 接口说明

### 更新新帖接口
- **请求频率**: 建议每 5 分钟请求一次
- **接口地址**:  
'http://127.0.0.1/newtie.php?key=ADWHJDGAJ786786'

### 更新内容接口
- **请求频率**: 建议每 1-2 分钟请求一次
- **接口地址**:  
'http://127.0.0.1/caiji.php?key=ADWHJDGAJ786786'

### 推送指定帖子接口
- **说明**: 此接口无需监控，用于测试推送
- **接口地址**:  
'http://127.0.0.1/tuisong.php?link=1366020&key=ADWHJDGAJ786786'

## 注意事项
- `key` 为 `config.php` 中的监控密钥。
- 配置信息已在 `config.php` 中详细备注。如有不明之处，请保持默认设置。
- `sid` 为必填项，缺少此项将无法成功采集数据。

## 演示网站
- https://yh.di39.com/
## 我的微信推送订阅
- https://wxpusher.zjiecode.com/api/message/2g2gLmN9z7FbnHEM296tuF7ZMhF0k4zi
- https://wxpusher.zjiecode.com/api/qrcode/Wor0vQ5nsGMlrBFGRfc6qDVfWaQV8t3aLJbjyYpOrrsVZN1DoBIW7CQCc4erHHSv.jpg
- https://wxpusher.zjiecode.com/wxuser/?type=2&id=30818#/follow
