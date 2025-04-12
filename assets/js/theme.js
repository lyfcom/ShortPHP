// 主题切换功能
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    const themeToggleMobile = document.getElementById('theme-toggle-mobile');
    const html = document.documentElement;
    
    // 检查本地存储是否有保存的主题
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        html.setAttribute('data-theme', savedTheme);
        const isDarkTheme = savedTheme === 'dark';
        
        // 设置两个切换按钮的状态
        if (themeToggle) {
            themeToggle.checked = isDarkTheme;
        }
        if (themeToggleMobile) {
            themeToggleMobile.checked = isDarkTheme;
        }
    }
    
    // 切换主题函数
    function toggleTheme(isChecked) {
        const newTheme = isChecked ? 'dark' : 'cupcake';
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        
        // 同步两个按钮的状态
        if (themeToggle) themeToggle.checked = isChecked;
        if (themeToggleMobile) themeToggleMobile.checked = isChecked;
    }
    
    // 桌面版切换事件
    if (themeToggle) {
        themeToggle.addEventListener('change', function() {
            toggleTheme(this.checked);
        });
    }
    
    // 移动版切换事件
    if (themeToggleMobile) {
        themeToggleMobile.addEventListener('change', function() {
            toggleTheme(this.checked);
        });
    }
}); 