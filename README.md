# Storefront Child Theme

A modern, clean child theme for WooCommerce's Storefront theme with enhanced header styling and mobile responsiveness.

## Features

- Modern header layout with logo on left, menu and cart on right.
- Smooth animations and hover effects.
- Mobile-friendly responsive design.
- Enhanced cart icon with notification badge.
- Dropdown menu animations.
- Customizable hero section (title, text, and image) via Customizer.
- Custom color scheme using WooCommerce purple palette.

![Theme Demo](.github/demo.gif)

## Installation

1. Upload the `storefront-child` folder to the `/wp-content/themes/` directory
2. Activate the theme through the 'Themes' menu in WordPress
3. Configure your site logo and menus as usual

## Customization

This theme includes custom CSS variables for easy customization:

```css
:root {
  /* WooCommerce Purple Palette */
  --woo-purple-10: #f6f2ff;
  --woo-purple-20: #e9ddff;
  --woo-purple-30: #dcc9ff;
  --woo-purple-40: #ceb4ff;
  --woo-purple-50: #a36bf7;
  --woo-purple-60: #7f54b3;
  --woo-purple-70: #533582;
  --woo-purple-80: #3c1f60;
  --woo-purple-90: #271041;

  /* Grayscale */
  --woo-white: #ffffff;
  --woo-gray-10: #f7f7f7;
  --woo-gray-20: #e5e5e5;
  /* ... and more variables */
}
```

You can modify these variables in the `main.css` file to change the color scheme.

## Structure

- `style.css` - Theme information
- `functions.php` - Theme functions and customizations
- `assets/css/main.css` - Main theme stylesheet
- `assets/css/header.css` - Header-specific styles
- `assets/js/navigation.js` - JavaScript for mobile menu and animations

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS and Android)

## License

This theme is licensed under the MIT License.

```
Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

## Credits

- Based on Storefront by WooCommerce
- Uses Inter font family from Google Fonts
