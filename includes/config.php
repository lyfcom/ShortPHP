<?php
// 数据库配置信息 - 需要根据虚拟主机提供商提供的信息修改
$db_host = 'localhost'; // 数据库主机地址，通常是localhost
$db_user = 'your_db_username'; // 修改为虚拟主机提供的数据库用户名
$db_pass = 'your_db_password'; // 修改为虚拟主机提供的数据库密码
$db_name = 'your_db_name';     // 修改为虚拟主机上的数据库名称

// 创建数据库连接
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// 检查连接是否成功
if ($conn->connect_error) {
    die("数据库连接失败: " . $conn->connect_error);
}

// 设置字符集
$conn->set_charset("utf8");

// 站点URL - 请修改为您的实际域名
define('BASE_URL', 'https://你的域名.com/'); // 修改为您的实际域名
?> 