<?php
require_once 'includes/functions.php';

// 处理表单提交
$shortUrl = '';
$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['long_url'])) {
    $longUrl = trim($_POST['long_url']);
    $customCode = isset($_POST['custom_code']) ? trim($_POST['custom_code']) : '';
    
    if (empty($longUrl)) {
        $error = '请输入有效的URL';
    } else {
        // 验证自定义短码格式
        if (!empty($customCode) && !preg_match('/^[a-zA-Z0-9]{3,10}$/', $customCode)) {
            $error = '自定义短码格式无效，仅允许3-10个字母和数字';
        } else {
            $shortCode = createShortUrl($longUrl, $customCode);
            
            if ($shortCode) {
                // 使用简洁的URL格式
                $shortUrl = BASE_URL . $shortCode;
                $success = true;
            } else {
                $error = '生成短链接失败，请检查URL是否有效或自定义短码是否已被使用';
            }
        }
    }
}

// 获取最近的URL
$recentUrls = getRecentUrls(5);

// 处理重定向
if (isset($_GET['c'])) {
    $shortCode = $_GET['c'];
    $longUrl = getLongUrl($shortCode);
    
    if ($longUrl) {
        header("Location: " . $longUrl);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN" data-theme="cupcake">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>短链接生成器</title>
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
                    <a href="index.php" class="btn btn-primary btn-sm">主页</a>
                    <a href="query.php" class="btn btn-ghost btn-sm ml-2">查询/编辑</a>
                    <a href="stats.php" class="btn btn-ghost btn-sm ml-2">统计</a>
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
                        <li><a href="index.php" class="active">主页</a></li>
                        <li><a href="query.php">查询/编辑</a></li>
                        <li><a href="stats.php">统计</a></li>
                        <li><a href="includes/init_db.php">初始化数据库</a></li>
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
            <h1 class="text-4xl md:text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary to-accent pb-2">短链接生成器</h1>
            <p class="mt-3 text-lg opacity-80 max-w-xl mx-auto">快速将长链接转化为简短易记的链接，方便分享和使用</p>
        </div>
        
        <div class="flex flex-col md:flex-row gap-6 max-w-6xl mx-auto">
            <div class="card bg-base-100 shadow-xl flex-1">
                <div class="card-body">
                    <h2 class="card-title text-primary mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                        创建短链接
                    </h2>
                    
                    <?php if ($error): ?>
                    <div class="alert alert-error mt-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span><?php echo $error; ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($shortUrl): ?>
                    <div class="alert alert-success mt-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>短链接已成功生成！</span>
                    </div>
                    
                    <div class="my-4">
                        <label class="text-sm font-medium">您的短链接:</label>
                        <div class="flex mt-2">
                            <input type="text" id="short-url" value="<?php echo $shortUrl; ?>" class="input input-bordered flex-grow" readonly />
                            <button class="btn btn-primary ml-2" onclick="copyToClipboard()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                </svg>
                                <span class="hidden md:inline ml-1">复制</span>
                            </button>
                        </div>
                        <p id="copy-status" class="text-success text-sm mt-2 hidden">链接已复制到剪贴板！</p>
                    </div>
                    <div class="divider my-4">或者继续创建新的短链接</div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">输入长链接</span>
                            </label>
                            <textarea name="long_url" class="textarea textarea-bordered h-24" placeholder="例如：https://example.com/very/long/url/that/is/hard/to/share"
                                required><?php echo htmlspecialchars($longUrl); ?></textarea>
                        </div>
                        
                        <div class="form-control mt-4">
                            <label class="label">
                                <span class="label-text">自定义短码（可选）</span>
                                <span class="label-text-alt">3-10个字符</span>
                            </label>
                            <input type="text" name="custom_code" class="input input-bordered" placeholder="例如：mylink"
                                pattern="[A-Za-z0-9_-]{3,10}" 
                                title="短码长度为3-10位，仅支持字母、数字、下划线和连字符"
                                value="<?php echo htmlspecialchars($customCode); ?>" />
                        </div>
                        
                        <div class="form-control mt-6">
                            <button type="submit" class="btn btn-primary">生成短链接</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card bg-base-100 shadow-xl w-full md:w-96">
                <div class="card-body">
                    <h2 class="card-title text-secondary mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        使用指南
                    </h2>
                    <div class="space-y-4">
                        <div class="p-3 bg-base-200 rounded-lg">
                            <div class="font-medium">创建短链接</div>
                            <p class="text-sm mt-1 opacity-80">在左侧表单中输入您的长链接，点击"生成短链接"按钮即可获得短链接。您也可以自定义短码部分。</p>
                        </div>
                        
                        <div class="p-3 bg-base-200 rounded-lg">
                            <div class="font-medium">查询与编辑</div>
                            <p class="text-sm mt-1 opacity-80">通过<a href="query.php" class="text-primary">查询/编辑</a>页面，您可以查看短链接详情、修改目标URL或删除短链接。</p>
                        </div>
                        
                        <div class="p-3 bg-base-200 rounded-lg">
                            <div class="font-medium">统计数据</div>
                            <p class="text-sm mt-1 opacity-80">在<a href="stats.php" class="text-primary">统计</a>页面查看所有短链接的使用情况和点击次数。</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 页脚 -->
    <footer class="footer footer-center p-8 bg-base-300 text-base-content mt-12">
        <div class="grid grid-flow-col gap-4">
            <a href="index.php" class="link link-hover">主页</a>
            <a href="query.php" class="link link-hover">查询/编辑</a>
            <a href="stats.php" class="link link-hover">统计</a>
            <a href="includes/init_db.php" class="link link-hover">初始化数据库</a>
        </div>
        <div>
            <div class="grid grid-flow-col gap-4">
                <a class="btn btn-ghost btn-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="fill-current"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"></path></svg>
                </a>
                <a class="btn btn-ghost btn-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="fill-current"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"></path></svg>
                </a>
                <a class="btn btn-ghost btn-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="fill-current"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"></path></svg>
                </a>
            </div>
        </div>
        <div>
            <p>© <?php echo date('Y'); ?> 短链接生成器 - 将长链接变为短链接的简单工具</p>
        </div>
    </footer>
    
    <script>
        function copyToClipboard() {
            var copyText = document.getElementById("short-url");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);
            
            var copyStatus = document.getElementById("copy-status");
            copyStatus.classList.remove("hidden");
            
            setTimeout(function() {
                copyStatus.classList.add("hidden");
            }, 2000);
        }
    </script>
</body>
</html> 