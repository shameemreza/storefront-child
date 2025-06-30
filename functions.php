<?php
/**
 * Storefront Child Theme functions and definitions
 */

/**
 * Disable WooCommerce Coming Soon mode when theme is activated
 */
function storefront_child_disable_coming_soon_mode() {
    // Set the store to live mode
    update_option('woocommerce_coming_soon', 'no');
}
add_action('after_switch_theme', 'storefront_child_disable_coming_soon_mode');

/**
 * Fix for the storefront_sticky_single_add_to_cart error in coming soon mode
 */
function storefront_child_fix_sticky_add_to_cart() {
    // Remove the original action
    remove_action('storefront_after_footer', 'storefront_sticky_single_add_to_cart', 999);
    
    // Add our custom implementation that checks for coming soon mode
    add_action('storefront_after_footer', 'storefront_child_sticky_single_add_to_cart', 999);
}
add_action('init', 'storefront_child_fix_sticky_add_to_cart');

/**
 * Custom implementation of sticky add to cart that's safe in coming soon mode
 */
function storefront_child_sticky_single_add_to_cart() {
    global $product;

    if (class_exists('Storefront_Sticky_Add_to_Cart') || true !== get_theme_mod('storefront_sticky_add_to_cart')) {
        return;
    }

    // Make sure we have a valid product object
    if (!is_a($product, 'WC_Product') && is_product()) {
        $product = wc_get_product(get_the_ID());
        if (!$product) {
            return;
        }
    }

    // If still no valid product or not on a product page, exit
    if (!is_a($product, 'WC_Product') || !is_product()) {
        return;
    }

    $show = false;

    if ($product->is_purchasable() && $product->is_in_stock()) {
        $show = true;
    } elseif ($product->is_type('external')) {
        $show = true;
    }

    if (!$show) {
        return;
    }

    $params = apply_filters(
        'storefront_sticky_add_to_cart_params',
        array(
            'trigger_class' => 'entry-summary',
        )
    );

    wp_localize_script('storefront-sticky-add-to-cart', 'storefront_sticky_add_to_cart_params', $params);

    wp_enqueue_script('storefront-sticky-add-to-cart');
    ?>
        <section class="storefront-sticky-add-to-cart">
            <div class="col-full">
                <div class="storefront-sticky-add-to-cart__content">
                    <?php echo wp_kses_post(woocommerce_get_product_thumbnail()); ?>
                    <div class="storefront-sticky-add-to-cart__content-product-info">
                        <span class="storefront-sticky-add-to-cart__content-title"><?php esc_html_e('You\'re viewing:', 'storefront'); ?> <strong><?php the_title(); ?></strong></span>
                        <span class="storefront-sticky-add-to-cart__content-price"><?php echo wp_kses_post($product->get_price_html()); ?></span>
                        <?php echo wp_kses_post(wc_get_rating_html($product->get_average_rating())); ?>
                    </div>
                    <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" class="storefront-sticky-add-to-cart__content-button button alt" rel="nofollow">
                        <?php echo esc_attr($product->add_to_cart_text()); ?>
                    </a>
                </div>
            </div>
        </section><!-- .storefront-sticky-add-to-cart -->
    <?php
}

// Set up theme defaults and registers support for various WordPress features
function storefront_child_setup() {
    // Add custom logo support
    add_theme_support('custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-width'  => true,
        'flex-height' => true,
    ));
}
add_action('after_setup_theme', 'storefront_child_setup', 11);

// Enqueue parent and child theme styles
function storefront_child_enqueue_styles() {
    // Enqueue parent theme style
    wp_enqueue_style('storefront-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme('storefront')->get('Version')
    );
    
    // Enqueue fonts
    wp_enqueue_style('storefront-child-fonts',
        get_stylesheet_directory_uri() . '/assets/css/fonts.css',
        array(),
        wp_get_theme()->get('Version')
    );
    
    // Enqueue child theme style
    wp_enqueue_style('storefront-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('storefront-style'),
        wp_get_theme()->get('Version')
    );
    
    // Enqueue header specific CSS
    wp_enqueue_style('storefront-child-header',
        get_stylesheet_directory_uri() . '/assets/css/header.css',
        array('storefront-child-style'),
        wp_get_theme()->get('Version')
    );
    
    // Enqueue main CSS file for custom styles
    wp_enqueue_style('storefront-child-main',
        get_stylesheet_directory_uri() . '/assets/css/main.css',
        array('storefront-child-style', 'storefront-child-header'),
        wp_get_theme()->get('Version')
    );
    
    // Enqueue homepage specific CSS
    wp_enqueue_style('storefront-child-homepage',
        get_stylesheet_directory_uri() . '/assets/css/homepage.css',
        array('storefront-child-style'),
        wp_get_theme()->get('Version')
    );
    
    // Enqueue custom JS
    wp_enqueue_script('storefront-child-js',
        get_stylesheet_directory_uri() . '/assets/js/custom.js',
        array('jquery'),
        wp_get_theme()->get('Version'),
        true
    );
}
add_action('wp_enqueue_scripts', 'storefront_child_enqueue_styles');

// Add custom footer disclaimer
function storefront_child_footer_disclaimer() {
    ?>
    <div class="disclaimer">
        <p>This is a test site for personal use. This site is not for selling anything and is not related to the Woo brand. 
        I work as a Happiness Engineer and use it to test and validate. It is not affiliated with or officially from WooCommerce.com 
        or Automattic Inc. WooCommerce and Woo are trademarks of Automattic Inc.</p>
    </div>
    <?php
}
add_action('storefront_footer', 'storefront_child_footer_disclaimer', 15);

/**
 * Product filtering functions
 */
function get_product_type_filters() {
    return array(
        'simple'      => 'Simple Products',
        'variable'    => 'Variable Products',
        'bundle'      => 'Bundle Products',
        'composite'   => 'Composite Products',
        'subscription' => 'Subscription Products',
        'booking'     => 'Booking Products'
    );
}

function get_filtered_products($product_type = '') {
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 12,
    );
    
    // Add product type filter
    if (!empty($product_type)) {
        if ($product_type === 'subscription') {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => array('subscription', 'variable-subscription'),
                    'operator' => 'IN',
                ),
            );
        } else {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => $product_type,
                ),
            );
        }
    }
    
    return new WP_Query($args);
}

/**
 * AJAX handler for product filtering
 */
function filter_products_ajax() {
    $product_type = isset($_POST['product_type']) ? sanitize_text_field($_POST['product_type']) : '';
    
    $products = get_filtered_products($product_type);
    
    ob_start();
    
    if ($products->have_posts()) {
        woocommerce_product_loop_start();
        
        while ($products->have_posts()) {
            $products->the_post();
            wc_get_template_part('content', 'product');
        }
        
        woocommerce_product_loop_end();
        woocommerce_pagination();
    } else {
        echo '<p class="woocommerce-info">No products found.</p>';
    }
    
    wp_reset_postdata();
    
    $response = ob_get_clean();
    
    echo $response;
    die();
}
add_action('wp_ajax_filter_products', 'filter_products_ajax');
add_action('wp_ajax_nopriv_filter_products', 'filter_products_ajax'); 

// Change the From address.
add_filter( 'wp_mail_from', function ( $original_email_address ) {
    return 'shameem.reza@automattic.com';
} );
 
// Change the From name.
add_filter( 'wp_mail_from_name', function ( $original_email_from ) {
    return 'Woo Ninja 🥷🏻';
} );

// Hide bookable product templates message from WooCommerce bookings
add_action( 'admin_enqueue_scripts', function() {
	remove_action( 'admin_notices', [ 'WC_Bookings_Admin_Install', 'bookable_product_templates_notice' ] );
});

// Add custom body class
function storefront_child_body_classes($classes) {
    $classes[] = 'wooninja-theme';
    return $classes;
}
add_filter('body_class', 'storefront_child_body_classes');

// Add custom homepage template
function storefront_child_add_page_templates($templates) {
    $templates['template-homepage-custom.php'] = 'Custom Homepage';
    return $templates;
}
add_filter('theme_page_templates', 'storefront_child_add_page_templates');

// Disable default Storefront sidebar on specific pages
function storefront_child_disable_sidebar($is_active) {
    if (is_page_template('template-homepage-custom.php')) {
        return false;
    }
    return $is_active;
}
add_filter('storefront_sidebar', 'storefront_child_disable_sidebar');

// Add WooCommerce support
function storefront_child_woocommerce_setup() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'storefront_child_woocommerce_setup');

// Add custom CSS variables for WooCommerce colors
function storefront_child_add_css_variables() {
    ?>
    <style>
        :root {
            /* WooCommerce color palette */
            --woo-purple-10: #F6F2F9;
            --woo-purple-20: #EBE0F5;
            --woo-purple-30: #DCB8FF;
            --woo-purple-40: #C292FF;
            --woo-purple-50: #7F54B3;
            --woo-purple-60: #674399;
            --woo-purple-70: #533180;
            --woo-purple-80: #3E2066;
            --woo-purple-90: #2A0F4D;
            
            --woo-gray-0: #F7F7F7;
            --woo-gray-5: #F0F0F0;
            --woo-gray-10: #E0E0E0;
            --woo-gray-20: #C0C0C0;
            --woo-gray-30: #A0A0A0;
            --woo-gray-40: #808080;
            --woo-gray-50: #606060;
            --woo-gray-60: #404040;
            --woo-gray-70: #303030;
            --woo-gray-80: #202020;
            --woo-gray-90: #101010;
            
            --woo-white: #FFFFFF;
            --woo-black: #000000;
        }
    </style>
    <?php
}
add_action('wp_head', 'storefront_child_add_css_variables');

/**
 * Simplified header layout
 * Removes cart and other redundant elements
 */
function storefront_child_header_layout() {
    // Remove default header elements
    remove_action('storefront_header', 'storefront_site_branding', 20);
    remove_action('storefront_header', 'storefront_secondary_navigation', 30);
    remove_action('storefront_header', 'storefront_product_search', 40);
    
    // Add our custom header elements in the desired order
    add_action('storefront_header', 'storefront_child_site_title', 20);
    add_action('storefront_header', 'storefront_primary_navigation', 30);
}
add_action('init', 'storefront_child_header_layout');

/**
 * Enqueue scripts and styles
 */
function storefront_child_scripts() {
    // Enqueue custom JS
    wp_enqueue_script(
        'storefront-child-custom',
        get_stylesheet_directory_uri() . '/assets/js/custom.js',
        array('jquery'),
        wp_get_theme()->get('Version') . '.' . time(),
        true
    );
    
    // Localize script for AJAX calls
    wp_localize_script(
        'storefront-child-custom',
        'ajax_object',
        array('ajax_url' => admin_url('admin-ajax.php'))
    );
    
    // Enqueue our custom navigation JS
    wp_enqueue_script(
        'storefront-child-navigation',
        get_stylesheet_directory_uri() . '/assets/js/navigation.js',
        array('jquery'),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'storefront_child_scripts');

// Automatically Delete Woocommerce Images After Deleting a Product
add_action('before_delete_post', 'delete_product_images', 10, 1);

function delete_product_images($post_id) {
    // Check if user has the capability to delete products
    if (!current_user_can('delete_products')) {
        return;
    }
    $product = wc_get_product($post_id);

    if (!$product) {
        return;
    }
    $featured_image_id = $product->get_image_id();
    $image_galleries_id = $product->get_gallery_image_ids();

    if ($featured_image_id && !is_image_used($featured_image_id, $post_id)) {
        wp_delete_attachment($featured_image_id, true);
    }

    if ($image_galleries_id) {
        foreach ($image_galleries_id as $image_id) {
            if (!is_image_used($image_id, $post_id)) {
                wp_delete_attachment($image_id, true);
            }
        }
    }
}

function is_image_used($image_id, $current_product_id) {
    global $wpdb;
    
    // Check if image is used as featured image for other posts
    $featured_image_query = $wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_thumbnail_id' AND meta_value = %d AND post_id != %d LIMIT 1",
        $image_id,
        $current_product_id
    );
    
    $featured_image_result = $wpdb->get_var($featured_image_query);
    
    if ($featured_image_result) {
        return true;
    }
    
    // Check if image is used in gallery for other products
    $gallery_query = $wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_product_image_gallery' AND meta_value LIKE %s AND post_id != %d LIMIT 1",
        '%' . $wpdb->esc_like($image_id) . '%',
        $current_product_id
    );
    
    $gallery_result = $wpdb->get_var($gallery_query);
    
    return $gallery_result ? true : false;
}

// Remove search from header
function storefront_child_remove_search() {
    remove_action('storefront_header', 'storefront_product_search', 40);
}
add_action('init', 'storefront_child_remove_search');

/**
 * Custom site title with logo
 */
function storefront_child_site_title() {
    ?>
    <div class="site-branding">
        <?php
        if (function_exists('the_custom_logo') && has_custom_logo()) {
            the_custom_logo();
        } else {
            if (is_front_page() && is_home()) : ?>
                <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
            <?php else : ?>
                <p class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></p>
            <?php endif;
        }
        ?>
    </div>
    <?php
}

// Filter site title markup
function storefront_child_site_title_markup($title) {
    if (is_front_page()) {
        $title = str_replace('Woo', '<span class="title-woo">Woo</span>', $title);
        $title = str_replace('Ninja', '<span class="title-ninja">Ninja</span>', $title);
    }
    return $title;
}
add_filter('the_title', 'storefront_child_site_title_markup');

// Add customizer options
function storefront_child_customize_register($wp_customize) {
    // Hero Section
    $wp_customize->add_section('hero_section', array(
        'title'       => __('Hero Section', 'storefront-child'),
        'description' => __('Customize the homepage hero section', 'storefront-child'),
        'priority'    => 30,
    ));
    
    // Hero Title
    $wp_customize->add_setting('hero_title', array(
        'default'           => 'Ninja Testing Playground',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('hero_title', array(
        'label'       => __('Hero Section Title', 'storefront-child'),
        'section'     => 'hero_section',
        'type'        => 'text',
    ));
    
    // Hero Description
    $wp_customize->add_setting('hero_description', array(
        'default'           => 'Welcome to my WooCommerce testing playground. This site is used for day-to-day testing of WooCommerce and WooCommerce.com extensions. As a Happiness Engineer, I use this environment to test, validate, and troubleshoot various WooCommerce functionalities.',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control('hero_description', array(
        'label'       => __('Hero Section Description', 'storefront-child'),
        'section'     => 'hero_section',
        'type'        => 'textarea',
    ));
    
    // Hero Image
    $wp_customize->add_setting('hero_image', array(
        'default'           => get_stylesheet_directory_uri() . '/assets/images/profile.png',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'hero_image', array(
        'label'       => __('Hero Section Image', 'storefront-child'),
        'section'     => 'hero_section',
        'description' => __('Upload an image for the hero section. Recommended size: 300x300px.', 'storefront-child'),
    )));
    
    // Hero Background Color
    $wp_customize->add_setting('hero_bg_color', array(
        'default'           => '#271041', // var(--woo-purple-90)
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'hero_bg_color', array(
        'label'       => __('Hero Background Color', 'storefront-child'),
        'section'     => 'hero_section',
    )));
    
    // Hero Text Color
    $wp_customize->add_setting('hero_text_color', array(
        'default'           => '#ffffff', // var(--woo-white)
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'hero_text_color', array(
        'label'       => __('Hero Text Color', 'storefront-child'),
        'section'     => 'hero_section',
    )));
}
add_action('customize_register', 'storefront_child_customize_register');

// Add hero section styles
function storefront_child_hero_section_styles() {
    $bg_color = get_theme_mod('hero_bg_color', '#271041');
    $text_color = get_theme_mod('hero_text_color', '#ffffff');
    ?>
    <style>
        .hero-section {
            background-color: <?php echo esc_attr($bg_color); ?> !important;
            color: <?php echo esc_attr($text_color); ?> !important;
        }
        .hero-section h1 {
            color: <?php echo esc_attr($text_color); ?> !important;
        }
        .hero-section p {
            color: <?php echo esc_attr($text_color); ?> !important;
            opacity: 0.9;
        }
    </style>
    <?php
}
add_action('wp_head', 'storefront_child_hero_section_styles');