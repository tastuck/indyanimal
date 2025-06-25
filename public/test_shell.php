<?php
/**
 * Author: Taniya Tucker
 * Date: 6/25/25
 * File: test_shell.php
 * Description:
 */
?>

<?php
$output = shell_exec("echo hello from shell");
echo "<pre>$output</pre>";

