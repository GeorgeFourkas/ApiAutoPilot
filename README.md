
<p align="center">
  <img src="https://apiautopilot.info/assets/images/aap_logo%20_cropped.png">
</p>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/apiautopilot/apiautopilot.svg?style=flat-square)](https://packagist.org/packages/apiautopilot/apiautopilot)
[![Total Downloads](https://img.shields.io/packagist/dt/apiautopilot/apiautopilot.svg?style=flat-square)](https://packagist.org/packages/apiautopilot/apiautopilot)

### **Api Auto Pilot** is laravel package that makes the procecess of creating laravel RESTful APIs a breeze!

## Installation

You can install the package via composer:

```bash
composer require apiautopilot/apiautopilot
```

After you required the packager from packagist, make sure to run the installation command:

```bash
php artisan apiautopilot:install
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="apiautopilot-config"
```

This is the contents of the published config file:

```php
<?php

return [
    'index' => [
        'exclude' => [

        ],
    ],
    'show' => [
        'exclude' => [

        ],
    ],
    'create' => [
        'exclude' => [

        ],
    ],
    'update' => [
        'exclude' => [

        ],
    ],
    'delete' => [
        'exclude' => [

        ],
    ],
    'attach' => [
        'exclude' => [

        ],
    ],
    'detach' => [
        'exclude' => [

        ],
    ],
    'sync' => [
        'exclude' => [

        ],
    ],

    'settings' => [

    ],
];

```

## Testing

```bash
composer test
```


## Credits

- [GeorgeFourkas](https://github.com/GeorgeFourkas)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
