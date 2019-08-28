###############################################################################
#
# PHP Syntax Check for Git pre-commit hook for Windows PowerShell
#
# Author: Vojtech Kusy &lt;wojtha@gmail.com&gt;
# Author: Chuck "MANCHUCK" Reeves &lt;chuck@manchuck.com&gt;
# Author: Andrew 'Ziggy' Dempster
#
###############################################################################

### INSTRUCTIONS ###

# Place the code to file "pre-commit" (no extension) and add it to the one of
# the following locations:
# 1) Repository hooks folder - C:\Path\To\Repository\.git\hooks
# 2) User profile template   - C:\Users\&lt;USER&gt;\.git\templates\hooks
# 3) Global shared templates - C:\Program Files (x86)\Git\share\git-core\templates\hooks
#
# The hooks from user profile or from shared templates are copied from there
# each time you create or clone new repository.

### SETTINGS ###

# Path to the php.exe
$php_exe = "C:\php\php.exe";

# Path to the phpcs
$php_cs = "phpcs";

# Extensions of the PHP files
$php_ext = 'php|module|inc|install|test|profile|theme|txt|class';

# Flag, if set to $true git will unstage all files with errors, set to $false to disable
$unstage_on_error = 0;

### FUNCTIONS ###

## PHP Lint
function php_syntax_check {
    param([string]$php_bin, [string]$extensions, [bool]$reset)

    $err_counter = 0;
    $file_counter = 0;

    write-host "PHP syntax check:" -foregroundcolor "white" -backgroundcolor "black"

    # loop through all commited files
    git diff-index --name-only --cached --diff-filter=AM HEAD -- | foreach {
        # only match php files
         if ($_ -match ".*\.($extensions)$") {
            $file_counter++;
            $file = $matches[0];
            $errors = & $php_bin -l $file

            write-host $file ": "  -foregroundcolor "gray"  -backgroundcolor "black" -NoNewline
            if ($errors -match "No syntax errors detected in $file") {
                write-host "OK!" -foregroundcolor "green" -backgroundcolor "black"
            } else {
                write-host "ERROR! " $errors -foregroundcolor "red" -backgroundcolor "black"
                if ($reset) {
                    git reset -q HEAD $file
                    write-host "Unstaging ..." -foregroundcolor "magenta" -backgroundcolor "black"
                }
                $err_counter++
            }
        }
    }

    # output report
    write-host "Checked" $file_counter "File(s)" -foregroundcolor "gray" -backgroundcolor "black"
    if ($err_counter -gt 0) {
        write-host "Some File(s) have syntax errors. Please fix then commit" -foregroundcolor "red" -backgroundcolor "black"
        exit 1
    }
}

# PHP Code Sniffer Check
function php_cs_check {
    param([string]$php_cs, [string]$extensions, [bool]$reset)

    $err_counter = 0;
    $file_counter = 0;

    write-host "PHP codesniffer check:" -foregroundcolor "white" -backgroundcolor "black"

    # Loop through all commited files
    git diff-index --name-only --cached --diff-filter=AM HEAD -- | foreach {
        # only run lint if file extensions match
        if ($_ -match ".*\.($extensions)$") {
            $file_counter++;
            $file = $matches[0];

            write-host $file ": "  -foregroundcolor "gray"  -backgroundcolor "black" -NoNewline

            # skip test files
            if ($file -match "test\/") {
                write-host "SKIPPED! (test file)" -foregroundcolor "darkGreen" -backgroundcolor "black"
            } else {
                $errors = & $php_cs --standard=Drupal $file

                # Outputs the error
                if ($LastExitCode) {
                    write-host "FAILED! (contains errors)"  -foregroundcolor "red" -backgroundcolor "black"
                    write-host
                    write-output $errors
                    if ($reset) {
                        git reset -q HEAD $file
                        write-host "Unstaging ..." -foregroundcolor "magenta" -backgroundcolor "black"
                    }
                    $err_counter++
                } else {
                    write-host "PASSED!" -foregroundcolor "green" -backgroundcolor "black"
                }
            }
        }
    }

    # output report
    write-host "Checked" $file_counter "File(s)" -foregroundcolor "gray" -backgroundcolor "black"
    if ($err_counter -gt 0) {
        write-host "Some File(s) are not following proper codeing standards. Please fix then commit" -foregroundcolor "red" -backgroundcolor "black"
        exit 1
    }
}

### MAIN ###
php_syntax_check $php_exe $php_ext $unstage_on_error
write-host

php_cs_check $php_cs $php_ext $unstage_on_error
write-host