# フィールドマッピング検証レポート

## 🔍 sheets-webhook.php vs acf-fields.php 整合性チェック

### ✅ 完全一致フィールド（H-S列）

| 列 | Index | sheets-webhook.php | acf-fields.php | ステータス |
|----|-------|-------------------|----------------|----------|
| H | 7 | `max_amount` | `max_amount` (line 121) | ✅ 一致 |
| I | 8 | `max_amount_numeric` | `max_amount_numeric` (line 135) | ✅ 一致 |
| J | 9 | `deadline` | `deadline` (line 169) | ✅ 一致 |
| K | 10 | `deadline_date` | `deadline_date` (line 183) | ✅ 一致 |
| L | 11 | `organization` | `organization` (line 85) | ✅ 一致 |
| M | 12 | `organization_type` | `organization_type` (line 99) | ✅ 一致 |
| N | 13 | `grant_target` | `grant_target` (line 234) | ✅ 一致 |
| O | 14 | `application_method` | `application_method` (line 330) | ✅ 一致 |
| P | 15 | `contact_info` | `contact_info` (line 349) | ✅ 一致 |
| Q | 16 | `official_url` | `official_url` (line 362) | ✅ 一致 |
| R | 17 | `regional_limitation` | `regional_limitation` (line 297) | ✅ 一致 |
| S | 18 | `application_status` | `application_status` (line 198) | ✅ 一致 |

### ✅ 完全一致フィールド（X-AD列）

| 列 | Index | sheets-webhook.php | acf-fields.php | ステータス |
|----|-------|-------------------|----------------|----------|
| X | 23 | `external_link` | `external_link` (line 374) | ✅ 一致 |
| Y | 24 | `area_notes` | `area_notes` (line 387) | ✅ 一致 |
| Z | 25 | `required_documents_detailed` | `required_documents_detailed` (line 401) | ✅ 一致 |
| AA | 26 | `adoption_rate` | `adoption_rate` (line 416) | ✅ 一致 |
| AB | 27 | `difficulty_level` | `difficulty_level` (line 433) | ✅ 一致 |
| AC | 28 | `eligible_expenses_detailed` | `eligible_expenses_detailed` (line 452) | ✅ 一致 |
| AD | 29 | `subsidy_rate_detailed` | `subsidy_rate_detailed` (line 467) | ✅ 一致 |

## 📋 追加のACFフィールド（スプレッドシートにマップされていない）

以下のフィールドはacf-fields.phpに定義されていますが、スプレッドシートの31列には含まれていません：

| フィールド名 | 行番号 | 用途 | 備考 |
|------------|--------|------|------|
| `min_amount` | 150 | 最小助成額 | スプレッドシート未使用 |
| `application_period` | 217 | 申請期間 | スプレッドシート未使用 |
| `eligible_expenses` | 246 | 対象経費（簡易版） | `eligible_expenses_detailed`と重複 |
| `grant_difficulty` | 258 | 申請難易度（旧） | `difficulty_level`と重複 |
| `required_documents` | 280 | 必要書類（簡易版） | `required_documents_detailed`と重複 |
| `regional_note` | 317 | 地域備考（旧） | `area_notes`と重複（移行メッセージ削除済み） |
| `is_featured` | 481 | 注目表示フラグ | 管理用フィールド |
| `priority_order` | 495 | 優先順位 | 管理用フィールド |
| `views_count` | 511 | 閲覧数 | 管理用フィールド |
| `last_updated` | 525 | 最終更新日 | 管理用フィールド |
| `admin_notes` | 539 | 管理者メモ | 管理用フィールド |

## ⚠️ 重複フィールドの整理推奨

### 簡易版と詳細版の重複

1. **対象経費**
   - `eligible_expenses` (line 246) - 簡易版
   - `eligible_expenses_detailed` (line 452) - 詳細版（AC列）✅ スプレッドシート連携
   
2. **申請難易度**
   - `grant_difficulty` (line 258) - 旧フィールド
   - `difficulty_level` (line 433) - 新フィールド（AB列）✅ スプレッドシート連携

3. **必要書類**
   - `required_documents` (line 280) - 簡易版
   - `required_documents_detailed` (line 401) - 詳細版（Z列）✅ スプレッドシート連携

4. **地域備考**
   - `regional_note` (line 317) - 旧フィールド（移行メッセージ削除済み）
   - `area_notes` (line 387) - 新フィールド（Y列）✅ スプレッドシート連携

## 🎯 推奨アクション

### オプション1: 重複フィールドの削除（推奨）
スプレッドシートを信頼できる情報源（Source of Truth）として、以下の旧フィールドを削除：
- `eligible_expenses` → `eligible_expenses_detailed`を使用
- `grant_difficulty` → `difficulty_level`を使用
- `required_documents` → `required_documents_detailed`を使用
- `regional_note` → `area_notes`を使用（すでに移行メッセージ削除済み）

### オプション2: フィールドの統合
重複フィールドをマージして、データを失わずに整理する。

### オプション3: 現状維持
後方互換性のために両方を保持するが、新規作成時は詳細版を使用するよう推奨。

## ✅ 結論

**sheets-webhook.phpとacf-fields.phpの31列マッピング（H-S、X-AD列）は完全に一致しています。**

重複フィールドは存在しますが、これらは：
1. 旧バージョンとの互換性のため
2. 管理用の追加機能のため

カテゴリ同期の問題とは無関係で、フィールド名の不一致は**ありません**。
