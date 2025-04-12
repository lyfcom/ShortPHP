<?php
require_once 'includes/functions.php';

// 获取所有URL
function getAllUrls() {
    global $conn;
    
    $sql = "SELECT * FROM urls ORDER BY clicks DESC";
    $result = $conn->query($sql);
    
    $urls = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $urls[] = $row;
        }
    }
    
    return $urls;
}

// 获取总点击数
function getTotalClicks() {
    global $conn;
    
    $sql = "SELECT SUM(clicks) as total_clicks FROM urls";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total_clicks'] ? $row['total_clicks'] : 0;
    }
    
    return 0;
}

// 获取总URL数
function getTotalUrls() {
    global $conn;
    
    $sql = "SELECT COUNT(*) as total_urls FROM urls";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total_urls'];
    }
    
    return 0;
}

$allUrls = getAllUrls();
$totalClicks = getTotalClicks();
$totalUrls = getTotalUrls();
?>
<!DOCTYPE html>
<html lang="zh-CN" data-theme="cupcake">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>短链接统计 - 短链接生成器</title>
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@latest/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="assets/js/theme.js"></script>
</head>
<body class="min-h-screen bg-base-200">
    <div class="navbar bg-base-100 shadow-md sticky top-0 z-30">
        <div class="container mx-auto px-2">
            <div class="flex-1">
                <a href="index.php" class="btn btn-ghost text-xl normal-case">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                    短链接生成器
                </a>
            </div>
            <div class="flex-none">
                <div class="hidden md:flex">
                    <a href="index.php" class="btn btn-ghost btn-sm">主页</a>
                    <a href="query.php" class="btn btn-ghost btn-sm ml-2">查询/编辑</a>
                    <a href="stats.php" class="btn btn-primary btn-sm ml-2">统计</a>
                    <label class="swap swap-rotate ml-4">
                        <input type="checkbox" id="theme-toggle" class="theme-controller" />
                        <svg class="swap-on fill-current w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z"/></svg>
                        <svg class="swap-off fill-current w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z"/></svg>
                    </label>
                </div>
                <div class="dropdown dropdown-end md:hidden">
                    <label tabindex="0" class="btn btn-ghost btn-circle">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg>
                    </label>
                    <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52">
                        <li><a href="index.php">主页</a></li>
                        <li><a href="query.php">查询/编辑</a></li>
                        <li><a href="stats.php" class="active">统计</a></li>
                        <li>
                            <label class="swap swap-rotate">
                                <input type="checkbox" id="theme-toggle-mobile" class="theme-controller" />
                                <svg class="swap-on fill-current w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z"/></svg>
                                <svg class="swap-off fill-current w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z"/></svg>
                                <span class="ml-2">切换主题</span>
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="text-center mb-8">
            <h1 class="text-4xl md:text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-accent to-primary pb-2">短链接使用统计</h1>
            <p class="mt-3 text-lg opacity-80 max-w-xl mx-auto">了解您的短链接使用情况与点击分析</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="stats shadow-lg bg-base-100 transform hover:scale-105 transition-transform">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-10 h-10 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="stat-title font-medium">总短链接数</div>
                    <div class="stat-value text-primary"><?php echo $totalUrls; ?></div>
                    <div class="stat-desc mt-2">所有生成的短链接</div>
                </div>
            </div>
            
            <div class="stats shadow-lg bg-base-100 transform hover:scale-105 transition-transform">
                <div class="stat">
                    <div class="stat-figure text-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-10 h-10 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    </div>
                    <div class="stat-title font-medium">总点击次数</div>
                    <div class="stat-value text-secondary"><?php echo $totalClicks; ?></div>
                    <div class="stat-desc mt-2">所有链接的访问量</div>
                </div>
            </div>
            
            <div class="stats shadow-lg bg-base-100 transform hover:scale-105 transition-transform">
                <div class="stat">
                    <div class="stat-figure text-accent">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-10 h-10 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    </div>
                    <div class="stat-title font-medium">平均点击率</div>
                    <div class="stat-value text-accent"><?php echo $totalUrls > 0 ? round($totalClicks / $totalUrls, 1) : 0; ?></div>
                    <div class="stat-desc mt-2">每个链接平均点击次数</div>
                </div>
            </div>
        </div>
        
        <?php if (!empty($allUrls)): ?>
        <div class="card bg-base-100 shadow-xl mx-auto overflow-hidden">
            <div class="card-body">
                <h2 class="card-title text-accent flex items-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    链接访问统计
                </h2>
                
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th class="bg-base-200">#</th>
                                <th class="bg-base-200">短链接</th>
                                <th class="bg-base-200 hidden md:table-cell">原始链接</th>
                                <th class="bg-base-200">点击量</th>
                                <th class="bg-base-200 hidden md:table-cell">创建时间</th>
                                <th class="bg-base-200">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allUrls as $index => $url): ?>
                            <tr class="hover">
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <div class="flex flex-col">
                                        <a href="<?php echo $url['short_code']; ?>" target="_blank" class="link link-primary font-medium">
                                            <?php echo BASE_URL . $url['short_code']; ?>
                                        </a>
                                        <span class="text-xs text-gray-500 md:hidden">
                                            <?php echo date('Y-m-d', strtotime($url['created_at'])); ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="max-w-xs truncate hidden md:table-cell" title="<?php echo htmlspecialchars($url['long_url']); ?>">
                                    <span class="text-sm"><?php echo htmlspecialchars(substr($url['long_url'], 0, 40) . (strlen($url['long_url']) > 40 ? '...' : '')); ?></span>
                                </td>
                                <td>
                                    <div class="flex flex-col gap-1">
                                        <div class="badge badge-primary badge-lg"><?php echo $url['clicks']; ?></div>
                                        <progress class="progress progress-primary w-20" value="<?php echo $url['clicks']; ?>" max="<?php echo $totalClicks > 0 ? $totalClicks : 1; ?>"></progress>
                                    </div>
                                </td>
                                <td class="hidden md:table-cell"><?php echo date('Y-m-d H:i', strtotime($url['created_at'])); ?></td>
                                <td>
                                    <a href="query.php?code=<?php echo $url['short_code']; ?>" class="btn btn-sm btn-outline btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 md:mr-0 lg:mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        <span class="hidden md:hidden lg:inline">编辑</span>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="card-actions justify-end mt-6">
                    <a href="index.php" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        创建新短链接
                    </a>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="card bg-base-100 shadow-xl mx-auto">
            <div class="card-body text-center py-12">
                <div class="flex justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-base-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold mb-4">暂无统计数据</h2>
                <p class="text-lg mb-6">您还没有创建任何短链接，无法显示统计信息</p>
                <a href="index.php" class="btn btn-primary btn-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    立即创建短链接
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <footer class="footer footer-center p-8 bg-base-300 text-base-content mt-12">
        <div class="grid grid-flow-col gap-4">
            <a href="index.php" class="link link-hover">主页</a>
            <a href="query.php" class="link link-hover">查询/编辑</a>
            <a href="stats.php" class="link link-hover">统计</a>
            <a href="includes/init_db.php" class="link link-hover">初始化数据库</a>
        </div>
        <div>
            <p>© <?php echo date('Y'); ?> 短链接生成器 - 将长链接变为短链接的简单工具</p>
        </div>
    </footer>
</body>
</html> 