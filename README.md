# Derafu: Twig - UI Component and Extension Library

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://opensource.org/licenses/MIT)

**Derafu Twig** is a comprehensive UI component library for Twig, designed to work standalone or with any PHP framework. Built with Bootstrap 5 and Font Awesome 6, it provides a collection of reusable, customizable components for rapid web development.

## Features

- 🎨 **Rich Component Library**: Extensive collection of UI components including headers, footers, cards, grids, and more.
- 🎯 **Framework Agnostic**: Works with any PHP framework or standalone Twig.
- 🔧 **Highly Customizable**: Theme support with CSS variables for easy styling.
- 📱 **Responsive Design**: All components are mobile-first and fully responsive.
- 🧩 **Modular Architecture**: Components can be used independently or combined.
- 🎭 **Theme System**: Built-in theme support with easy customization.
- 📦 **Bootstrap 5 Integration**: Leverages Bootstrap's grid system and utilities.
- 🎟️ **Font Awesome 6**: Integrated icon support.
- 🏷️ **MIT License**: Open-source and free to use.

## Component Categories

### Block Components

- **Layout**: Header, Footer, Grid.
- **Content**: Cards, Features (Grid, List, Icon, Table), Text with Image/Video.
- **Navigation**: Tabs, Steps, Timeline.
- **Interaction**: Accordion, CTA (Call to Action).
- **Showcasing**: Team, Testimonials, Hero, Image.
- **Comparison**: Tables, Boxes, Comparison grids.

## Extensions

- **Markdown**: render markdown content with `markdown` filter.

## Installation

Install the library using Composer:

```bash
composer require derafu/twig
```

## Basic Usage

1. Create the Twig environment with `TwigService` or register the components with `ComponentRegistrar`:

```php
use Derafu\Twig\Service\TwigService;

$options = [
    'paths' => [
        __DIR__ . '/../templates',  // Path to the derafu-twig templates.
        __DIR__ . '/pages',         // Path to your templates.
    ],
];

$twigService = new TwigService($options);

echo $twigService->render('example.html.twig');
```

2. Use components in your Twig templates:

```twig
{# Example using a card grid component #}
<twig:block-card-grid
    :cols="3"
    :cards="[
        {
            'image': 'path/to/image.jpg',
            'title': 'Card Title',
            'description': 'Card description text',
            'button_text': 'Learn More',
            'button_url': '#'
        }
    ]"
/>
```

## Key Concepts

### Theme System
Components support theming through CSS variables:
```css
.derafu-theme-default {
    --color-primary: #0d6efd;
    --color-secondary: #6c757d;
    --color-tertiary: #198754;
}
```

### Component Structure

Each component follows a consistent structure:

- PHP Class: Defines component properties and logic.
- Twig Template: Handles component rendering.
- CSS: Component-specific styles.

## Documentation

Visit our [documentation site](https://derafu.org/twig) for:

- Live demos.
- Detailed component examples.
- List of themes and predefined colors.

## Roadmap

- 🔌 More component variations.
- 📱 Enhanced mobile optimizations.
- 🎨 Additional theme presets.
- 🌐 Internationalization features.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to discuss what you would like to change.

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

Happy coding! ✨
