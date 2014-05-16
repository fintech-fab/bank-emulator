<?php

/**
 * @var array $data
 */

$report = http_build_query($data);
$report = str_replace('&', "\n", $report);
$report = urldecode($report);
echo $report;
