[![Build Status](https://travis-ci.COM/alceanicu/php-password-generator.svg?branch=master)](https://travis-ci.org/alceanicu/php-password-generator) [![Latest Stable Version](https://poser.pugx.org/alcea/php-password-generator/v/stable.svg)](https://packagist.org/packages/alcea/php-password-generator) [![Total Downloads](https://poser.pugx.org/alcea/php-password-generator/downloads.svg)](https://packagist.org/packages/alcea/php-password-generator) [![License](https://poser.pugx.org/alcea/php-password-generator/license.svg)](https://packagist.org/packages/alcea/php-password-generator)

# PhpPasswordGenerator
Simple PHP Password Generator.

## How to install?

### 1. use composer
```php
composer require alcea/php-password-generator
```

### 2. or, edit require section from composer.json and run composer update
```
"alcea/php-password-generator": "^1.0"
```

## How to use?

```php
<?php

use alcea\generator\PhpPasswordGenerator;

// require __DIR__ . '\vendor\autoload.php';

$passwordObj = new PhpPasswordGenerator();
    
# password that contains:
# between 4 and 7 uppercase letter,
# between 4 and 7 lowercase letter,
# between 1 and 5 numbers,
# between 1 and 5 special char,
echo $passwordObj->generate();

# password that contains 2 uppercase letter, 2 lowercase letter, 2 numbers and 1 special char in a random order
echo $passwordObj->generate(2, 2, 2, 1);

# password that contains 4 numbers
echo $passwordObj->generate(false, false, 4, false);
```

## How to run tests?
```
## Open an terminal and run commands:
git clone https://github.com/alceanicu/php-password-generator.git
cd php-password-generator
composer install
./vendor/bin/phpunit --bootstrap ./vendor/autoload.php --testdox
```

## License

This package is licensed under the [MIT](http://opensource.org/licenses/MIT) license.
