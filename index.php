<?php
define('APP_NAME',    'Markdown Previewer');
define('APP_VERSION', '1.0');
define('APP_URL',     'https://github.com/u01jmg3');

$api        = 'https://api.github.com/markdown/raw';
$filename   = null;
$base_dir   = dirname(__FILE__);
$template   = file_get_contents("{$base_dir}/template.html");
$stylesheet = file_get_contents("{$base_dir}/markdown.css");

if (isset($_SERVER['argv'])) {
    $filename = $_SERVER['argv'][1];
} else if (isset($_GET['m'])) {
    $filename = $_GET['m'];
}

if (empty($filename)) {
    $filename = 'README.md';
}

$ch = curl_init($api);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($filename));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: text/plain']);
curl_setopt($ch, CURLOPT_USERAGENT, sprintf('%s v%s %s', APP_NAME, APP_VERSION, APP_URL));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$markdown = curl_exec($ch);
curl_close($ch);

ob_start(function($buffer) {
    global $markdown, $filename, $stylesheet;

    $buffer = str_replace('%title%', basename($filename), $buffer);
    $buffer = str_replace('%stylesheet%', $stylesheet, $buffer);
    $buffer = str_replace('%markdown%', $markdown, $buffer);

    return $buffer;
});

echo $template;
ob_end_flush();