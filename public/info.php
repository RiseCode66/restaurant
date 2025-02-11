<?php
// info.php
$output = shell_exec('php /var/www/html/bin/console debug:router');
echo "<pre>$output</pre>";
