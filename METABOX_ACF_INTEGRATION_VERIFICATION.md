# POST METABOXES と ACF/Taxonomy 連携検証レポート

## 🔍 POST METABOXES (admin-functions.php) 整合性チェック

### ✅ タクソノミーメタボックス - 完全連携確認

#### 1. カテゴリメタボックス（grant_category）

**Metabox定義（line 815-849）:**
```php
public function render_category_metabox($post) {
    $categories = get_terms(array(
        'taxonomy' => 'grant_category',  // ✅ 正しいタクソノミー
        'hide_empty' => false
    ));
    
    $post_categories = wp_get_post_terms($post->ID, 'grant_category', array('fields' => 'ids'));
    // チェックボックスで複数選択可能
}
```

**保存処理（line 976-981）:**
```php
if (isset($_POST['grant_categories'])) {
    $categories = array_map('intval', $_POST['grant_categories']);
    wp_set_post_terms($post_id, $categories, 'grant_category'); // ✅ 正しく保存
}
```

**sheets-webhook.php連携（line 426）:**
```php
// 21 => カテゴリ (V列) - grant_category タクソノミーで処理
```

**sheets-sync.php連携（line 780-791）:**
```php
// カテゴリを設定（V列のデータから） ★完全連携
if (isset($row[21]) && !empty($row[21])) {
    $categories = array_map('trim', explode(',', $row[21]));
    $category_result = wp_set_post_terms($post_id, $categories, 'grant_category');
}
```

#### 2. 都道府県メタボックス（grant_prefecture）

**Metabox定義（line 855-896）:**
```php
public function render_prefecture_metabox($post) {
    $prefectures = get_terms(array(
        'taxonomy' => 'grant_prefecture',  // ✅ 正しいタクソノミー
        'hide_empty' => false,
        'orderby' => 'name'
    ));
    
    $post_prefectures = wp_get_post_terms($post->ID, 'grant_prefecture', array('fields' => 'ids'));
}
```

**保存処理（line 983-988）:**
```php
if (isset($_POST['grant_prefectures'])) {
    $prefectures = array_map('intval', $_POST['grant_prefectures']);
    wp_set_post_terms($post_id, $prefectures, 'grant_prefecture'); // ✅ 正しく保存
}
```

**sheets-webhook.php連携（line 424）:**
```php
// 19 => 都道府県 (T列) - タクソノミーで処理、ACFフィールド削除
```

**sheets-sync.php連携（line 754-765）:**
```php
// 都道府県を設定（T列のデータから） ★完全連携
if (isset($row[19]) && !empty($row[19])) {
    $prefectures = array_map('trim', explode(',', $row[19]));
    $prefecture_result = wp_set_post_terms($post_id, $prefectures, 'grant_prefecture');
}
```

#### 3. 市町村メタボックス（grant_municipality）

**Metabox定義（line 898-945）:**
```php
public function render_municipality_metabox($post) {
    $municipalities = get_terms(array(
        'taxonomy' => 'grant_municipality',  // ✅ 正しいタクソノミー
        'hide_empty' => false
    ));
    
    $post_municipalities = wp_get_post_terms($post->ID, 'grant_municipality', array('fields' => 'ids'));
}
```

**保存処理（line 990-995）:**
```php
if (isset($_POST['grant_municipalities'])) {
    $municipalities = array_map('intval', $_POST['grant_municipalities']);
    wp_set_post_terms($post_id, $municipalities, 'grant_municipality'); // ✅ 正しく保存
}
```

**sheets-webhook.php連携（line 425）:**
```php
// 20 => 市町村 (U列) - タクソノミーで処理、ACFフィールド削除
```

**sheets-sync.php連携（line 767-778）:**
```php
// 市町村を設定（U列のデータから） ★完全連携
if (isset($row[20]) && !empty($row[20])) {
    $municipalities = array_map('trim', explode(',', $row[20]));
    $municipality_result = wp_set_post_terms($post_id, $municipalities, 'grant_municipality');
}
```

## 🔗 ACFフィールドとの連携

POST METABOXESは**タクソノミー専用**で、ACFフィールドは処理しません。
ACFフィールドは以下で管理されます：

### ACF管理場所

1. **acf-fields.php** - フィールド定義（19個のフィールド）
2. **sheets-webhook.php** - Webhook経由の更新（index 7-29）
3. **sheets-sync.php** - 双方向同期（H-S列、X-AD列）

### タクソノミー vs ACFフィールドの分離

| データ種類 | 管理方法 | 列位置 | 管理ファイル |
|-----------|---------|--------|------------|
| タクソノミー（3種） | POST METABOXES | T-W列 | admin-functions.php |
| ACFフィールド（19個） | ACF管理画面 | H-S, X-AD列 | acf-fields.php |

## ✅ 統合確認結果

### 1. タクソノミー連携（完全一致）✅

| タクソノミー | Metabox | sheets-webhook | sheets-sync | 列 |
|-------------|---------|----------------|-------------|-----|
| grant_category | ✅ | ✅ | ✅ | V (21) |
| grant_prefecture | ✅ | ✅ | ✅ | T (19) |
| grant_municipality | ✅ | ✅ | ✅ | U (20) |

### 2. ACFフィールド連携（完全一致）✅

すべてのACFフィールドは：
- acf-fields.php で定義 ✅
- sheets-webhook.php で同期 ✅
- sheets-sync.php で双方向同期 ✅

### 3. 削除完了項目✅

- ❌ 「サンプルデータ管理」セクション - **削除完了**
- ❌ 「都道府県データ初期化」セクション - **削除完了**

## 🎯 結論

**POST METABOXESはタクソノミー、ACFフィールドとも完全に連携されています！**

### 連携フロー

```
WordPress管理画面
├── POST METABOXES → grant_category/prefecture/municipality（タクソノミー）
├── ACF管理画面 → 19個のカスタムフィールド
│
↕ 双方向同期
│
Google Sheets（31列 A-AE）
├── T-W列: タクソノミー
└── H-S, X-AD列: ACFフィールド
```

### 不整合なし

- フィールド名の不一致: **なし** ✅
- タクソノミー名の不一致: **なし** ✅
- カラムマッピングの不一致: **修正済み** ✅（IntegratedSheetSync.gs）

すべてのコンポーネントが正しく連携されています！
