<?php
require_once 'includes/functions.php';

$shortCode = '';
$longUrl = '';
$error = '';
$success = '';
$deleted = false;

// 处理删除操作
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete']) && isset($_POST['short_code'])) {
    $shortCode = trim($_POST['short_code']);
    
    if (deleteShortUrl($shortCode)) {
        $success = '短链接已成功删除';
        $deleted = true;
        $longUrl = '';
    } else {
        $error = '删除短链接失败，请稍后重试';
    }
}

// 处理查询
if (isset($_GET['code']) && !empty($_GET['code'])) {
    $shortCode = trim($_GET['code']);
    $result = getUrlDetails($shortCode);
    
    if ($result) {
        $longUrl = $result['long_url'];
    } else {
        $error = '未找到该短链接，请检查输入是否正确';
    }
}

// 处理编辑提交
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $shortCode = trim($_POST['short_code']);
    $newLongUrl = trim($_POST['new_long_url']);
    
    if (empty($newLongUrl) || !filter_var($newLongUrl, FILTER_VALIDATE_URL)) {
        $error = '请输入有效的URL';
    } else {
        if (updateLongUrl($shortCode, $newLongUrl)) {
            $success = '长链接已成功更新';
            $longUrl = $newLongUrl;
        } else {
            $error = '更新失败，请稍后重试';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN" data-theme="cupcake">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查询与编辑短链接 - 短链接生成器</title>
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
                    <a href="query.php" class="btn btn-primary btn-sm ml-2">查询/编辑</a>
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
                        <li><a href="index.php">主页</a></li>
                        <li><a href="query.php" class="active">查询/编辑</a></li>
                        <li><a href="stats.php">统计</a></li>
                        <li>
                            <label class="swap swap-rotate">
                                <input type="checkbox" id="theme-toggle" class="theme-controller" />
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
            <h1 class="text-4xl md:text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-secondary to-primary pb-2">短链接查询与编辑</h1>
            <p class="mt-3 text-lg opacity-80 max-w-xl mx-auto">查询短链接对应的原始URL，并可以修改或删除</p>
        </div>
        
        <div class="card bg-base-100 shadow-xl mx-auto max-w-2xl">
            <div class="card-body">
                <h2 class="card-title text-secondary mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    查询短链接
                </h2>
                
                <form method="GET" action="">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">输入短链接代码</span>
                        </label>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <div class="flex flex-1">
                                <span class="bg-base-300 px-4 flex items-center rounded-l-lg text-sm"><?php echo BASE_URL; ?></span>
                                <input type="text" name="code" placeholder="输入短链接代码" 
                                    class="input input-bordered w-full rounded-l-none" required
                                    value="<?php echo htmlspecialchars($shortCode); ?>" />
                            </div>
                            <button class="btn btn-primary px-6" type="submit">查询</button>
                        </div>
                        <label class="label">
                            <span class="label-text-alt">输入短链接最后的代码部分，例如：AbC123</span>
                        </label>
                    </div>
                </form>
                
                <?php if ($error): ?>
                <div class="alert alert-error mt-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span><?php echo $error; ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                <div class="alert alert-success mt-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span><?php echo $success; ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($longUrl && !$deleted): ?>
                <!-- 查询结果显示 -->
                <div class="mt-8">
                    <div class="card bg-base-200">
                        <div class="card-body">
                            <h2 class="card-title flex items-center text-primary mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                查询结果
                            </h2>
                            
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="py-2">
                                    <p class="text-sm font-semibold mb-1">短链接：</p>
                                    <div class="flex">
                                        <input type="text" id="short-url" value="<?php echo BASE_URL . htmlspecialchars($shortCode); ?>" 
                                            class="input input-bordered input-sm w-full font-mono" readonly />
                                        <button class="btn btn-square btn-sm btn-primary ml-1" onclick="copyToClipboard('short-url')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <?php 
                                // 获取更多详情
                                $details = getUrlDetails($shortCode);
                                if ($details): 
                                ?>
                                <div class="py-2">
                                    <p class="text-sm font-semibold mb-1">使用情况：</p>
                                    <div class="flex flex-wrap gap-2">
                                        <div class="badge badge-lg badge-primary"><?php echo $details['clicks']; ?> 次点击</div>
                                        <div class="badge badge-lg badge-secondary">创建于：<?php echo date('Y-m-d H:i', strtotime($details['created_at'])); ?></div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="py-2 mt-2">
                                <p class="text-sm font-semibold mb-1">原始链接：</p>
                                <div class="bg-base-100 p-3 rounded break-all text-sm"><?php echo htmlspecialchars($longUrl); ?></div>
                            </div>
                            
                            <div class="mt-4">
                                <a href="<?php echo BASE_URL . htmlspecialchars($shortCode); ?>" target="_blank" class="btn btn-outline btn-primary btn-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    访问短链接
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 编辑区域 -->
                <div class="divider my-6">
                    <span class="badge badge-lg">编辑操作</span>
                </div>
                
                <form method="POST" action="">
                    <input type="hidden" name="short_code" value="<?php echo htmlspecialchars($shortCode); ?>">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">修改长链接</span>
                        </label>
                        <textarea name="new_long_url" class="textarea textarea-bordered h-24" placeholder="输入新的长链接" required><?php echo htmlspecialchars($longUrl); ?></textarea>
                    </div>
                    
                    <div class="form-control mt-6 flex flex-col sm:flex-row gap-4 items-center justify-between">
                        <button type="submit" name="update" class="btn btn-primary w-full sm:w-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            更新链接
                        </button>
                        
                        <div class="dropdown dropdown-end w-full sm:w-auto">
                            <label tabindex="0" class="btn btn-error btn-outline w-full sm:w-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                删除短链接
                            </label>
                            <div tabindex="0" class="dropdown-content z-[1] card card-compact w-72 p-2 shadow bg-base-100 text-base-content mt-2">
                                <div class="card-body">
                                    <h3 class="font-bold text-lg text-error">确认删除</h3>
                                    <p>删除后无法恢复，确定要删除此短链接吗？</p>
                                    <div class="card-actions justify-end mt-2">
                                        <button type="button" class="btn btn-sm" onclick="this.closest('.dropdown').removeAttribute('open')">取消</button>
                                        <button type="submit" name="delete" class="btn btn-sm btn-error">确认删除</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <footer class="footer footer-center p-8 bg-base-300 text-base-content mt-12">
        <div class="grid grid-flow-col gap-4">
            <a href="index.php" class="link link-hover">主页</a>
            <a href="query.php" class="link link-hover">查询/编辑</a>
            <a href="stats.php" class="link link-hover">统计</a>
        </div>
        <div>
            <p>© <?php echo date('Y'); ?> 短链接生成器 - 将长链接变为短链接的简单工具</p>
        </div>
    </footer>
    
    <script>
        function copyToClipboard(elementId) {
            var copyText = document.getElementById(elementId);
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);
            
            // 显示复制成功提示
            const toast = document.createElement('div');
            toast.className = 'toast toast-top toast-center';
            toast.innerHTML = `
                <div class="alert alert-success">
                    <span>已复制到剪贴板！</span>
                </div>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 2000);
        }
    </script>
</body>
</html> 