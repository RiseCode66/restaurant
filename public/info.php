<?php
echo "<pre>";
print_r(scandir('/var/log/faillog'));
print_r(scandir('/var/www/html/public/'));
echo "</pre>";
?>
