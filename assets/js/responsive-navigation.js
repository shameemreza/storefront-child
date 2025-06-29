/**
 * Responsive Navigation Handler
 *
 * Enhances the mobile navigation experience by ensuring proper display
 * of navigation elements across different viewport sizes.
 *
 * @package     Storefront Child
 * @version     1.0.0
 */
(function () {
  "use strict";

  /**
   * Optimizes navigation elements for current viewport size
   * Ensures proper display of menu toggle and related elements on mobile devices
   */
  function optimizeNavigation() {
    if (window.innerWidth <= 768) {
      // Mobile viewport optimizations

      // Ensure menu toggle button is properly displayed
      var menuToggle = document.querySelector(".menu-toggle");
      if (menuToggle) {
        menuToggle.style.display = "flex";
        menuToggle.style.visibility = "visible";
        menuToggle.style.opacity = "1";

        // Ensure the SVG hamburger icon is visible
        var svgIcon = menuToggle.querySelector(".hamburger-icon svg");
        if (svgIcon) {
          svgIcon.style.display = "inline-block";
          svgIcon.style.visibility = "visible";
          svgIcon.style.opacity = "1";
        }
      }

      // Optimize header spacing for mobile
      var siteHeader = document.querySelector(".site-header");
      if (siteHeader) {
        siteHeader.style.paddingTop = "0.5em";
        siteHeader.style.paddingBottom = "0.5em";
      }
    }
  }

  // Initialize on DOM content loaded
  document.addEventListener("DOMContentLoaded", optimizeNavigation);

  // Ensure proper display after all resources are loaded
  window.addEventListener("load", optimizeNavigation);

  // Respond to viewport size changes
  window.addEventListener("resize", optimizeNavigation);

  // Additional initialization attempts to handle delayed DOM updates
  // from third-party scripts or conditional loading
  setTimeout(optimizeNavigation, 500);
  setTimeout(optimizeNavigation, 1000);
  setTimeout(optimizeNavigation, 2000);
})();
