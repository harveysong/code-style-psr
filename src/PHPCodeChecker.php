<?php
/**
 * 挂载Git钩子 用于检测php语法风格.
 * User: giles <giles.wang@qq.com>
 * Date: 2019/8/28
 * Time: 09:35
 */

namespace Giles\MountHooks;

defined('DS') || define('DS', DIRECTORY_SEPARATOR); // 目录分隔符简写

class PHPCodeChecker
{
    protected static $DS =  DIRECTORY_SEPARATOR;

    public static function hookInstall()
    {
        $os = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'windows' : 'author';
        self::checkEnvironment($os);
    }

    public static function hookUninstall()
    {
        $os = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'windows' : 'author';
        if ($os == 'windows') {
            system('ren .git'. DS .'hooks'. DS .'pre-commit pre-commit.bak.'. time());
            echo "Remove phpcsc success!\n";
        } else {
            $oldHook ='.'. DS .'.git'. DS .'hooks'. DS .'pre-commit';
            system('mv '. $oldHook .' .'. DS .'.git'. DS .'hooks'. DS .'pre-commit.bak.' . time());
            echo "Remove phpcsc success!\n";
        }
    }

    /**
     * 挂载git hook
     * @param string $os 默认环境 winodws
     */
    private static function checkEnvironment($os = 'windows')
    {
        # check git
        echo "Checking git repository...\n";
        if (!is_dir(".". DS .".git")) {
            echo "Your project has not been init by git! Please check it...\n";
            exit(1);
        }

        # check phplint
        echo "Checking phplint install...\n";
        exec('.'. DS .'vendor'. DS .'bin'. DS .'phplint --version', $phpLintCheckRs, $returnVar);
        if ($returnVar) {
            echo "Checking phplint failed! Please install phplint first!";
            exit(1);
        } else {
            echo "Checking phplint success!\n";
            echo $phpLintCheckRs[0] . "\n";
        }

        # check phpcs
        echo "Checking phpcs install...\n";
        exec('.'. DS .'vendor'. DS .'bin'. DS .'phpcs --version', $phpCsCheckRs, $returnVar);
        if ($returnVar) {
            echo "Checking phpcs failed! Please install phpcs first!";
            exit(1);
        } else {
            echo "Checking phpcs success!\n";
            echo $phpCsCheckRs[0] . "\n";
        }

        if (is_file('.'. DS .'.git'. DS .'hooks'. DS .'pre-commit')) {
            $fileOldMd5 = md5_file('.'. DS .'.git'. DS .'hooks'. DS .'pre-commit');
            $fileNewMd5 = $os  == 'windows'
                ? md5_file('.'. DS .'vendor'. DS .'giles'. DS .'php-csc'. DS .'src'. DS .'pre-commit.win')
                : md5_file('.'. DS .'vendor'. DS .'giles'. DS .'php-csc'. DS .'src'. DS .'pre-commit');
            if ($fileOldMd5 == $fileNewMd5) {
                echo "php-csc install success!\n";
                exit(0);
            } else {
                if ($os == 'windows') {
                    system('ren .git'. DS .'hooks'. DS .'pre-commit pre-commit.bak.'. time());
                } else {
                    $oldHook ='.'. DS .'.git'. DS .'hooks'. DS .'pre-commit';
                    system('mv '. $oldHook .' .'. DS .'.git'. DS .'hooks'. DS .'pre-commit.bak.' . time());
                }
            }
        }
        if ($os == 'windows') {
            system('copy vendor\giles\php-csc\src\pre-commit.win .git\hooks\pre-commit');
            system('copy vendor\giles\php-csc\src\pre-commit.ps1 .git\hooks\pre-commit.ps1');
        } else {
            system('cp ./vendor/giles/php-csc/src/pre-commit ./.git/hooks/');
            system('tr -d \'\r\' < ./vendor/giles/php-csc/src/pre-commit > ./.git/hooks/pre-commit');
            system('chmod +x .git/hooks/pre-commit');
        }

        echo "php-csc install success!\n";
        exit(0);
    }
}
