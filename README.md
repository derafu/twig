# Derafu: Twig - UI Component and Extension Library

![GitHub last commit](https://img.shields.io/github/last-commit/derafu/twig/main)
![CI Workflow](https://github.com/derafu/twig/actions/workflows/ci.yml/badge.svg?branch=main&event=push)
![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/derafu/twig)
![GitHub Issues](https://img.shields.io/github/issues-raw/derafu/twig)
![Total Downloads](https://poser.pugx.org/derafu/twig/downloads)
![Monthly Downloads](https://poser.pugx.org/derafu/twig/d/monthly)

**Derafu Twig** is a comprehensive UI component library for Twig, designed to work standalone or with any PHP framework. Built with Bootstrap 5 and Font Awesome 6, it provides a collection of reusable, customizable components for rapid web development.

## Features

- 🎨 **Rich Component Library**: Extensive collection of UI components including headers, footers, cards, grids, and more.
- 🎯 **Framework Agnostic**: Works with any PHP framework or standalone Twig.
- 🔧 **Highly Customizable**: Support for Bootstrap CSS variables for easy styling.
- 📱 **Responsive Design**: All components are mobile-first and fully responsive.
- 🧩 **Modular Architecture**: Components can be used independently or combined.
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
            image: 'path/to/image.jpg',
            title: 'Card Title',
            content: 'Card content text',
            buttonText: 'Learn More',
            buttonUrl: '#'
        }
    ]"
/>
```

## Key Concepts

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
