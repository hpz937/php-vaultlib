# php-vaultlib
PHP library for Vault API

### How to use:
```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

$vaultClient = new \Hpz937\Vault\Client;

$vaultSecret = $vaultClient->getSecret('secretName');
echo $vaultSecret->keyname . PHP_EOL;
