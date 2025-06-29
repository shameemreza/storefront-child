<?php
/**
 * Template Name: Homepage
 *
 * @package storefront-child
 */

get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
            
            <?php
            /**
             * Hero Section
             */
            // Get hero section content from customizer
            $hero_title = get_theme_mod('hero_title', 'WooNinja Testing Playground');
            $hero_description = get_theme_mod('hero_description', 'Welcome to my WooCommerce testing playground. This site is used for day-to-day testing of WooCommerce and WooCommerce.com extensions. As a Happiness Engineer, I use this environment to test, validate, and troubleshoot various WooCommerce functionalities.');
            $hero_image = get_theme_mod('hero_image', get_stylesheet_directory_uri() . '/assets/images/profile.png');
            ?>
            <section class="hero-section">
                <div class="col-full">
                    <div class="hero-content">
                        <div class="hero-image">
                            <img src="<?php echo esc_url($hero_image); ?>" alt="<?php echo esc_attr($hero_title); ?>" />
                        </div>
                        <div class="hero-text">
                            <h1><?php echo esc_html($hero_title); ?></h1>
                            <p><?php echo wp_kses_post($hero_description); ?></p>
                        </div>
                    </div>
                </div>
            </section>

            <?php
            /**
             * Product Filters Section
             */
            ?>
            <section class="product-filters">
                <div class="col-full">
                    <h2>Browse Products by Type</h2>
                    <ul>
                        <li><a href="#" class="active" data-type="">All Products</a></li>
                        <?php foreach (get_product_type_filters() as $type => $label) : ?>
                            <li><a href="#" data-type="<?php echo esc_attr($type); ?>"><?php echo esc_html($label); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>

            <?php
            /**
             * Products Section
             */
            ?>
            <section class="products-section">
                <div class="col-full">
                    <div class="woocommerce-products">
                        <?php
                        $products = get_filtered_products();
                        
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
                        ?>
                    </div>
                </div>
            </section>

            <?php
            /**
             * CTA Section
             */
            ?>
            <section class="cta-section">
                <div class="col-full">
                    <h2>Need WooCommerce Support?</h2>
                    <p>If you're looking for help with your WooCommerce store, the WooCommerce.com support team is ready to assist you.</p>
                    <a href="https://woocommerce.com/my-account/create-a-ticket/" class="button">Contact Support</a>
                </div>
            </section>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php
get_footer(); 