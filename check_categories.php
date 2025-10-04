<?php
/**
 * カテゴリ確認スクリプト
 */

// WordPressをロード
require_once('wp-load.php');

echo "=== grant_category タクソノミーの全タームを表示 ===\n\n";

$categories = get_terms(array(
    'taxonomy' => 'grant_category',
    'hide_empty' => false,
    'orderby' => 'name'
));

if (empty($categories) || is_wp_error($categories)) {
    echo "カテゴリが見つかりませんでした。\n";
    exit;
}

echo "合計: " . count($categories) . "個のカテゴリ\n\n";
echo str_repeat("=", 80) . "\n";
printf("%-5s %-40s %-10s %-10s\n", "ID", "カテゴリ名", "投稿数", "スラッグ");
echo str_repeat("=", 80) . "\n";

foreach ($categories as $cat) {
    printf("%-5d %-40s %-10d %-10s\n", 
        $cat->term_id, 
        mb_strimwidth($cat->name, 0, 40, '...'),
        $cat->count,
        $cat->slug
    );
}

echo str_repeat("=", 80) . "\n\n";

// 重複チェック
$names = array();
$duplicates = array();

foreach ($categories as $cat) {
    if (in_array($cat->name, $names)) {
        $duplicates[] = $cat->name;
    }
    $names[] = $cat->name;
}

if (!empty($duplicates)) {
    echo "⚠️ 重複しているカテゴリ名:\n";
    foreach (array_unique($duplicates) as $dup) {
        echo "  - " . $dup . "\n";
    }
} else {
    echo "✅ 重複しているカテゴリはありません。\n";
}
