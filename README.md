# price-bundle
Price Type Extension for Symfony 4
1. installation
    ```
    composer require kematjaya/price-bundle
    ```

2. add to bundles.php
    ```
    Kematjaya\PriceBundle\PriceBundle::class => ["all" => true]
    ```
3. add in twig.yaml
    ```
    twig:
    form_themes: 
        ....
        - '@Price/bootstrap_5_price_layout.html.twig' ## for bootstrap 5
        - '@Price/bootstrap_4_price_layout.html.twig' ## for bootstrap 4
        - '@Price/bootstrap_3_price_layout.html.twig' ## for bootstrap 3
        ....
    ```
4. Usage
    3.1. Form
    ```
    ...
    use Kematjaya\PriceBundle\Type\PriceType;
    ...

    $builder->add("price", PriceType::class);
    ...
    ```
    3.2. Twig
    - terbilang
    ```
    {{ terbilang(1000) }} // seribu
    {{ terbilang(40000) }} // empat puluh ribu
    ```
    - price format
    ```
    {{ price(1000) }} // IDR 1,000
    {{ price(1000.22) }} // IDR 1,000.22
    {{ price(4500) }} // IDR 4,500
    ```
