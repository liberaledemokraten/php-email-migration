<?php
setReturnHeader(503); // Send HTTP 503 Service Unavailable
header("Retry-After: 900"); // set estimated time to recover service

// disable browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

echo "This service is currently under maintenance.\n\n";
die();
?>