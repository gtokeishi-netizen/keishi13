# 📊 Google Sheets ↔ WordPress Synchronization Analysis Report

**Date:** 2025-10-04  
**Analysis Scope:** 13 PHP files in `/home/user/webapp/inc/` directory  
**Focus:** Category synchronization diagnosis and field consistency verification

---

## 🎯 Executive Summary

### ✅ **Good News: Category Sync Code is Working Correctly**

After comprehensive analysis of all synchronization files, the **category synchronization from Google Sheets → WordPress is functioning properly**. The code correctly maps Column V (index 21) to the `grant_category` taxonomy.

### 🔧 **Issues Found & Fixed**

1. **❌ Unused ACF Fields** (treating spreadsheet as source of truth)
2. **❌ Field Name Inconsistencies** across sync modules
3. **✅ All Issues Resolved**

---

## 📁 Files Analyzed (13 Total)

### **Core Synchronization Files:**
1. ✅ `sheets-sync.php` - Bidirectional sync logic (Sheets ↔ WP)
2. ✅ `sheets-webhook.php` - Webhook handlers for real-time updates
3. ✅ `sheets-init.php` - Spreadsheet initialization and export
4. ✅ `sheets-admin.php` - Admin UI and field mapping table
5. ✅ `acf-fields.php` - ACF field definitions (34,021 bytes)
6. ✅ `safe-sync-manager.php` - Rate limiting and safety
7. ✅ `disable-auto-sync.php` - Auto-sync disabler
8. ✅ `theme-foundation.php` - Post types and taxonomies

### **Supporting Files:**
9. ✅ `admin-functions.php` - Admin UI and metaboxes
10. ✅ `ajax-functions.php` - AJAX handlers
11. ✅ `ai-functions.php` - AI content generation
12. ✅ `data-processing.php` - Helper functions
13. ✅ `card-display.php` - Grant card rendering

---

## 🔍 Detailed Analysis: Category Synchronization

### **Column V (Index 21) → grant_category Taxonomy**

#### ✅ **Code Location: sheets-sync.php (Lines 780-791)**

```php
// カテゴリを設定（V列のデータから） ★完全連携
if (isset($row[21]) && !empty($row[21])) {
    $categories = array_map('trim', explode(',', $row[21]));
    $category_result = wp_set_post_terms($post_id, $categories, 'grant_category');
    
    gi_log_error('Category sync result', array(
        'post_id' => $post_id,
        'raw_category_data' => $row[21],
        'categories_array' => $categories,
        'set_terms_result' => $category_result
    ));
}
```

#### ✅ **Verification Points:**

1. **Column Mapping:**
   - ✅ Column V = Index 21 (correct)
   - ✅ Taxonomy: `grant_category` (correct)
   - ✅ Supports comma-separated multiple categories
   - ✅ Automatic term creation if term doesn't exist

2. **Logging:**
   - ✅ Detailed logging enabled (`gi_log_error`)
   - ✅ Logs include: post_id, raw data, parsed array, result

3. **Error Handling:**
   - ✅ Checks for isset and !empty before processing
   - ✅ Returns WP_Error on failure (logged)

### **Why Categories Might Not Be Reflecting (Debugging Guide):**

If categories are not appearing after sync, check these points:

1. **Check Logs:**
   ```bash
   # Check WordPress debug log
   tail -f /path/to/wp-content/debug.log | grep "Category sync"
   ```

2. **Verify Spreadsheet Data:**
   - Column V should contain category names (e.g., "ビジネス支援,IT関連")
   - Use comma separation for multiple categories
   - Category names should match existing terms or will be auto-created

3. **Verify Taxonomy Registration:**
   ```php
   // theme-foundation.php (Lines 281-310)
   register_taxonomy('grant_category', 'grant', [...]); // ✅ Confirmed
   ```

4. **Check Sync Direction:**
   - **Sheets → WP:** Uses `sheets-sync.php::sync_sheets_to_wp()`
   - **WP → Sheets:** Uses `sheets-sync.php::convert_post_to_sheet_row()`
   - **Both:** Uses `full_bidirectional_sync()`

---

## 🛠️ Issues Fixed

### **1. Removed Unused ACF Fields (acf-fields.php)**

Treating the spreadsheet as the **source of truth**, removed the following fields:

#### ❌ **field_subsidy_rate** (Lines 163-174)
```php
// BEFORE:
array(
    'key' => 'field_subsidy_rate',
    'label' => '補助率',
    'name' => 'subsidy_rate',
    'type' => 'text',
    // ...
)

// AFTER: Removed (use subsidy_rate_detailed instead - AD column)
```

**Reason:** Duplicate field. Spreadsheet uses `subsidy_rate_detailed` (AD column).

---

#### ❌ **field_amount_note** (Lines 177-187)
```php
// BEFORE:
array(
    'key' => 'field_amount_note',
    'label' => '金額に関する備考',
    'name' => 'amount_note',
    'type' => 'textarea',
    // ...
)

// AFTER: Removed (not in spreadsheet configuration)
```

**Reason:** Not present in 31-column spreadsheet configuration.

---

#### ❌ **field_deadline_note** (Lines 252-262)
```php
// BEFORE:
array(
    'key' => 'field_deadline_note',
    'label' => '締切に関する備考',
    'name' => 'deadline_note',
    'type' => 'textarea',
    // ...
)

// AFTER: Removed (not in spreadsheet configuration)
```

**Reason:** Not present in 31-column spreadsheet configuration.

---

#### ❌ **field_grant_success_rate** (Lines 309-322)
```php
// BEFORE:
array(
    'key' => 'field_grant_success_rate',
    'label' => '採択率（%）',
    'name' => 'grant_success_rate',
    'type' => 'number',
    // ...
)

// AFTER: Removed (use adoption_rate instead - AA column)
```

**Reason:** Duplicate field. Spreadsheet uses `adoption_rate` (AA column).

---

### **2. Fixed Field Name Inconsistencies (sheets-sync.php)**

Found and fixed 5 field name mismatches between sync modules:

#### ✅ **Y Column: region_notes → area_notes**
```php
// BEFORE (sheets-sync.php line 1490):
'field_key' => 'region_notes',

// AFTER:
'field_key' => 'area_notes',
```

**Reason:** `acf-fields.php` and `sheets-webhook.php` both use `area_notes`.

---

#### ✅ **Z Column: required_documents → required_documents_detailed**
```php
// BEFORE (sheets-sync.php line 1621):
'required_documents' => 25,

// AFTER:
'required_documents_detailed' => 25,
```

**Reason:** ACF field name is `required_documents_detailed`.

---

#### ✅ **AB Column: application_difficulty → difficulty_level**
```php
// BEFORE (sheets-sync.php line 1509):
'field_key' => 'application_difficulty',

// AFTER:
'field_key' => 'difficulty_level',
```

**Reason:** ACF field name is `difficulty_level`.

---

#### ✅ **AC Column: target_expenses → eligible_expenses_detailed**
```php
// BEFORE (sheets-sync.php line 1516):
'field_key' => 'target_expenses',

// AFTER:
'field_key' => 'eligible_expenses_detailed',
```

**Reason:** ACF field name is `eligible_expenses_detailed`.

---

#### ✅ **AD Column: subsidy_rate → subsidy_rate_detailed**
```php
// BEFORE (sheets-sync.php line 1522):
'field_key' => 'subsidy_rate',

// AFTER:
'field_key' => 'subsidy_rate_detailed',
```

**Reason:** ACF field name is `subsidy_rate_detailed`.

---

### **3. TinyMCE Statusbar Elements**

#### ✅ **Status: Not Present**

Searched for TinyMCE `statusbar` configuration in all WYSIWYG fields:
```bash
grep -n "statusbar" inc/acf-fields.php
# Result: No matches found ✅
```

**Conclusion:** No statusbar configuration present. Nothing to remove.

---

## 📋 Complete 31-Column Mapping (A-AE)

### **Column Structure: Verified Across All Files**

| Col | Index | Field Name | Field Key | Type | Sync Status |
|-----|-------|------------|-----------|------|-------------|
| A | 0 | ID | post_id | readonly | ✅ Auto |
| B | 1 | タイトル | post_title | text | ✅ Synced |
| C | 2 | 内容・詳細 | post_content | wysiwyg | ✅ Synced |
| D | 3 | 抜粋・概要 | post_excerpt | textarea | ✅ Synced |
| E | 4 | ステータス | post_status | select | ✅ Synced |
| F | 5 | 作成日 | post_date | readonly | ✅ Auto |
| G | 6 | 更新日 | post_modified | readonly | ✅ Auto |
| H | 7 | 助成金額 | max_amount | text | ✅ Synced |
| I | 8 | 助成金額数値 | max_amount_numeric | number | ✅ Synced |
| J | 9 | 申請期限 | deadline | text | ✅ Synced |
| K | 10 | 申請期限日付 | deadline_date | date | ✅ Synced |
| L | 11 | 実施組織 | organization | text | ✅ Synced |
| M | 12 | 組織タイプ | organization_type | select | ✅ Synced |
| N | 13 | 対象者・対象事業 | grant_target | wysiwyg | ✅ Synced |
| O | 14 | 申請方法 | application_method | select | ✅ Synced |
| P | 15 | 問い合わせ先 | contact_info | textarea | ✅ Synced |
| Q | 16 | 公式URL | official_url | url | ✅ Synced |
| R | 17 | 地域制限 | regional_limitation | select | ✅ Synced |
| S | 18 | 申請ステータス | application_status | select | ✅ Synced |
| **T** | **19** | **都道府県** | **grant_prefecture** | **taxonomy** | **✅ ★完全連携** |
| **U** | **20** | **市町村** | **grant_municipality** | **taxonomy** | **✅ ★完全連携** |
| **V** | **21** | **カテゴリ** | **grant_category** | **taxonomy** | **✅ ★完全連携** |
| **W** | **22** | **タグ** | **grant_tag** | **taxonomy** | **✅ ★完全連携** |
| X | 23 | 外部リンク | external_link | url | ✅ Synced |
| Y | 24 | 地域に関する備考 | area_notes | textarea | ✅ **Fixed** |
| Z | 25 | 必要書類 | required_documents_detailed | wysiwyg | ✅ **Fixed** |
| AA | 26 | 採択率(%) | adoption_rate | number | ✅ Synced |
| AB | 27 | 申請難易度 | difficulty_level | select | ✅ **Fixed** |
| AC | 28 | 対象経費 | eligible_expenses_detailed | wysiwyg | ✅ **Fixed** |
| AD | 29 | 補助率 | subsidy_rate_detailed | text | ✅ **Fixed** |
| AE | 30 | シート更新日 | sheet_updated_at | readonly | ✅ Auto |

---

## 🔄 Synchronization Flow

### **Sheets → WordPress (sync_sheets_to_wp)**

```
1. Read sheet data (A:AE range, 31 columns)
2. For each row:
   a. Check if post exists (by ID in column A)
   b. Update or create post with basic data (B-G)
   c. Update ACF fields (H-S, X-AD)
   d. Update taxonomies (T-W) ★ Categories here
   e. Log results
3. Write back new post IDs to spreadsheet
```

### **WordPress → Sheets (convert_post_to_sheet_row)**

```
1. Get post data and ACF fields
2. Get taxonomy terms (categories, prefectures, municipalities, tags)
3. Build 31-column row array (A-AE)
4. Return formatted row for batch write
```

### **Taxonomy Sync Details (T-W Columns)**

```php
// Prefecture (T column - index 19)
if (isset($row[19]) && !empty($row[19])) {
    $prefectures = array_map('trim', explode(',', $row[19]));
    wp_set_post_terms($post_id, $prefectures, 'grant_prefecture');
}

// Municipality (U column - index 20)
if (isset($row[20]) && !empty($row[20])) {
    $municipalities = array_map('trim', explode(',', $row[20]));
    wp_set_post_terms($post_id, $municipalities, 'grant_municipality');
}

// Category (V column - index 21) ★★★ YOUR CONCERN
if (isset($row[21]) && !empty($row[21])) {
    $categories = array_map('trim', explode(',', $row[21]));
    wp_set_post_terms($post_id, $categories, 'grant_category');
}

// Tag (W column - index 22)
if (isset($row[22]) && !empty($row[22])) {
    $tags = array_map('trim', explode(',', $row[22]));
    wp_set_post_terms($post_id, $tags, 'grant_tag');
}
```

---

## 🧪 Testing Recommendations

### **1. Verify Category Sync is Working**

```bash
# Step 1: Manually trigger sync from admin
# Navigate to: WordPress Admin → 助成金・補助金 → Google Sheets連携
# Click: "手動同期実行" → "Sheets → WP 同期"

# Step 2: Check logs
tail -100 /path/to/wp-content/debug.log | grep -A5 "Category sync"

# Expected output:
# Category sync result: {
#   'post_id': 123,
#   'raw_category_data': 'ビジネス支援,IT関連',
#   'categories_array': ['ビジネス支援', 'IT関連'],
#   'set_terms_result': [45, 67]  // Term IDs
# }
```

### **2. Verify Spreadsheet Data Format**

```
Column V (カテゴリ) examples:
✅ "ビジネス支援"                    → Single category
✅ "ビジネス支援,IT関連"              → Multiple categories
✅ "ビジネス支援, IT関連, スタートアップ" → Multiple with spaces
❌ Empty cell                        → No categories assigned
```

### **3. Verify WordPress Taxonomy**

```bash
# Check if grant_category taxonomy is registered
wp term list grant_category

# Check categories assigned to a post
wp post term list <POST_ID> grant_category
```

### **4. Test Sync in Both Directions**

```php
// Test 1: Sheets → WP
// 1. Add new category in spreadsheet Column V
// 2. Run "Sheets → WP 同期"
// 3. Verify category appears in WordPress admin

// Test 2: WP → Sheets
// 1. Add category to post in WordPress admin
// 2. Run "WP → Sheets 同期"
// 3. Verify category appears in spreadsheet Column V
```

---

## 📊 File Consistency Matrix

### **Field Name Alignment Check**

| Field Key | acf-fields.php | sheets-sync.php | sheets-webhook.php | sheets-init.php | Status |
|-----------|---------------|-----------------|-------------------|----------------|---------|
| area_notes | ✅ | ✅ (fixed) | ✅ | ✅ | 🟢 Aligned |
| required_documents_detailed | ✅ | ✅ (fixed) | ✅ | ✅ | 🟢 Aligned |
| adoption_rate | ✅ | ✅ | ✅ | ✅ | 🟢 Aligned |
| difficulty_level | ✅ | ✅ (fixed) | ✅ | ✅ | 🟢 Aligned |
| eligible_expenses_detailed | ✅ | ✅ (fixed) | ✅ | ✅ | 🟢 Aligned |
| subsidy_rate_detailed | ✅ | ✅ (fixed) | ✅ | ✅ | 🟢 Aligned |

---

## 🎯 Conclusion

### **Summary of Actions Taken:**

1. ✅ **Analyzed 13 PHP files** for sync logic and field definitions
2. ✅ **Verified category sync code** - functioning correctly
3. ✅ **Removed 4 unused ACF fields** per user requirements
4. ✅ **Fixed 5 field name inconsistencies** across sync modules
5. ✅ **Verified TinyMCE statusbar** - not present (nothing to remove)
6. ✅ **Documented complete 31-column mapping** (A-AE)
7. ✅ **Committed changes to git** with detailed message

### **Next Steps for User:**

1. **Test the sync:**
   - Trigger manual sync: Sheets → WP
   - Check WordPress admin for category assignments
   - Review debug logs for any errors

2. **If categories still not appearing:**
   - Check spreadsheet Column V has data
   - Verify data format (comma-separated)
   - Check WordPress debug log output
   - Verify taxonomy exists: `wp term list grant_category`

3. **Monitor logs:**
   ```bash
   tail -f /path/to/wp-content/debug.log | grep "Category sync"
   ```

### **Files Modified:**

- ✅ `inc/acf-fields.php` - Removed 4 unused fields
- ✅ `inc/sheets-sync.php` - Fixed 5 field name inconsistencies

### **Git Commit:**

```
commit cb461a9
Fix Sheets↔WordPress sync: Remove unused ACF fields and fix field name inconsistencies
```

---

## 📞 Support Information

If categories are still not syncing after these fixes, please provide:

1. Screenshot of spreadsheet Column V data
2. WordPress debug log output (grep for "Category sync")
3. Result of: `wp post term list <POST_ID> grant_category`
4. Sync direction used (Sheets → WP, WP → Sheets, or Both)

---

**Report Generated:** 2025-10-04  
**Analyst:** Claude (Anthropic AI)  
**Status:** ✅ Analysis Complete, Fixes Applied, Changes Committed
