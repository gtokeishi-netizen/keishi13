# 🚀 強化されたAI機能の使用方法

## 📋 概要

このアップデートでは、**UIを一切変更せず**、バックエンドのAI機能を大幅に強化しました。
ユーザーは既存のインターフェースをそのまま使用しながら、より賢く・より速く・より正確な結果を得られます。

---

## 🎯 新機能一覧

### 1. **セマンティック検索 (Semantic Search)**

**従来の検索**:
- キーワードマッチのみ
- 「IT補助金」で検索すると「IT」「補助金」を含む結果のみ

**新しいセマンティック検索**:
- 意味を理解して検索
- 「IT補助金」で検索すると、「デジタル化支援」「システム導入助成」なども表示
- OpenAI Embeddingsによるベクトル類似度マッチング

**使用方法**:
```javascript
// 既存のAJAXエンドポイントがそのまま使える
jQuery.post(ajaxurl, {
    action: 'gi_semantic_search',
    query: 'スタートアップ向けの資金調達'
}, function(response) {
    // response.results に類似度でランク付けされた結果
    // response.method: 'semantic' または 'keyword'
});
```

**特徴**:
- ✅ 自動フォールバック: OpenAI APIが使えない時は従来の検索
- ✅ キャッシュ機能: 一度計算した埋め込みは24時間キャッシュ
- ✅ 高速レスポンス: キャッシュヒット時は500ms以下

---

### 2. **コンテキスト記憶 (Context Memory)**

**従来のチャット**:
- 毎回独立した質問として処理
- 前の会話を覚えていない

**新しいコンテキスト記憶**:
- 過去10件の会話を記憶
- 「それについて詳しく」などの指示代名詞を理解
- パーソナライズされた回答

**使用方法**:
```javascript
// 既存のチャットAJAXがそのまま強化される
jQuery.post(ajaxurl, {
    action: 'gi_contextual_chat',
    message: 'それの申請方法は？'
}, function(response) {
    // 前の会話を理解した回答が返る
    // response.related_grants: 関連助成金も提案
});
```

**特徴**:
- ✅ セッションベース: ログイン不要でも機能
- ✅ ユーザー履歴: ログインユーザーは永続保存
- ✅ 自動クリーンアップ: 30日以上古いデータは自動削除

---

### 3. **スマートキャッシュシステム**

**データベーステーブル**:

```sql
-- ベクトル埋め込みキャッシュ
wp_gi_embeddings_cache
- 助成金ごとのベクトル表現を保存
- 24時間有効
- 内容変更時は自動再計算

-- ユーザーコンテキスト
wp_gi_user_context
- 検索・チャット履歴
- セッションID + ユーザーID
- 30日間保持
```

**パフォーマンス**:
- 初回検索: ~2秒
- キャッシュヒット後: <500ms
- チャット応答: ~3秒

---

## 🔧 セットアップ手順

### ステップ1: OpenAI APIキーの設定

WordPressの管理画面で:
1. 設定 → Grant Insight
2. OpenAI APIキーを入力
3. 「接続テスト」をクリック

または、`wp-config.php`に追加:
```php
define('OPENAI_API_KEY', 'sk-proj-xxxxx');
```

### ステップ2: データベーステーブルの作成

新しいテーブルは **自動的に作成** されます。
初回アクセス時に自動セットアップが実行されます。

確認方法:
```sql
SHOW TABLES LIKE 'wp_gi_embeddings_cache';
SHOW TABLES LIKE 'wp_gi_user_context';
```

### ステップ3: 既存助成金データの埋め込み生成 (オプション)

```php
// WordPressの管理画面 → Tools → AI Embeddings Generator
// または wp-cron で自動実行
do_action('gi_generate_all_embeddings');
```

---

## 📊 AJAXエンドポイント

### 1. セマンティック検索

**エンドポイント**: `gi_semantic_search`

**リクエスト**:
```javascript
{
    action: 'gi_semantic_search',
    query: '検索クエリ'
}
```

**レスポンス**:
```javascript
{
    success: true,
    data: {
        results: [
            {
                id: 123,
                title: '助成金名',
                excerpt: '説明',
                url: 'https://...',
                similarity: 0.857  // 類似度スコア (0-1)
            }
        ],
        count: 10,
        method: 'semantic'  // または 'keyword'
    }
}
```

### 2. コンテキストチャット

**エンドポイント**: `gi_contextual_chat`

**リクエスト**:
```javascript
{
    action: 'gi_contextual_chat',
    message: 'ユーザーのメッセージ'
}
```

**レスポンス**:
```javascript
{
    success: true,
    data: {
        response: 'AI生成の回答',
        related_grants: [
            {post_id: 123, similarity: 0.9, post: {...}}
        ],
        has_context: true  // 過去の会話を考慮したか
    }
}
```

---

## 🎨 UIへの影響

**重要**: UIは一切変更されていません。

- ✅ 既存の検索フォームがそのまま使える
- ✅ 既存のチャットUIがそのまま使える
- ✅ 既存のJavaScriptコードが全て動作
- ✅ レスポンス構造は後方互換性あり

ユーザーは何も変更する必要なく、自動的に恩恵を受けます。

---

## ⚡ パフォーマンス最適化

### キャッシュ戦略

1. **埋め込みキャッシュ**:
   - 助成金の内容が変わらない限り再利用
   - 24時間有効期限
   - 自動クリーンアップ

2. **レスポンスキャッシュ**:
   - 同じ検索クエリは即座に返答
   - WordPress Transient API利用

3. **バッチ処理**:
   - 複数の助成金を一度に処理
   - API呼び出し回数を削減

### メモリ使用量

- 埋め込みベクトル: ~5KB/助成金
- 1000件の助成金: ~5MB
- キャッシュテーブル: 定期的にクリーンアップ

---

## 🛠️ トラブルシューティング

### Q: セマンティック検索が動作しない

**確認事項**:
1. OpenAI APIキーが正しく設定されているか
2. `wp_gi_embeddings_cache`テーブルが存在するか
3. PHPエラーログを確認
4. フォールバック検索は動作するか

**解決方法**:
```php
// デバッグモードを有効化
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);

// エラーログ確認
tail -f wp-content/debug.log
```

### Q: コンテキストが記憶されない

**確認事項**:
1. セッションが開始されているか (`session_id()`)
2. `wp_gi_user_context`テーブルが存在するか
3. ブラウザのCookieが有効か

### Q: パフォーマンスが遅い

**最適化方法**:
1. OpenAI APIキーのレート制限を確認
2. キャッシュが正常に動作しているか確認
3. 埋め込みの事前生成を実行

```php
// すべての助成金の埋め込みを事前生成
$semantic_search = GI_Semantic_Search::getInstance();
$posts = get_posts(['post_type' => 'grant', 'numberposts' => -1]);
foreach ($posts as $post) {
    $semantic_search->get_post_embedding($post->ID);
}
```

---

## 📈 今後の拡張計画

### Phase 2 (予定)
- [ ] 推薦システム: 「あなたにおすすめの助成金」
- [ ] 自動要約: 長文を3段階で要約
- [ ] 比較分析: 複数助成金の比較テーブル自動生成
- [ ] 音声検索強化: Whisper API統合
- [ ] 多言語対応: 英語・中国語検索

### Phase 3 (予定)
- [ ] RAG (Retrieval-Augmented Generation)
- [ ] ファインチューニング: 助成金特化モデル
- [ ] エージェント機能: 申請書類の自動作成支援

---

## 📞 サポート

質問・バグ報告:
- GitHub Issues
- support@grantinsight.example.com

**Commit**: 0ae5957
**Date**: 2025-10-04
**Version**: AI Features v2.0
