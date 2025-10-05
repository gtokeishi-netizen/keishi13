# 🎯 助成金検索システム - Grant Insight

WordPressベースの助成金検索・管理システム

## 📁 プロジェクト構造

```
/home/user/webapp/
├── docs/                      # 📚 ドキュメント
│   ├── README.md             # ドキュメント一覧とガイド
│   └── reports/              # レポート・分析資料（12ファイル）
│       ├── PROJECT_STATUS_REPORT.md
│       ├── AI_FEATURES_COMPLETE.md
│       ├── AI_FEATURES_PROGRESS.md
│       └── ... (他9ファイル)
│
├── pages/                     # 📄 固定ページテンプレート
│   ├── README.md             # ページテンプレートガイド
│   ├── page-about.php        # Aboutページ
│   ├── page-faq.php          # FAQページ（18 Q&As）
│   ├── page-contact.php      # お問い合わせ
│   ├── page-privacy.php      # プライバシーポリシー
│   └── page-terms.php        # 利用規約
│
├── template-parts/           # テンプレートパーツ
│   ├── grant-card-unified.php
│   ├── category-hierarchy.php
│   └── front-page/
│
├── inc/                      # PHP関数・機能
│   ├── ai-functions.php
│   ├── acf-fields.php
│   ├── google-sheets-integration.php
│   └── ...
│
├── assets/                   # アセット（CSS/JS）
│   ├── css/
│   └── js/
│
├── archive-grant.php         # 助成金アーカイブページ
├── single-grant.php          # 助成金詳細ページ
├── front-page.php            # フロントページ
└── functions.php             # メイン関数ファイル
```

## 🚀 クイックスタート

### ドキュメントを読む
```bash
# プロジェクト概要
cat docs/reports/QUICK_SUMMARY.md

# 最新状況
cat docs/reports/PROJECT_STATUS_REPORT.md

# AI機能ガイド
cat docs/reports/AI_FEATURES_COMPLETE.md
```

### ページテンプレートを確認
```bash
# ページテンプレート一覧
ls -la pages/

# ページ構成ガイド
cat pages/README.md
```

## 📚 主要ドキュメント

### 必読ドキュメント（優先順位順）
1. **[docs/README.md](docs/README.md)** - ドキュメント全体のインデックス
2. **[docs/reports/QUICK_SUMMARY.md](docs/reports/QUICK_SUMMARY.md)** - プロジェクト概要
3. **[docs/reports/PROJECT_STATUS_REPORT.md](docs/reports/PROJECT_STATUS_REPORT.md)** - 現在の状況
4. **[pages/README.md](pages/README.md)** - 固定ページガイド

### 開発者向け
- **[docs/reports/FIELD_MAPPING_VERIFICATION.md](docs/reports/FIELD_MAPPING_VERIFICATION.md)** - データ構造
- **[docs/reports/SHEETS_SYNC_ANALYSIS.md](docs/reports/SHEETS_SYNC_ANALYSIS.md)** - Google Sheets同期
- **[docs/reports/AI_FEATURES_USAGE.md](docs/reports/AI_FEATURES_USAGE.md)** - AI機能実装

### 管理者向け
- **[docs/reports/PAGES_SETUP_GUIDE.md](docs/reports/PAGES_SETUP_GUIDE.md)** - ページ管理
- **[docs/reports/ABOUT_PAGE_SETUP.md](docs/reports/ABOUT_PAGE_SETUP.md)** - Aboutページ設定

## ✨ 主要機能

### 🔍 検索・フィルタリング
- ✅ 階層型地域フィルター（地方→都道府県→市町村）
- ✅ 階層型カテゴリフィルター（親→子）
- ✅ サイドバー詳細フィルター
- ✅ キーワード検索

### 🤖 AI機能
- ✅ AIに質問（助成金についてチャット）
- ✅ AI比較（複数助成金の比較）
- ✅ チェックリスト（申請準備管理）

### 🎨 デザイン
- ✅ Minna Bank風デザインシステム
- ✅ トレーディングカード風カードデザイン
- ✅ モノクロ・ミニマルスタイル
- ✅ レスポンシブ対応

### 🔄 データ同期
- ✅ Google Sheets ↔ WordPress自動同期
- ✅ カスタムフィールド（ACF）統合
- ✅ 画像自動アップロード

## 🛠️ 技術スタック

- **CMS**: WordPress 6.x
- **カスタムフィールド**: Advanced Custom Fields (ACF)
- **デザイン**: カスタムCSS、Font Awesome 6.5.1
- **フォント**: Inter + Noto Sans JP
- **API**: Google Sheets API、OpenAI API

## 📊 統計

- **投稿タイプ**: `grant`（助成金）
- **タクソノミー**: 
  - `grant_category`（カテゴリ）
  - `grant_prefecture`（都道府県）
  - `grant_municipality`（市町村）
- **カスタムフィールド**: 30+ ACFフィールド
- **固定ページ**: 5ページ（About, FAQ, Contact, Privacy, Terms）
- **ドキュメント**: 12レポート

## 🔗 リンク

- **リポジトリ**: https://github.com/gtokeishi-netizen/keishi13
- **最新PR**: https://github.com/gtokeishi-netizen/keishi13/pull/1
- **Issues**: https://github.com/gtokeishi-netizen/keishi13/issues

## 📝 更新履歴

### 2025-10-05
- ✅ 階層型カテゴリフィルター追加
- ✅ トレーディングカード風デザイン適用
- ✅ サイドバーフィルター統一
- ✅ ドキュメント整理（docs/, pages/フォルダ作成）

### 2025-10-04
- ✅ AI機能統合（3機能）
- ✅ FAQページ追加（18 Q&As）
- ✅ Minna Bank風デザイン適用
- ✅ カーセンサー風階層フィルター

## 👥 開発チーム

- **AI Developer**: GenSpark AI
- **Project Owner**: gtokeishi-netizen

## 📄 ライセンス

プライベートプロジェクト

---

**最終更新**: 2025-10-05
**バージョン**: 1.0.0
**ブランチ**: genspark_ai_developer → main
