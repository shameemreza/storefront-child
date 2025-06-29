jQuery(document).ready(function ($) {
  // Product filtering
  $(".product-filters a").on("click", function (e) {
    e.preventDefault();

    var productType = $(this).data("type");

    // Update active class
    $(".product-filters a").removeClass("active");
    $(this).addClass("active");

    // Show loading
    $(".woocommerce-products").addClass("loading");

    // AJAX request
    $.ajax({
      url: ajax_object.ajax_url,
      type: "post",
      data: {
        action: "filter_products",
        product_type: productType,
      },
      success: function (response) {
        // Update products
        $(".woocommerce-products").html(response).removeClass("loading");
      },
    });
  });

  // Ensure mobile menu toggle is visible on mobile devices
  function checkMobileMenuVisibility() {
    if ($(window).width() <= 768) {
      $(".menu-toggle").css({
        display: "flex",
        visibility: "visible",
        opacity: "1",
      });

      // Force the svg icon to be visible too
      $(".hamburger-icon svg").css({
        display: "block",
        visibility: "visible",
      });
    } else {
      $(".menu-toggle").css("display", "none");
    }
  }

  // Run on page load
  checkMobileMenuVisibility();

  // Also run after a slight delay to handle any dynamic content loading
  setTimeout(checkMobileMenuVisibility, 500);

  // Run on window resize
  $(window).on("resize", function () {
    checkMobileMenuVisibility();
  });

  // Improve mobile menu behavior
  $(".menu-toggle").on("click", function () {
    setTimeout(function () {
      if ($(".main-navigation").hasClass("toggled")) {
        $("body").addClass("mobile-menu-open");
      } else {
        $("body").removeClass("mobile-menu-open");
      }
    }, 50);
  });

  // Close mobile menu when clicking outside
  $(document).on("click", function (e) {
    if (
      $(".main-navigation").hasClass("toggled") &&
      !$(e.target).closest(".main-navigation").length &&
      !$(e.target).closest(".menu-toggle").length
    ) {
      $(".menu-toggle").trigger("click");
    }
  });

  // Smooth scroll for anchor links
  $('a[href^="#"]').on("click", function (e) {
    var target = $(this.getAttribute("href"));
    if (target.length) {
      e.preventDefault();
      $("html, body")
        .stop()
        .animate(
          {
            scrollTop: target.offset().top - 100,
          },
          500
        );
    }
  });

  // Cart notification badge functionality
  function updateCartNotification() {
    var $cartCount = $(".site-header-cart .cart-contents .count");
    var countValue = parseInt($cartCount.text().trim());

    // Hide count if it's 0 or NaN
    if (isNaN(countValue) || countValue === 0) {
      if ($cartCount.length) {
        $cartCount.hide();
      }
    } else {
      if ($cartCount.length) {
        $cartCount.show();
      }
    }
  }

  // Run on page load
  updateCartNotification();

  // Update when fragments are refreshed
  $(document.body).on("wc_fragments_refreshed", function () {
    updateCartNotification();
  });

  // Update when cart changes
  $(document.body).on(
    "added_to_cart removed_from_cart updated_cart_totals",
    function () {
      updateCartNotification();
    }
  );

  // Fix cart hover issue - prevent selection of empty space
  $(".site-header-cart").on("mouseenter mouseleave", function (e) {
    if (
      $(e.target).is(".site-header-cart") ||
      $(e.target).closest(".site-header-cart").length
    ) {
      e.stopPropagation();
    }
  });

  // Enhanced 3D logo text animation
  function animateLogoText() {
    var $titleNinja = $(".title-ninja");
    var $titleWoo = $(".title-woo");

    // Apply 3D effect to the ninja text
    $titleNinja.css({
      transform: "translateZ(10px) scale(1.05)",
      "text-shadow": "0 5px 15px rgba(0, 0, 0, 0.3)",
    });

    // After a delay, return to normal
    setTimeout(function () {
      $titleNinja.css({
        transform: "translateZ(0) scale(1)",
        "text-shadow": "0 2px 4px rgba(0, 0, 0, 0.2)",
      });
    }, 700);
  }

  // Animate on hover with enhanced 3D effect
  $(".site-title a").hover(
    function () {
      $(this).css("perspective", "800px");
      animateLogoText();
    },
    function () {
      setTimeout(function () {
        $(".site-title a").css("perspective", "none");
      }, 700);
    }
  );

  // Animate once on page load
  setTimeout(function () {
    animateLogoText();
  }, 1000);
});
