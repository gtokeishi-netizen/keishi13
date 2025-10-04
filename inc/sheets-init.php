<?php
/**
 * Google Sheets Initializer
 * 
 * スプレッドシートの初期設定と自動セットアップ
 * - ヘッダー行の自動作成
 * - 初期データの投入
 * - バリデーションルールの設定
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

class SheetsInitializer {
    
    private static $instance = null;
    private $sheets_sync;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // GoogleSheetsSync インスタンスを取得
        add_action('init', array($this, 'init_after_sheets_sync'), 15);
        
        // AJAX ハンドラー
        add_action('wp_ajax_gi_initialize_sheet', array($this, 'ajax_initialize_sheet'));
        add_action('wp_ajax_gi_export_all_posts', array($this, 'ajax_export_all_posts'));
    }
    
    /**
     * Sheets同期後の初期化
     */
    public function init_after_sheets_sync() {
        $this->sheets_sync = GoogleSheetsSync::getInstance();
    }
    
    /**
     * スプレッドシートの初期化
     */
    public function initialize_sheet() {
        try {
            gi_log_error('Starting sheet initialization process');
            
            // Sheets Syncインスタンスの確認
            if (!$this->sheets_sync) {
                gi_log_error('SheetsSync instance not available, attempting to get instance');
                if (class_exists('GoogleSheetsSync')) {
                    $this->sheets_sync = GoogleSheetsSync::getInstance();
                    gi_log_error('SheetsSync instance obtained');
                } else {
                    throw new Exception('GoogleSheetsSync クラスが利用できません');
                }
            }
            
            // 1. ヘッダー行を設定
            gi_log_error('Step 1: Setting up headers');
            $this->setup_headers();
            gi_log_error('Headers setup completed');
            
            // 2. バリデーションルールを設定
            gi_log_error('Step 2: Setting up validation rules');
            $this->setup_validation_rules();
            gi_log_error('Validation rules setup completed');
            
            // 3. 既存の投稿データをエクスポート
            gi_log_error('Step 3: Exporting existing posts');
            $this->export_existing_posts();
            gi_log_error('Existing posts export completed');
            
            // 4. フォーマット設定
            gi_log_error('Step 4: Setting up formatting');
            $this->setup_formatting();
            gi_log_error('Formatting setup completed');
            
            gi_log_error('Sheet initialization completed successfully');
            return array('success' => true, 'message' => 'スプレッドシートの初期化が完了しました');
            
        } catch (Exception $e) {
            gi_log_error('Sheet initialization failed', array(
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ));
            
            return array('success' => false, 'message' => '初期化に失敗しました: ' . $e->getMessage());
        }
    }
    
    /**
     * ヘッダー行の設定
     */
    private function setup_headers() {
        $headers = array(
            'ID (自動入力)' => 'WordPress投稿ID（自動入力）',
            'タイトル' => '助成金名・タイトル',
            '内容・詳細' => '助成金の詳細説明（HTML可）',
            '抜粋・概要' => '一覧表示用の簡潔な概要',
            'ステータス (draft/publish/private)' => '投稿ステータス（publish/draft/private/deleted）',
            '作成日 (自動入力)' => '投稿作成日時（自動入力）',
            '更新日 (自動入力)' => '投稿更新日時（自動入力）',
            '助成金額 (例: 300万円)' => '表示用の助成金額',
            '助成金額数値 (例: 3000000)' => '数値での助成金額（円）',
            '申請期限 (例: 令和6年3月31日)' => '表示用の申請期限',
            '申請期限日付 (YYYY-MM-DD)' => 'YYYY-MM-DD形式の締切日',
            '実施組織名' => '助成金を実施する組織名',
            '組織タイプ (national/prefecture/city/public_org/private_org/other)' => '組織のタイプ',
            '対象者・対象事業' => '助成対象の詳細',
            '申請方法 (online/mail/visit/mixed)' => '申請方法',
            '問い合わせ先' => '連絡先情報',
            '公式URL' => '公式サイトURL',
            '地域制限 (nationwide/prefecture_only/municipality_only/region_group/specific_area)' => '地域制限のタイプ',
            '申請ステータス (open/upcoming/closed/suspended)' => '募集状況',
            '都道府県 (例: 東京都)' => '対象となる都道府県名（複数可、カンマ区切り）',
            '市町村 (例: 新宿区,渋谷区)' => '対象となる市区町村（カンマ区切り）',
            'カテゴリ (例: ビジネス支援,IT関連)' => 'grant_categoryタクソノミー名（複数可）',
            'タグ (例: スタートアップ,中小企業)' => 'grant_tagタクソノミー名（複数可）',
            '外部リンク' => '関連する外部リンクURL',
            '地域に関する備考' => '地域制限や対象地域に関する補足',
            '必要書類' => '申請に必要な書類（複数はカンマ区切り）',
            '採択率（%）' => '採択率（0-100）',
            '申請難易度 (easy/normal/hard/very_hard)' => '申請難易度の目安',
            '対象経費' => '助成対象となる経費の詳細',
            '補助率 (例: 2/3, 50%)' => '補助率・補助割合の詳細',
            'シート更新日 (自動入力)' => 'スプレッドシート最終更新日時（自動入力）'
        );

        // ヘッダー行を書き込み
        $header_values = array_keys($headers);
        $sheet_name = $this->sheets_sync->get_sheet_name();
        $result = $this->sheets_sync->write_sheet_data(
            $sheet_name . '!A1:AE1',
            array($header_values)
        );

        if (!$result) {
            throw new Exception('ヘッダー行の設定に失敗しました');
        }

        // 2行目に説明を追加
        $descriptions = array_values($headers);
        $this->sheets_sync->write_sheet_data(
            $sheet_name . '!A2:AE2',
            array($descriptions)
        );

        return true;
    }
    
    /**
     * バリデーションルールの設定（Google Sheets API v4では制限あり）
     */
    private function setup_validation_rules() {
        // Google Sheets APIでは高度なバリデーション設定が難しいため、サンプル行で想定値を提示
        $sample_row = array(
            '', // ID（自動入力）
            'サンプル助成金タイトル', // タイトル
            'この助成金の詳細な説明をここに記載します。', // 内容
            '短い概要説明', // 抜粋
            'draft', // ステータス
            '', // 作成日（自動入力）
            '', // 更新日（自動入力）
            '最大100万円', // 助成金額（表示用）
            '1000000', // 助成金額（数値）
            '2024年12月31日', // 申請期限（表示用）
            '2024-12-31', // 申請期限（日付）
            '◯◯財団', // 実施組織
            'public_org', // 組織タイプ
            '中小企業向け地域振興事業', // 対象者・対象事業
            'online', // 申請方法
            'info@example.org', // 問い合わせ先
            'https://example.org', // 公式URL
            'prefecture_only', // 地域制限
            'open', // 申請ステータス
            '東京都', // 都道府県
            '新宿区, 渋谷区', // 市町村
            '地域振興, 社会貢献', // カテゴリ
            'NPO, 助成金', // タグ
            'https://external.example.com', // 外部リンク
            '東京都内の中小企業が対象', // 地域備考
            '事業計画書, 決算書', // 必要書類
            '85', // 採択率
            '中級', // 申請難易度
            '人件費, 設備費', // 対象経費
            '1/2以内', // 補助率
            '' // シート更新日（自動入力）
        );

        $sheet_name = $this->sheets_sync->get_sheet_name();
        $this->sheets_sync->write_sheet_data(
            $sheet_name . '!A3:AE3',
            array($sample_row)
        );

        return true;
    }
    
    /**
     * 既存投稿のエクスポート
     */
    private function export_existing_posts() {
        $posts = get_posts(array(
            'post_type' => 'grant',
            'post_status' => array('publish', 'draft', 'private'),
            'numberposts' => -1,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        
        if (empty($posts)) {
            return true; // 投稿がない場合はそのまま成功
        }
        
        $rows = array();
        $start_row = 4; // 4行目から開始（ヘッダー、説明、サンプルの後）
        
        foreach ($posts as $post) {
            $row_data = $this->convert_post_to_row($post->ID);
            if ($row_data) {
                $rows[] = $row_data;
            }
        }
        
        if (!empty($rows)) {
            // 一括で書き込み
            $end_row = $start_row + count($rows) - 1;
            $sheet_name = $this->sheets_sync->get_sheet_name();
            $range = $sheet_name . "!A{$start_row}:AE{$end_row}";
            
            $result = $this->sheets_sync->write_sheet_data($range, $rows);
            
            if (!$result) {
                throw new Exception('既存投稿のエクスポートに失敗しました');
            }
        }
        
        return true;
    }
    
    /**
     * 投稿データを行データに変換
     */
    private function convert_post_to_row($post_id) {
        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'grant') {
            return false;
        }

        $prefectures = wp_get_post_terms($post_id, 'grant_prefecture', array('fields' => 'names'));
        $municipalities = wp_get_post_terms($post_id, 'grant_municipality', array('fields' => 'names'));
        $categories = wp_get_post_terms($post_id, 'grant_category', array('fields' => 'names'));
        $tags = wp_get_post_terms($post_id, 'grant_tag', array('fields' => 'names'));

        $max_amount = get_field('max_amount', $post_id);
        $max_amount_numeric = get_field('max_amount_numeric', $post_id);
        $deadline_display = get_field('deadline', $post_id);
        $deadline_date = get_field('deadline_date', $post_id);
        $organization = get_field('organization', $post_id);
        $organization_type = get_field('organization_type', $post_id) ?: 'national';
        $grant_target = get_field('grant_target', $post_id);
        $application_method = get_field('application_method', $post_id) ?: 'online';
        $contact_info = get_field('contact_info', $post_id);
        $official_url = get_field('official_url', $post_id);
        $regional_limitation = get_field('regional_limitation', $post_id) ?: 'nationwide';
        $application_status = get_field('application_status', $post_id) ?: 'open';
        $external_link = get_field('external_link', $post_id);
        $area_notes = get_field('area_notes', $post_id);
        $required_documents = get_field('required_documents_detailed', $post_id);
        $adoption_rate = get_field('adoption_rate', $post_id);
        $difficulty_level = get_field('difficulty_level', $post_id) ?: '中級';
        $eligible_expenses = get_field('eligible_expenses_detailed', $post_id);
        $subsidy_rate = get_field('subsidy_rate_detailed', $post_id);

        $prefecture_value = is_array($prefectures) && !is_wp_error($prefectures) ? implode(', ', $prefectures) : '';
        $municipality_value = is_array($municipalities) && !is_wp_error($municipalities) ? implode(', ', $municipalities) : '';
        $category_value = is_array($categories) && !is_wp_error($categories) ? implode(', ', $categories) : '';
        $tag_value = is_array($tags) && !is_wp_error($tags) ? implode(', ', $tags) : '';

        $row = array(
            $post_id,
            $post->post_title,
            $post->post_content,
            $post->post_excerpt,
            $post->post_status,
            $post->post_date,
            $post->post_modified,
            $max_amount ?: '',
            ($max_amount_numeric !== null && $max_amount_numeric !== '') ? $max_amount_numeric : '',
            $deadline_display ?: '',
            $deadline_date ?: '',
            $organization ?: '',
            $organization_type,
            $grant_target ?: '',
            $application_method,
            $contact_info ?: '',
            $official_url ?: '',
            $regional_limitation,
            $application_status,
            $prefecture_value,
            $municipality_value,
            $category_value,
            $tag_value,
            $external_link ?: '',
            $area_notes ?: '',
            $required_documents ?: '',
            ($adoption_rate !== null && $adoption_rate !== '') ? $adoption_rate : '',
            $difficulty_level,
            $eligible_expenses ?: '',
            $subsidy_rate ?: '',
            current_time('mysql')
        );

        return $row;
    }
    
    /**
     * フォーマット設定
     */
    private function setup_formatting() {
        // Google Sheets API v4では詳細なフォーマット設定は制限的
        // 基本的なフォーマットのみ設定可能
        
        // 今回は省略（将来的にはGoogle Apps Scriptで実装を推奨）
        return true;
    }
    
    /**
     * 統計情報の取得
     */
    public function get_sync_stats() {
        // WordPress側の統計
        $wp_posts_count = wp_count_posts('grant');
        
        // スプレッドシート側の統計を取得
        $sheet_data = $this->sheets_sync->read_sheet_data();
        $sheet_rows_count = is_array($sheet_data) ? count($sheet_data) - 1 : 0; // ヘッダー行を除外
        
        return array(
            'wordpress' => array(
                'publish' => $wp_posts_count->publish ?? 0,
                'draft' => $wp_posts_count->draft ?? 0,
                'private' => $wp_posts_count->private ?? 0,
                'total' => ($wp_posts_count->publish ?? 0) + ($wp_posts_count->draft ?? 0) + ($wp_posts_count->private ?? 0)
            ),
            'spreadsheet' => array(
                'total_rows' => $sheet_rows_count,
                'last_updated' => get_option('gi_sheets_last_sync', '未同期')
            ),
            'sync_status' => array(
                'auto_sync_enabled' => get_option('gi_sheets_config', array())['auto_sync_enabled'] ?? true,
                'last_sync' => get_option('gi_sheets_last_full_sync', '未実行'),
                'errors_count' => count(get_option('gi_sheets_sync_log', array()))
            )
        );
    }
    
    /**
     * AJAX: スプレッドシート初期化
     */
    public function ajax_initialize_sheet() {
        // タイムアウトとメモリ制限の拡張
        set_time_limit(300); // 5分
        ini_set('memory_limit', '256M');
        
        try {
            gi_log_error('AJAX initialize_sheet started', array(
                'user_id' => get_current_user_id(),
                'post_data' => $_POST
            ));
            
            // Nonce検証
            check_ajax_referer('gi_sheets_nonce', 'nonce');
            gi_log_error('Nonce verification passed for initialization');
            
            // 権限チェック
            if (!current_user_can('edit_posts')) {
                gi_log_error('Permission denied for initialization', array('user_id' => get_current_user_id()));
                wp_send_json_error('Permission denied');
                return;
            }
            
            gi_log_error('Starting sheet initialization');
            
            // 初期化処理を実行
            $result = $this->initialize_sheet();
            
            gi_log_error('Sheet initialization result', $result);
            
            if ($result && isset($result['success']) && $result['success']) {
                wp_send_json_success($result['message']);
            } else {
                $error_message = isset($result['message']) ? $result['message'] : '初期化に失敗しました';
                wp_send_json_error($error_message);
            }
            
        } catch (Exception $e) {
            gi_log_error('AJAX initialize_sheet exception caught', array(
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ));
            wp_send_json_error('初期化中にエラーが発生しました: ' . $e->getMessage());
            
        } catch (Error $e) {
            gi_log_error('AJAX initialize_sheet fatal error caught', array(
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ));
            wp_send_json_error('初期化中に致命的エラーが発生しました: ' . $e->getMessage());
            
        } catch (Throwable $e) {
            gi_log_error('AJAX initialize_sheet throwable caught', array(
                'error' => $e->getMessage(),
                'file' => method_exists($e, 'getFile') ? $e->getFile() : 'unknown',
                'line' => method_exists($e, 'getLine') ? $e->getLine() : 'unknown',
                'trace' => method_exists($e, 'getTraceAsString') ? $e->getTraceAsString() : 'no trace'
            ));
            wp_send_json_error('初期化中に予期しないエラーが発生しました: ' . $e->getMessage());
        }
    }
    
    /**
     * AJAX: 全投稿エクスポート
     */
    public function ajax_export_all_posts() {
        check_ajax_referer('gi_sheets_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Permission denied');
        }
        
        try {
            $this->export_existing_posts();
            wp_send_json_success('全投稿をスプレッドシートにエクスポートしました');
            
        } catch (Exception $e) {
            wp_send_json_error('エクスポートに失敗しました: ' . $e->getMessage());
        }
    }
    
    /**
     * スプレッドシートをクリア
     */
    public function clear_sheet() {
        try {
            gi_log_error('Starting sheet clear process');
            
            // Sheets Syncインスタンスの確認
            if (!$this->sheets_sync) {
                if (class_exists('GoogleSheetsSync')) {
                    $this->sheets_sync = GoogleSheetsSync::getInstance();
                } else {
                    throw new Exception('GoogleSheetsSync クラスが利用できません');
                }
            }
            
            // スプレッドシートのデータをクリア（ヘッダー行は残す）
            $range = 'A2:AE1000'; // 31列、1000行までクリア
            $clear_data = $this->sheets_sync->clear_sheet_range($range);
            
            if ($clear_data) {
                gi_log_error('Sheet clear completed successfully');
                return array(
                    'success' => true,
                    'message' => 'スプレッドシートのデータをクリアしました'
                );
            } else {
                throw new Exception('スプレッドシートのクリアに失敗しました');
            }
            
        } catch (Exception $e) {
            gi_log_error('Sheet clear failed', array('error' => $e->getMessage()));
            return array(
                'success' => false,
                'message' => 'クリアに失敗しました: ' . $e->getMessage()
            );
        }
    }
}

// AJAX ハンドラーを追加
add_action('wp_ajax_gi_clear_sheet', function() {
    check_ajax_referer('gi_sheets_nonce', 'nonce');
    
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Permission denied');
    }
    
    $initializer = SheetsInitializer::getInstance();
    $result = $initializer->clear_sheet();
    
    if ($result['success']) {
        wp_send_json_success($result['message']);
    } else {
        wp_send_json_error($result['message']);
    }
});

// インスタンスを初期化
function gi_init_sheets_initializer() {
    return SheetsInitializer::getInstance();
}
add_action('init', 'gi_init_sheets_initializer');