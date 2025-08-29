
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
  resource: '../../vendor/vdub-dev/csv-preview-convert/Resources/config/routing/CsvPreviewConvert.php'
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
    <link rel="stylesheet" href="{{ asset('bundles/csvpreviewconvert/css/csv-preview-convert.css') }}">
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
    {% include '@CsvPreviewConvert/components/csv_preview_button.html.twig' %}
{% endblock %}
```

---

## 🛡️ Security configuration

To display the CSV preview inside an iframe, you have two options:

### Option 1 — NelmioSecurityBundle Configuration (Recommended)

```yaml
nelmio_security:
  clickjacking:
    paths:
      '^/csv-preview-content': ALLOW

  csp:
    enforce:
      frame-ancestors:
        - 'self'
      frame-src:
        - 'self'
```

**Explanation:**

- `frame-ancestors 'self'` → allows only your site to embed the page in an iframe.
- `frame-src 'self'` → allows iframes on your site to load resources from your own domain.
- `clickjacking.paths` → disables clickjacking protection for this route.

---

### Option 2 — Parent Page Headers (Alternative)

If you cannot use NelmioSecurityBundle, you can set CSP headers on the **parent page that contains the popup and iframe**:

```php
$response = $this->render('parent_page_with_popup.html.twig', $data);

$response->headers->set(
    'Content-Security-Policy',
    "default-src 'self'; frame-src 'self'; frame-ancestors 'self';"
);

return $response;
```

**Important Notes:**

- This only works when you control the parent page; you cannot modify the vendor controller.
- Using `$response->headers->set()` **overwrites any existing header of the same type**.
    - If another CSP is already set, it will be replaced.
    - To merge headers, you must retrieve existing values and append manually.

---

