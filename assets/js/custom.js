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

  // Ninja profile interactive effects
  const heroContainer = $(".hero-image-container");
  const heroImage = $(".hero-image");
  const shurikens = $(".shuriken");

  // Mouse move effect for hero image
  $(".hero-section").on("mousemove", function (e) {
    if ($(window).width() < 768) return; // Skip on mobile

    const containerRect = heroContainer[0].getBoundingClientRect();
    const centerX = containerRect.left + containerRect.width / 2;
    const centerY = containerRect.top + containerRect.height / 2;

    // Calculate distance from center (normalized -1 to 1)
    const moveX = ((e.clientX - centerX) / (containerRect.width / 2)) * 10;
    const moveY = ((e.clientY - centerY) / (containerRect.height / 2)) * 10;

    // Apply subtle tilt effect
    heroImage.css({
      transform: `perspective(1000px) rotateY(${moveX}deg) rotateX(${-moveY}deg)`,
      transition: "transform 0.2s ease-out",
    });

    // Move shurikens in opposite direction for parallax effect
    shurikens.each(function (index) {
      const factor = (index + 1) * 0.5;
      $(this).css({
        transform: `translate(${-moveX * factor}px, ${-moveY * factor}px)`,
        transition: "transform 0.3s ease-out",
      });
    });
  });

  // Reset on mouse leave
  $(".hero-section").on("mouseleave", function () {
    heroImage.css({
      transform: "perspective(1000px) rotateY(0) rotateX(0)",
      transition: "transform 0.5s ease-out",
    });

    shurikens.css({
      transform: "translate(0, 0)",
      transition: "transform 0.5s ease-out",
    });
  });

  // Create smoke effect on click
  heroContainer.on("click", function () {
    // Create a new smoke element
    const smoke = $('<div class="smoke-burst"></div>');
    heroContainer.append(smoke);

    // Animate and remove
    setTimeout(function () {
      smoke.remove();
    }, 1000);
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
