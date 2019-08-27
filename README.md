# 介绍

PHP code style check 利用git hook、phplint、phpcs在git commit的时候对php代码进行语法检测、代码风格检查，如果有问题，不允许提交。

# 使用
composer require --dev webergiles/php-csc

安装成功之后执行`composer exec phpcsc install`该命令会检查phplint、phpcs的安装情况，并将git原有的pre-commit钩子备份，再将php-cc的pre-commit钩子拷贝至.git/hooks中。

这样，在git commit之前，就会执行phplint和phpcs检查待提交的文件，如果不满足要求，则会组织代码提交。

# 指令

指令 (composer exec -v phpcsc {指令} | 用法
--- | --- |
install |	安装php-csc
remove  |	移除php-csc

#注意事项
phpcsc的pre-commit会覆盖原有的pre-commit，但仍然会将它备份为pre-commit.bak.{timestamp}。所以之前有在pre-commit中插入操作，请谨慎安装。
