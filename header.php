<?php
// header.php
session_start();

// 默认语言设置
$default_lang = 'en';

// 获取语言参数（优先从URL获取，其次从session）
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// 确定当前语言
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : $default_lang;

// 加载语言文件
$lang_file = "lang_{$current_lang}.php";
if (file_exists($lang_file)) {
    include $lang_file;
} else {
    include "lang_en.php"; // 默认加载英语
}
?>