# 📄 固定ページテンプレート

このディレクトリには、WordPressの固定ページ用カスタムテンプレートが格納されています。

## 📁 テンプレート一覧

| ファイル名 | ページ名 | 説明 | スタイル |
|-----------|---------|------|---------|
| `page-about.php` | About（私たちについて） | 会社・サービス紹介ページ | 黒/白/黄色のスタイリッシュデザイン |
| `page-faq.php` | FAQ（よくある質問） | 18個のQ&A、6カテゴリ | アコーディオン式、検索機能付き |
| `page-contact.php` | Contact（お問い合わせ） | 問い合わせフォーム | シンプルなフォームデザイン |
| `page-privacy.php` | Privacy Policy（プライバシーポリシー） | プライバシーポリシー | 法的文書スタイル |
| `page-terms.php` | Terms（利用規約） | サービス利用規約 | 法的文書スタイル |

## 🎨 デザインシステム

### About ページ
- **配色**: 黒 (#000000) / 白 (#FFFFFF) / 黄色 (#FFD700)
- **特徴**: 
  - スタイリッシュなヒーローセクション
  - 統計情報の表示
  - チーム紹介セクション
  - CTA（Call to Action）ボタン

### FAQ ページ
- **配色**: モノクロベース
- **特徴**:
  - 6カテゴリ（基本情報、申請方法、必要書類、採択基準、サポート、その他）
  - 18個のQ&A
  - 検索機能
  - アコーディオンUI
  - カテゴリフィルター

### Contact ページ
- **配色**: クリーンなモノクロ
- **特徴**:
  - Contact Form 7統合
  - バリデーション
  - スパム対策

## 🛠️ 使用方法

### WordPressでの設定

1. **固定ページ作成**
   ```
   WordPress管理画面 > 固定ページ > 新規追加
   ```

2. **テンプレート選択**
   ```
   ページ属性 > テンプレート > 適切なテンプレートを選択
   ```

3. **スラッグ設定**
   - About: `about`
   - FAQ: `faq`
   - Contact: `contact`
   - Privacy: `privacy`
   - Terms: `terms`

### カスタマイズ

各テンプレートファイルを編集してカスタマイズできます：

```php
// page-about.php の例
<section class="hero-section">
    <h1>ここにタイトル</h1>
    <p>ここに説明文</p>
</section>
```

## 📋 必要なプラグイン

### Contact Form 7
Contact ページで使用しています。

**インストール方法**:
```
プラグイン > 新規追加 > "Contact Form 7" を検索 > インストール > 有効化
```

**フォーム設定**:
1. Contact Form 7 > フォーム一覧
2. デフォルトフォームを編集
3. ショートコードをコピー
4. `page-contact.php` に貼り付け

## 🎯 ページ構成のベストプラクティス

### 1. ヘッダー構造
```html
<header class="page-header">
    <h1 class="page-title">ページタイトル</h1>
    <p class="page-subtitle">サブタイトル</p>
</header>
```

### 2. コンテンツセクション
```html
<section class="content-section">
    <div class="container">
        <!-- コンテンツ -->
    </div>
</section>
```

### 3. CTA（Call to Action）
```html
<section class="cta-section">
    <a href="#" class="cta-button">アクション</a>
</section>
```

## 🔧 トラブルシューティング

### テンプレートが表示されない
1. ファイル名が正しいか確認（`page-*.php`）
2. ファイルのパーミッションを確認
3. WordPressキャッシュをクリア

### スタイルが適用されない
1. CSS/JSファイルのパスを確認
2. `wp_enqueue_style()` が正しく呼ばれているか確認
3. ブラウザキャッシュをクリア

### フォームが動作しない
1. Contact Form 7がインストール・有効化されているか確認
2. ショートコードが正しいか確認
3. メール送信設定を確認

## 📝 更新履歴

- **2025-10-04**: FAQ ページ追加（18 Q&As、6カテゴリ）
- **2025-10-04**: About ページにスタイリッシュデザイン適用
- **2025-10-04**: 全ページをモノクロデザインに統一

## 🔗 関連リンク

- [WordPress テンプレート階層](https://developer.wordpress.org/themes/basics/template-hierarchy/)
- [Contact Form 7 公式ドキュメント](https://contactform7.com/docs/)
- [ACF フィールド](https://www.advancedcustomfields.com/resources/)
