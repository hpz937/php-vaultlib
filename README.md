# php-vaultlib
PHP library for Vault API

### How to use:
```php
<?php
require_once __DIR__.'/vendor/autoload.php';

$test = new \Hpz937\Vault\Client;

$secret = $test->getSecret('test1');
echo $secret->keyname . PHP_EOL;
