<?php
/**
 * Category Hierarchy Display
 * カテゴリー階層表示（親→子の2段階構造）
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// 親カテゴリーのみを取得
$parent_categories = get_terms(array(
    'taxonomy' => 'grant_category',
    'parent' => 0, // 親カテゴリーのみ
    'hide_empty' => false,
    'orderby' => 'count',
    'order' => 'DESC'
));

$archive_base_url = get_post_type_archive_link('grant');
?>

<div class="category-hierarchy-section">
    <div class="hierarchy-container">
        
        <?php if (!empty($parent_categories) && !is_wp_error($parent_categories)): ?>
            
            <?php foreach ($parent_categories as $parent_index => $parent_cat): 
                // 子カテゴリーを取得
                $child_categories = get_terms(array(
                    'taxonomy' => 'grant_category',
                    'parent' => $parent_cat->term_id,
                    'hide_empty' => false,
                    'orderby' => 'count',
                    'order' => 'DESC'
                ));
                
                $has_children = !empty($child_categories) && !is_wp_error($child_categories);
                $parent_url = add_query_arg('category', $parent_cat->slug, $archive_base_url);
            ?>
            
            <div class="hierarchy-item" data-aos="fade-up" data-aos-delay="<?php echo $parent_index * 100; ?>">
                <!-- 親カテゴリー -->
                <div class="parent-category">
                    <button class="parent-category-button <?php echo $has_children ? 'has-children' : ''; ?>" 
                            data-category-id="<?php echo $parent_cat->term_id; ?>"
                            <?php if (!$has_children): ?>
                                onclick="window.location.href='<?php echo esc_url($parent_url); ?>'"
                            <?php endif; ?>>
                        <div class="parent-info">
                            <span class="parent-name"><?php echo esc_html($parent_cat->name); ?></span>
                            <span class="parent-count"><?php echo number_format($parent_cat->count); ?>件</span>
                        </div>
                        <?php if ($has_children): ?>
                        <span class="toggle-icon">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                        <?php else: ?>
                        <span class="view-icon">
                            <i class="fas fa-arrow-right"></i>
                        </span>
                        <?php endif; ?>
                    </button>
                </div>
                
                <!-- 子カテゴリー -->
                <?php if ($has_children): ?>
                <div class="child-categories" id="children-<?php echo $parent_cat->term_id; ?>">
                    <div class="child-categories-grid">
                        <?php foreach ($child_categories as $child_cat): 
                            $child_url = add_query_arg('category', $child_cat->slug, $archive_base_url);
                        ?>
                        <a href="<?php echo esc_url($child_url); ?>" class="child-category-item">
                            <span class="child-name"><?php echo esc_html($child_cat->name); ?></span>
                            <span class="child-count"><?php echo number_format($child_cat->count); ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <?php endforeach; ?>
            
        <?php else: ?>
            <p class="no-categories">カテゴリーが見つかりませんでした。</p>
        <?php endif; ?>
        
    </div>
</div>

<style>
/* ============================================================
   CATEGORY HIERARCHY STYLES
   カテゴリー階層スタイル
   ============================================================ */

.category-hierarchy-section {
    margin: 40px 0;
}

.hierarchy-container {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* 親カテゴリー */
.hierarchy-item {
    background: #ffffff;
    border: 2px solid #e5e5e5;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.hierarchy-item:hover {
    border-color: #000000;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

.parent-category-button {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: background 0.2s ease;
}

.parent-category-button:hover {
    background: #fafafa;
}

.parent-category-button.active {
    background: #f5f5f5;
}

.parent-info {
    display: flex;
    align-items: center;
    gap: 16px;
    flex: 1;
}

.parent-name {
    font-size: 17px;
    font-weight: 700;
    color: #000000;
}

.parent-count {
    padding: 4px 12px;
    background: #f5f5f5;
    border: 1px solid #e5e5e5;
    border-radius: 16px;
    font-size: 13px;
    font-weight: 700;
    color: #000000;
}

.toggle-icon,
.view-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f5f5f5;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.parent-category-button:hover .toggle-icon,
.parent-category-button:hover .view-icon {
    background: #000000;
    color: #ffffff;
}

.toggle-icon i {
    transition: transform 0.3s ease;
}

.parent-category-button.active .toggle-icon i {
    transform: rotate(180deg);
}

/* 子カテゴリー */
.child-categories {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease, padding 0.4s ease;
    background: #fafafa;
}

.child-categories.expanded {
    max-height: 2000px;
    padding: 16px 24px 24px;
}

.child-categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 10px;
}

.child-category-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: #ffffff;
    border: 1px solid #e5e5e5;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.child-category-item:hover {
    background: #000000;
    border-color: #000000;
    transform: translateX(4px);
}

.child-name {
    font-size: 14px;
    font-weight: 600;
    color: #2d2d2d;
    transition: color 0.2s ease;
}

.child-category-item:hover .child-name {
    color: #ffffff;
}

.child-count {
    padding: 2px 8px;
    background: #f5f5f5;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 700;
    color: #000000;
    transition: all 0.2s ease;
}

.child-category-item:hover .child-count {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
}

/* No categories message */
.no-categories {
    text-align: center;
    padding: 40px;
    color: #9b9b9b;
    font-size: 14px;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .parent-category-button {
        padding: 16px 20px;
    }
    
    .parent-name {
        font-size: 15px;
    }
    
    .parent-count {
        padding: 3px 10px;
        font-size: 12px;
    }
    
    .child-categories-grid {
        grid-template-columns: 1fr;
        gap: 8px;
    }
    
    .child-category-item {
        padding: 10px 14px;
    }
    
    .child-name {
        font-size: 13px;
    }
}

@media (max-width: 480px) {
    .parent-info {
        gap: 12px;
    }
    
    .parent-name {
        font-size: 14px;
    }
}
</style>

<script>
(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        // 親カテゴリーボタンのクリックイベント
        const parentButtons = document.querySelectorAll('.parent-category-button.has-children');
        
        parentButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const categoryId = this.dataset.categoryId;
                const childContainer = document.getElementById('children-' + categoryId);
                
                if (!childContainer) return;
                
                // トグル状態
                const isExpanded = childContainer.classList.contains('expanded');
                
                // 全ての他の子カテゴリーを閉じる（アコーディオン動作）
                document.querySelectorAll('.child-categories.expanded').forEach(function(el) {
                    el.classList.remove('expanded');
                });
                
                document.querySelectorAll('.parent-category-button.active').forEach(function(btn) {
                    btn.classList.remove('active');
                });
                
                // 現在のカテゴリーをトグル
                if (!isExpanded) {
                    childContainer.classList.add('expanded');
                    this.classList.add('active');
                }
            });
        });
    });
})();
</script>
<?php
