<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
function sempai_breadcrumbs_add_meta_box()
{
    add_meta_box(
        'sempai_breadcrumbs_meta_box',
        'Sempai Custom Breadcrumbs',
        'sempai_breadcrumbs_meta_box_html',
        'page',
        'normal',
        'high'
    );
}

add_action('add_meta_boxes', 'sempai_breadcrumbs_add_meta_box');
function sempai_breadcrumbs_meta_box_html($post)
{
    $selected_pages = get_post_meta($post->ID, '_breadcrumbs_meta_key', true);
    $selected_pages = !empty($selected_pages) ? $selected_pages : array(array('page' => '', 'name' => ''));
    wp_nonce_field('sempai_save_breadcrumbs', 'sempai_breadcrumbs_nonce');
    ?>
    <div style="display: flex; justify-content: center;">
        <div class="block-list-appender wp-block">
            <div id="sempai-breadcrumbs-fields" class="mt-2 block-editor-default-block-appender">
                <table class="wp-list-table widefat striped table-view-list pages">
                    <thead>
                    <tr>
                        <th class="manage-column column-title column-primary">Page</th>
                        <th class="manage-column">Name</th>
                        <th class="manage-column">Operation</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($selected_pages

                    as $key => $selected_page): ?>

                    <tr class="sempai-breadcrumbs-field">
                        <td class="title column-title has-row-actions column-primary page-title">
                            <select name="sempai_breadcrumbs_fields[page][]"
                                    class="components-combobox-control__input components-form-token-field__input">
                                <?php
                                $pages = get_pages();
                                foreach ($pages as $page) {
                                    $selected = $selected_page['page'] == $page->ID ? 'selected' : '';
                                    echo '<option value="' . esc_attr($page->ID) . '" ' . esc_html($selected) . '>' . esc_html($page->post_title) . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="sempai_breadcrumbs_fields[name][]" class="form-control"
                                   value="<?php echo esc_html($selected_page['name']); ?>"
                                   placeholder="<?php echo esc_html($page->post_title); ?>">
                        </td>
                        <td>
                            <?php if ($key !== 0): ?>
                                <button type="button" style="margin-top: 5px;"
                                        class="remove-field button add-keyword">
                                    Delete
                                </button>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-2" style="display: flex; justify-content: end">
                <button type="button"
                        class="button add-keyword"
                        id="add-field">Add page
                </button>
            </div>
        </div>
    </div>
    <?php
}