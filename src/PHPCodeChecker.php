<?php
/**
 * 挂载Git钩子 用于检测php语法风格.
 * User: giles <giles.wang@qq.com>
 * Date: 2019/8/28
 * Time: 09:35
 */

namespace MountHooks;

class PHPCodeChecker
{
    protected static $DS =  DIRECTORY_SEPARATOR;

    public static function hookInstall()
    {
        self::checkEnvironment();
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            self::installWindows();
        } else {
            self::installNoWindows();
        }
    }

    /**
     * 检测本地环境
     */
    private static function checkEnvironment()
    {
        # check git
        echo "Checking git repository...\n";
        if (!is_dir(".". self::$DS .".git")) {
            echo "Your project has not been init by git! Please check it...\n";
            exit(1);
        }

        # check phplint
        echo "Checking phplint install...\n";
        exec('.'. self::$DS .'vendor'. self::$DS .'bin'. self::$DS .'phplint --version', $phpLintCheckRs, $returnVar);
        if ($returnVar) {
            echo "Checking phplint failed! Please install phplint first!";
            exit(1);
        } else {
            echo "Checking phplint success!\n";
            echo $phpLintCheckRs[0] . "\n";
        }

        # check phpcs
        echo "Checking phpcs install...\n";
        exec('.'. self::$DS .'vendor'. self::$DS .'bin'. self::$DS .'phpcs --version', $phpCsCheckRs, $returnVar);
        if ($returnVar) {
            echo "Checking phpcs failed! Please install phpcs first!";
            exit(1);
        } else {
            echo "Checking phpcs success!\n";
            echo $phpCsCheckRs[0] . "\n";
        }
    }

    /**
     * 挂载非Windows机器的git hooks
     */
    private static function installNoWindows()
    {
        # check&&back pre-commit
        if (is_file('./.git/hooks/pre-commit')
            && md5_file('./.git/hooks/pre-commit') != md5_file('./vendor/webergiles/php-csc/pre-commit')) {
            system('mv ./.git/hooks/pre-commit ./.git/hooks/pre-commit.bak.' . time());
        }
        if (is_file('./.git/hooks/pre-commit')
            && md5_file('./.git/hooks/pre-commit') == md5_file('./vendor/webergiles/php-csc/pre-commit')) {
            echo "php-csc install success!\n";
            exit(0);
        }

        system('cp ./vendor/webergiles/php-csc/src/pre-commit ./.git/hooks');
        system('chmod +x .git/hooks/pre-commit');

        echo "php-csc install success!\n";
        exit(0);
    }

    /**
     * 挂载非Windows机器的git hooks
     */
    private static function installWindows()
    {
        # check&&back pre-commit
        if (is_file('.\.git\hooks\pre-commit')
            && md5_file('.\.git\hooks\pre-commit') != md5_file('.\vendor\webergiles\php-csc\pre-commit')) {
            system('xcopy /s /f /y .\.git\hooks\pre-commit .\.git\hooks\pre-commit.bak.' . time());
        }
        if (is_file('.\.git\hooks\pre-commit')
            && md5_file('.\.git\hooks\pre-commit') == md5_file('.\vendor\webergiles\php-csc\pre-commit')) {
            echo "php-csc install success!\n";
            exit(0);
        }

        system('xcopy /s /f /y "vendor\webergiles\php-csc\src\pre-commit.win" ".git\hooks\pre-commit"');
        system('xcopy /s /f /y "vendor\webergiles\php-csc\src\pre-commit.ps1" ".git\hooks\pre-commit.ps1"');

        echo "php-csc install success!\n";
        exit(0);
    }
}
