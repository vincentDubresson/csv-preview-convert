
# 🛡️ vdub-dev/csv-preview-convert

A Symfony 6.4+ bundle that allows you to **upload** CSV files, **preview** from the most popular encodings, and **convert** to UTF-8.

---

## 🚀 Installation

Add the following lines to your `composer.json`:

```json
{
  "repositories": [
    {
      "type": "git",
      "url": "https://github.com/vincentDubresson/csv-preview-convert.git"
    }
  ],
  "require": {
    "vdub-dev/csv-preview-convert": "^1.0"
  }
}
```

Then run:

```bash
composer update vdub-dev/csv-preview-convert:^1.0
```

Create a file `config/routes/csv_preview_convert.yaml` :

```yaml
csv_preview_convert:
  resource: '../../vendor/vdub-dev/csv-preview-convert/Resources/config/routing/csvPreviewConvert.php'
  type: php
```

then run:

```bash
php bin/console c:cl
```

---

## 📂 Assets

Assets should be installed automatically in:

```
public/bundles/csvpreviewconvert
```

### If not, do the following:

1. Make sure the bundle is enabled in `config/bundles.php`:

```php
<?php

return [
    VdubDev\CaptchaHandler\CsvPreviewConvert::class => ['all' => true],
];
```

2. Re-install the assets manually:

```bash
php bin/console assets:install
```

---

## 🎨 Frontend integration (CSS & JS)

The package provides ready-to-use **CSS** and **JavaScript** assets.

Reference the bundle's files directly in your `app.js`.  
Example:

```js
// assets/app.js
import '/bundles/csvpreviewconvert/js/csv-preview-convert.js';
```

Include the CSS assets directly in your Twig templates:

```twig
{# example.html.twig #}

{% extends 'base.html.twig' %}

{% block stylesheets %}
    {{ parent() }}
    <link rel="stylesheet" href="{{ asset('bundles/csvpreviewconvert/css/csvpreviewconvert.css') }}">
{% endblock %}

{% block javascripts %}
    {{ parent() }}
{% endblock %}
```

This will load the assets directly from the `public/` directory.

---

## 🔑 Backend Integration

Add dedicated button in the view that will render the csv preview convert popup:

```twig
{% block body %}
    {% include '@csvpreviewconvert/components/csv_preview_button.html.twig' %}
{% endblock %}
```