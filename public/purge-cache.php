<?php
header("X-LiteSpeed-Purge: *");
header("Content-Type: text/plain");
echo "LiteSpeed cache purge requested at " . date("Y-m-d H:i:s") . "\n";
echo "Please wait 30 seconds and test again.\n";
?>
