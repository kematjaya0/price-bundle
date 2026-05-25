# price-bundle

Price/currency form type, number formatting, Indonesian terbilang & date formatting.

Supports Symfony 7+ and PHP 8.1+.

## Installation

```bash
composer require kematjaya/price-bundle
```

## Bundle Registration

```php
// config/bundles.php
Kematjaya\PriceBundle\PriceBundle::class => ['all' => true]
```

## Configuration (optional)

```yaml
# config/packages/price.yaml
price:
  currency:
    code:           IDR
    cent_limit:     0
    cent_point:     '.'
    thousand_point: ','
    allow_negative: true
```

## Twig Form Theme

```yaml
# config/packages/twig.yaml
twig:
  form_themes:
    - '@Price/bootstrap_5_price_layout.html.twig'  # Bootstrap 5
    - '@Price/bootstrap_4_price_layout.html.twig'  # Bootstrap 4
    - '@Price/bootstrap_3_price_layout.html.twig'  # Bootstrap 3
```

## Form Usage

```php
use Kematjaya\PriceBundle\Type\PriceType;

$builder->add('price', PriceType::class);
```

## Twig Functions

```twig
{{ price(1000) }}        {# Rp1.000 #}
{{ price(1000.22) }}     {# Rp1.000,22 #}
{{ price_symbol() }}     {# Rp #}
{{ thousand_point() }}   {# . #}
{{ cent_point() }}       {# , #}
{{ cent_limit() }}       {# 0 #}
{{ allow_negative() }}   {# true #}
{{ 15000|terbilang }}    {# lima belas ribu #}
{{ date|date_format }}   {# Senin, 25 Mei 2026 #}
{{ date|date_to_string }}{# dua puluh lima Mei dua ribu dua puluh enam #}
```
