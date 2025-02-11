<?php
// info.php
$output = shell_exec('php bin/console router:match /api/client/get');
echo "<pre>$output</pre>";
