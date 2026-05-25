# kematjaya/price-bundle

Symfony bundle: price/currency form type, number formatting, Indonesian number-to-words (terbilang), Indonesian date formatting.

## PSR

- **PSR-4**: `Kematjaya\PriceBundle\` → `src/`, `Kematjaya\PriceBundle\Tests\` → `tests/`
- **PSR-12**: extended coding style (Symfony conventions)

## Stack

- Symfony **7.0|8.0** (http-kernel, dependency-injection, config, form, intl, translation, event-dispatcher, yaml)
- PHP **8.1+**, Twig **3.14|4.0**, PHPUnit **9.5|11.0**
- jQuery Price Format Plugin v2.2.0 (bundled, client-side input formatting)
- No static analysis, no CS fixer, no CI

## Dirs

```
src/
  PriceBundle.php                              — Bundle registration
  Contract/Messages.php                        — Deprecation helper
  Converter/
    ConverterInterface.php                     — float → words contract
    IndonesianConverter.php                    — Indonesian terbilang (trillions)
  DataTransformer/
    PriceDataTransformer.php                   — float ↔ formatted string transformer
  DependencyInjection/
    PriceExtension.php                         — DI extension, loads services.yaml
    Configuration.php                          — Config tree: currency.code, cent_limit, cent_point, thousand_point, allow_negative
  EventSubscriber/Form/
    PriceEventSubscriber.php                   — PRE_SUBMIT: formatted string → float
  Lib/
    AbstractDateFormat.php                     — Base date formatting with localized day/month + number spelling
    IndonesianDateFormat.php                   — Indonesian day/month names, reverse parse
    DateFormatInterface.php                    — Marker interface
    CurrencyFormatInterface.php                — Price parse/format contract
    CurrencyFormat.php                         — symfony/intl-backed currency formatter
  Resources/
    config/services.yaml                       — Service definitions
    public/js/jquery.priceFormat.min.js        — jQuery Price Format (bundled)
    public/js/jquery-price-format/             — Full source + docs
    views/
      bootstrap_3_price_layout.html.twig
      bootstrap_4_price_layout.html.twig
      bootstrap_5_price_layout.html.twig
      javascripts.twig
  Twig/
    PriceExtension.php                         — Functions: currency, price, price_symbol, thousand_point, cent_point, cent_limit, allow_negative
    ConverterExtension.php                     — Filter: terbilang
    DateFormatExtension.php                    — Filters: date_format, date_to_string
  Type/
    PriceType.php                              — Form type (extends MoneyType), configurable scale/separators
```

## Config

```yaml
price:
  currency:
    code:           IDR      # default
    cent_limit:     0        # decimal digits
    cent_point:     '.'      # decimal separator
    thousand_point: ','      # thousands separator
    allow_negative: true
```

Services autowired, autoconfigured. Twig extensions tagged automatically.

## Twig Usage

```twig
{{ price(12345.67) }}              → Rp12.346
{{ price_symbol() }}               → Rp
{{ thousand_point() }}             → ,
{{ cent_point() }}                 → .
{{ cent_limit() }}                 → 0
{{ allow_negative() }}             → true
{{ 15000|terbilang }}              → lima belas ribu
{{ date|date_format }}             → Senin, 25 Mei 2026
{{ date|date_to_string }}          → dua puluh lima Mei dua ribu dua puluh enam
```

## Tests

```bash
composer test       → phpunit (PHPUnit 9.5|11.0)
```

- `IndonesianDateFormatTest` — unit test (PHPUnit\Framework\TestCase)
- `PriceTypeTest` — form test (TypeTestCase + PreloadedExtension)
- `CurrencyFormatTest` — integration test (WebTestCase, custom AppKernelTest)
- `PriceExtensionTest` — integration test (WebTestCase, custom AppKernelTest)

## Caveats

- Symfony 7 & 8 only (^7.0|^8.0), no 6.x compat
- PHPUnit 9.5 (no 10/11 upgrade)
- No phpstan / php-cs-fixer config
- No CI pipeline
- `phpunit.xml.dist` references outdated schema 4.1
- `PriceBundle.php` has IDE template boilerplate comments
