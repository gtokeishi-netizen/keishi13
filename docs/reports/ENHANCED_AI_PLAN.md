# AI機能強化計画 (UIは変更なし、機能のみ大幅強化)

## 🎯 目標
既存のUIを完全に維持しながら、バックエンドのAI機能を大幅に強化し、ユーザー体験を向上させる。

## 🚀 追加する機能

### 1. **セマンティック検索の高精度化**
- **ベクトル埋め込み**: OpenAI Embeddingsを使用して助成金データのベクトル化
- **コサイン類似度**: 検索クエリとのセマンティックマッチング
- **ハイブリッド検索**: キーワード検索 + セマンティック検索の組み合わせ
- **リランキング**: 検索結果を関連性でスコアリング・再順位付け

### 2. **コンテキスト記憶機能**
- **会話履歴**: ユーザーの過去10件の検索・チャット履歴を記憶
- **パーソナライズ**: 履歴に基づいた検索結果の最適化
- **継続対話**: 前の質問を覚えていて、文脈を理解した回答

### 3. **スマート補完・提案**
- **検索クエリ補完**: ユーザーの入力途中でAIが最適な検索クエリを提案
- **関連助成金推薦**: 現在閲覧中の助成金に類似したものを自動推薦
- **カテゴリ予測**: ユーザーの検索パターンから興味のあるカテゴリを予測

### 4. **高度な質問応答**
- **GPT-4 Turbo統合**: より精度の高い回答生成
- **引用付き回答**: 回答の根拠となる助成金データを明示
- **多段階推論**: 複雑な質問を分解して段階的に回答

### 5. **自動要約・分析**
- **助成金の自動要約**: 長い説明文を3段階（1行/3行/5行）で要約
- **比較分析**: 複数の助成金を自動比較してテーブル生成
- **適合度スコアリング**: ユーザープロフィールと助成金の適合度を数値化

### 6. **音声機能の強化**
- **Whisper API**: 高精度な音声認識（ブラウザAPI + Whisper併用）
- **TTS統合**: 回答の音声読み上げ機能
- **音声コマンド**: 「検索して」「詳細を教えて」などの自然言語コマンド

### 7. **キャッシュ・最適化**
- **ベクトルキャッシュ**: 計算済みembeddingsをWordPress transientに保存
- **レスポンスキャッシュ**: 同じ質問の回答をキャッシュ（24時間）
- **バッチ処理**: 複数の助成金を一度にベクトル化

### 8. **エラーハンドリング・フォールバック**
- **段階的劣化**: OpenAI APIが使えない時も基本機能は動作
- **ローカル検索**: AI検索失敗時は従来のWP_Query検索
- **エラーログ**: 詳細なエラートラッキングとユーザーフレンドリーなメッセージ

## 📊 実装詳細

### データベーススキーマ拡張
```sql
-- ベクトル埋め込みキャッシュテーブル
CREATE TABLE IF NOT EXISTS `wp_gi_embeddings_cache` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL,
  `content_hash` varchar(64) NOT NULL,
  `embedding_vector` longtext NOT NULL,
  `model_version` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `post_content_hash` (`post_id`, `content_hash`),
  KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ユーザーコンテキスト履歴テーブル
CREATE TABLE IF NOT EXISTS `wp_gi_user_context` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NULL,
  `session_id` varchar(64) NOT NULL,
  `interaction_type` varchar(20) NOT NULL,
  `query` text NOT NULL,
  `response` longtext NULL,
  `metadata` longtext NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_session` (`user_id`, `session_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 新しいクラス構造
```php
// 1. GI_Semantic_Search: セマンティック検索エンジン
// 2. GI_Context_Manager: ユーザーコンテキスト管理
// 3. GI_Smart_Recommender: 推薦システム
// 4. GI_Advanced_NLP: 高度な自然言語処理
// 5. GI_Cache_Manager: キャッシュ管理システム
```

### API統合
- **OpenAI Embeddings API**: text-embedding-3-small (高速・低コスト)
- **OpenAI Chat API**: gpt-4-turbo-preview (高精度回答)
- **Whisper API**: whisper-1 (音声認識)

### パフォーマンス目標
- 検索レスポンス: < 500ms (キャッシュヒット時)
- AI検索: < 2秒 (初回)
- チャット応答: < 3秒
- 音声認識: < 5秒

## 🔧 実装ステップ

1. **データベーステーブル作成** (inc/ai-functions.php)
2. **セマンティック検索クラス実装** (inc/ai-functions.php)
3. **コンテキスト管理クラス実装** (inc/ai-functions.php)
4. **AJAX ハンドラー強化** (inc/ajax-functions.php)
5. **キャッシュシステム実装** (inc/ai-functions.php)
6. **既存UIとの統合** (JavaScriptはそのまま、レスポンス構造を拡張)

## ✅ 完成後の機能一覧

- ✅ キーワード検索（既存）
- 🆕 セマンティック検索（新規）
- ✅ AIチャット（既存・強化）
- 🆕 コンテキスト記憶（新規）
- 🆕 スマート推薦（新規）
- ✅ 音声検索（既存・強化）
- 🆕 自動要約（新規）
- 🆕 比較分析（新規）
- 🆕 適合度スコアリング（新規）

## 🎨 UIへの影響
**なし** - すべて既存のUI要素を利用し、バックエンドの処理を強化するのみ。
ユーザーは既存のインターフェースで、より賢く・より速く・より正確な結果を得られる。
