# 介绍

PHP code style check 利用git hook、phplint、phpcs在git commit的时候对php代码进行语法检测、代码风格检查，如果有问题，不允许提交。

# 前置要求
 - Git已安装
 - PHP安装并全局可用 Windows下载最新版php [php-7.3.8-nts-Win32-VC15-x64.zip](https://windows.php.net/downloads/releases/php-7.3.8-nts-Win32-VC15-x64.zip)
 - Composer可用 [Windows下载 Composer](https://getcomposer.org/Composer-Setup.exe)

## 检测原理 及流程
- 每次git commit前是有hooks可以触发
- 开发特定脚本,在git hooks触发的时候执行
- 首先利用phpline检测提交文件的语法是否有错
- 再次利用phpcs检测php的风格规范是否是否符合特定的规范
- 符合规范 可以继续执行git push
- 不符合规范，本次commit失败，需要根据错误提示更改 
- 再次 git add && git commit

# 使用
composer require --dev giles/php-csc

该命令会根据本机系统检查phplint、phpcs的安装情况，并将git原有的pre-commit钩子备份，
再将php-csc的pre-commit钩子拷贝至.git/hooks中。

这样，在git commit之前，就会执行phplint和phpcs检查待提交的文件，如果不满足要求，则会阻止代码提交。

# 开放指令

指令 (composer exec -v phpcsc {指令} | 用法
--- | --- |
install |	安装php-csc
remove  |	移除php-csc

# composer 自动挂载
在主项目composer 文件中增加事件
```json
"post-autoload-dump": [
    "Giles\\MountHooks\\PHPCodeChecker::hookInstall"
],
"pre-package-uninstall": [
    "Giles\\MountHooks\\PHPCodeChecker::hookUnstall"
]
```
可以在没次执行composer update 的时候去检测钩子挂载情况,自动挂载钩子

# 注意事项
- phpcsc的pre-commit会覆盖原有的pre-commit，但仍然会将它备份为pre-commit.bak.{timestamp}。所以之前有在pre-commit中插入操作，请谨慎安装。

- Windows版本的PHPStorm 默认回车符\r\n 这是不符合PSR2 规范的 需要设置为\n 
`File->Line Separators->LF - Unix and macOs (\n)`
