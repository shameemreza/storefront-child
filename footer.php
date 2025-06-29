<?php
/**
 * Custom Footer for Storefront Child Theme
 *
 * @package storefront-child
 */
?>
    </div><!-- .col-full -->
</div><!-- #content -->

<?php do_action( 'storefront_before_footer' ); ?>

<footer id="colophon" class="site-footer" role="contentinfo">
    <div class="col-full">
        <div class="footer-main wooninja-footer">
            <div class="footer-left">
                <span class="footer-copyright">&copy; <?php echo esc_html( get_bloginfo( 'name' ) ); ?> <?php echo esc_html( date( 'Y' ) ); ?></span>
            </div>
            <div class="footer-right">
                <a href="https://woocommerce.com" target="_blank" rel="noopener noreferrer nofollow" class="footer-built-with-woo">Built with WooCommerce</a>
            </div>
        </div>
        <div class="footer-disclaimer">
            <p>This is a test site for personal use. This site is not for selling anything and is not related to the Woo brand. I work as a Happiness Engineer and use it to test and validate. It is not affiliated with or officially from WooCommerce.com or Automattic Inc. WooCommerce and Woo are trademarks of Automattic Inc.</p>
            <ul class="footer-links">
                <li><a href="https://woocommerce.com/support-policy/" target="_blank" rel="noopener noreferrer nofollow">WooCommerce Support Policy</a></li>
                <li><a href="https://woocommerce.com/refund-policy/" target="_blank" rel="noopener noreferrer nofollow">Refund Policy</a></li>
                <li><a href="https://woocommerce.com/development-services/" target="_blank" rel="noopener noreferrer nofollow">Development Services</a></li>
                <li><a href="https://wordpress.org/support/plugin/woocommerce/" target="_blank" rel="noopener noreferrer nofollow">WooCommerce Forums</a></li>
            </ul>
        </div>
    </div><!-- .col-full -->
</footer><!-- #colophon -->

<?php do_action( 'storefront_after_footer' ); ?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html> 