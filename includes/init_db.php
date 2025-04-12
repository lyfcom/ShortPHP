<?php
// 数据库连接参数 - 需要根据虚拟主机提供的信息修改
$db_host = 'localhost'; // 数据库主机地址，通常是localhost
$db_user = 'your_db_username'; // 修改为虚拟主机提供的数据库用户名
$db_pass = 'your_db_password'; // 修改为虚拟主机提供的数据库密码
$db_name = 'your_db_name';     // 修改为虚拟主机上的数据库名称

// 创建数据库连接
try {
    // 有些虚拟主机不允许创建数据库，如果已经有数据库，直接连接即可
    $conn = new mysqli($db_host, $db_user, $db_pass);
    
    // 检查连接是否成功
    if ($conn->connect_error) {
        throw new Exception("数据库连接失败: " . $conn->connect_error);
    }
    
    // 在某些虚拟主机上，可能无法创建数据库，需要通过控制面板创建
    // 如果数据库已存在，此步骤可能会失败，我们可以尝试直接使用数据库
    $dbExists = false;
    
    try {
        $sql = "CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8 COLLATE utf8_general_ci";
        if ($conn->query($sql) === TRUE) {
            echo "数据库创建成功或已存在<br>";
        } else {
            throw new Exception("创建数据库出错: " . $conn->error);
        }
    } catch (Exception $e) {
        echo "注意：" . $e->getMessage() . "<br>";
        echo "尝试直接连接到数据库...<br>";
        $dbExists = true;
    }
    
    // 选择或连接数据库
    if ($dbExists) {
        $conn->close();
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
        if ($conn->connect_error) {
            throw new Exception("连接到指定数据库失败: " . $conn->connect_error);
        }
        echo "成功连接到数据库<br>";
    } else {
        $conn->select_db($db_name);
    }
    
    // 创建表
    $sql = "CREATE TABLE IF NOT EXISTS urls (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        long_url VARCHAR(2048) NOT NULL,
        short_code VARCHAR(20) NOT NULL UNIQUE,
        clicks INT(11) UNSIGNED DEFAULT 0,
        created_at DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    
    if ($conn->query($sql) === TRUE) {
        echo "数据表创建成功或已存在<br>";
    } else {
        throw new Exception("创建数据表出错: " . $conn->error);
    }
    
    // 关闭连接
    $conn->close();
    
    echo "<div style='margin: 20px 0; padding: 10px; background-color: #d4edda; color: #155724; border-radius: 4px;'>
            数据库初始化完成，您可以<a href='../index.php' style='color: #155724; text-decoration: underline;'>返回主页</a>
          </div>";
          
    echo "<div style='margin: 20px 0; padding: 10px; background-color: #cce5ff; color: #004085; border-radius: 4px;'>
            <strong>重要提示：</strong> 初始化成功后，请删除或重命名 init_db.php 文件以提高安全性。
          </div>";
    
} catch (Exception $e) {
    echo "<div style='margin: 20px 0; padding: 10px; background-color: #f8d7da; color: #721c24; border-radius: 4px;'>
            <strong>错误：</strong> " . $e->getMessage() . "
          </div>";
    
    echo "<div style='margin: 20px 0; padding: 10px; background-color: #fff3cd; color: #856404; border-radius: 4px;'>
            <strong>提示：</strong> 请确认您已在虚拟主机控制面板中创建了数据库，并且提供了正确的数据库连接信息。
            <br>修改 includes/config.php 和 includes/init_db.php 文件中的数据库参数。
          </div>";
}
?> 