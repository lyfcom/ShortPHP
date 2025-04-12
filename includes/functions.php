<?php
require_once 'config.php';

/**
 * 生成随机的短码
 * 
 * @param int $length 短码长度
 * @return string 生成的短码
 */
function generateShortCode($length = 6) {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $code;
}

/**
 * 创建短链接
 * 
 * @param string $longUrl 原始长链接
 * @param string $customCode 自定义短码(可选)
 * @return string|bool 成功返回短码，失败返回false
 */
function createShortUrl($longUrl, $customCode = '') {
    global $conn;
    
    // 检查URL有效性
    if (!filter_var($longUrl, FILTER_VALIDATE_URL)) {
        return false;
    }
    
    // 检查是否已存在该长链接
    $stmt = $conn->prepare("SELECT short_code FROM urls WHERE long_url = ?");
    $stmt->bind_param("s", $longUrl);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['short_code'];
    }
    
    // 如果提供了自定义短码
    if (!empty($customCode)) {
        // 检查自定义短码是否已存在
        $stmt = $conn->prepare("SELECT id FROM urls WHERE short_code = ?");
        $stmt->bind_param("s", $customCode);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // 自定义短码已被使用
            return false;
        }
        
        $shortCode = $customCode;
    } else {
        // 自动生成短码
        $shortCode = generateShortCode();
        
        // 检查短码是否已存在，如果存在则重新生成
        $stmt = $conn->prepare("SELECT id FROM urls WHERE short_code = ?");
        $stmt->bind_param("s", $shortCode);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($result->num_rows > 0) {
            $shortCode = generateShortCode();
            $stmt->bind_param("s", $shortCode);
            $stmt->execute();
            $result = $stmt->get_result();
        }
    }
    
    // 将新的短链接保存到数据库
    $stmt = $conn->prepare("INSERT INTO urls (long_url, short_code, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $longUrl, $shortCode);
    
    if ($stmt->execute()) {
        return $shortCode;
    } else {
        return false;
    }
}

/**
 * 根据短码获取长链接
 * 
 * @param string $shortCode 短码
 * @return string|bool 成功返回长链接，失败返回false
 */
function getLongUrl($shortCode) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT long_url FROM urls WHERE short_code = ?");
    $stmt->bind_param("s", $shortCode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // 更新访问次数
        $stmt = $conn->prepare("UPDATE urls SET clicks = clicks + 1 WHERE short_code = ?");
        $stmt->bind_param("s", $shortCode);
        $stmt->execute();
        
        return $row['long_url'];
    } else {
        return false;
    }
}

/**
 * 获取最近生成的短链接
 * 
 * @param int $limit 数量限制
 * @return array 短链接数组
 */
function getRecentUrls($limit = 10) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM urls ORDER BY created_at DESC LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $urls = [];
    while ($row = $result->fetch_assoc()) {
        $urls[] = $row;
    }
    
    return $urls;
}

/**
 * 更新长链接
 * 
 * @param string $shortCode 短码
 * @param string $newLongUrl 新的长链接
 * @return bool 是否更新成功
 */
function updateLongUrl($shortCode, $newLongUrl) {
    global $conn;
    
    // 检查URL有效性
    if (!filter_var($newLongUrl, FILTER_VALIDATE_URL)) {
        return false;
    }
    
    // 检查短码是否存在
    $stmt = $conn->prepare("SELECT id FROM urls WHERE short_code = ?");
    $stmt->bind_param("s", $shortCode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        return false;
    }
    
    // 更新长链接
    $stmt = $conn->prepare("UPDATE urls SET long_url = ? WHERE short_code = ?");
    $stmt->bind_param("ss", $newLongUrl, $shortCode);
    
    return $stmt->execute();
}

/**
 * 获取URL详细信息
 * 
 * @param string $shortCode 短码
 * @return array|bool 成功返回URL详情数组，失败返回false
 */
function getUrlDetails($shortCode) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM urls WHERE short_code = ?");
    $stmt->bind_param("s", $shortCode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return false;
}

/**
 * 删除短链接
 * 
 * @param string $shortCode 要删除的短链接代码
 * @return bool 删除是否成功
 */
function deleteShortUrl($shortCode) {
    global $conn;
    
    // 检查短码是否存在
    $stmt = $conn->prepare("SELECT id FROM urls WHERE short_code = ?");
    $stmt->bind_param("s", $shortCode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        return false; // 短码不存在
    }
    
    // 删除短链接
    $stmt = $conn->prepare("DELETE FROM urls WHERE short_code = ?");
    $stmt->bind_param("s", $shortCode);
    
    return $stmt->execute();
}
?> 