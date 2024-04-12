<?php
/**
 * Plugin Name: WooCommerce Order Date Range Filter
 * Plugin URI: https://titandigital.ie
 * Description: Adds a custom date range filter to the WooCommerce orders admin page.
 * Version: 1.0
 * Author: Titan Digital
 * Author URI: https://titandigital.ie
 */

// Fire up the date picker UI (Uses Google Smoothness)
add_action('admin_enqueue_scripts', 'woocommerce_load_admin_datepicker');
function woocommerce_load_admin_datepicker($hook) {
    global $typenow;

    if ($typenow == 'shop_order' && ($hook == 'edit.php')) {
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css');
    }
}

// Add date selector inputs
add_action('restrict_manage_posts', 'woocommerce_admin_orders_date_selector_filter', 20);
function woocommerce_admin_orders_date_selector_filter() {
    global $typenow;

    if ('shop_order' !== $typenow) {
        return;
    }

    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

    echo '<input type="text" name="start_date" id="start_date" placeholder="' . esc_attr__('Start Date...', 'woocommerce') . '" value="' . esc_attr($start_date) . '"/>';
    echo '<input type="text" name="end_date" id="end_date" placeholder="' . esc_attr__('End Eate...', 'woocommerce') . '" value="' . esc_attr($end_date) . '"/>';

    ?>
    <script type="text/javascript">
        jQuery(function($) {
            $('#start_date, #end_date').datepicker({
                dateFormat: 'yy-mm-dd'
            });
        });
    </script>
    <?php
}

// Modify the query based on selected dates
add_filter('request', 'woocommerce_filter_orders_by_date_range');
function woocommerce_filter_orders_by_date_range($vars) {
    global $typenow;

    if ('shop_order' === $typenow && isset($_GET['start_date']) && isset($_GET['end_date']) && !empty($_GET['start_date']) && !empty($_GET['end_date'])) {
        $start_date = date('Y-m-d', strtotime($_GET['start_date']));
        $end_date = date('Y-m-d', strtotime($_GET['end_date']));

        $vars['date_query'] = array(
            array(
                'after'     => $start_date,
                'before'    => $end_date,
                'inclusive' => true,
            ),
        );
    }

    return $vars;
}
