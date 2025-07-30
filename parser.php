<?php
require_once 'vendor/autoload.php'; // Assuming Parsedown is installed via Composer

function load_page($page) {
    $file_path = "content/{$page}.md";
    if (file_exists($file_path)) {
        return file_get_contents($file_path);
    } else {
        return '# Page Not Found';
    }
}

function parse_content($content, $page) {
    $parsedown = new Parsedown();
    $html = $parsedown->text($content);

    return $html;
}
