# php-vaultlib
PHP library for Vault API

### How to use:
```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

$vaultClient = new \Hpz937\Vault\Client('http://localhost:8200','s.TokEnAuTh',10.0,\Hpz937\Vault\Client::VAULT_AUTH_TOKEN);
// OR
$vaultClient = new \Hpz937\Vault\Client('http://localhost:8200',array('vaultUser','vaultPassword'),10.0,\Hpz937\Vault\Client::VAULT_AUTH_USER);

$vaultSecret = $vaultClient->getSecret('secretName');
echo $vaultSecret->keyname . PHP_EOL;
