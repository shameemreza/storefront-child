/**
 * Enhanced mobile navigation for Storefront Child Theme
 */
(function ($) {
  "use strict";

  $(document).ready(function () {
    // Mobile menu toggle
    $(".menu-toggle").on("click", function () {
      $(".main-navigation").slideToggle(300);
      $(this).toggleClass("toggled");

      if ($(this).hasClass("toggled")) {
        $(this).attr("aria-expanded", "true");
        $(".main-navigation").addClass("toggled");
      } else {
        $(this).attr("aria-expanded", "false");
        $(".main-navigation").removeClass("toggled");
      }
    });

    // Handle sub-menu toggles on mobile
    if ($(window).width() < 768) {
      $(".menu-item-has-children > a").after(
        '<span class="sub-menu-toggle"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg></span>'
      );

      $(".sub-menu-toggle").on("click", function (e) {
        e.preventDefault();
        $(this).toggleClass("active");
        $(this)
          .closest(".menu-item-has-children")
          .find("> .sub-menu")
          .slideToggle(200);
      });
    }

    // Adjust menu on resize
    $(window).resize(function () {
      if ($(window).width() > 768) {
        $(".main-navigation").css("display", "");
        $(".main-navigation").removeClass("toggled");
      }
    });
  });
})(jQuery);
