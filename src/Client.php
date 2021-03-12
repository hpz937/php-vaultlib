<?php

namespace Hpz937\Vault;

use Exception;

Class Client
{
    private $client;

    public function __construct($vaultAddr,$vaultToken,$vaultTimeout = 2.0)
    {
        $this->client = $this->getClient($vaultAddr, $vaultToken, $vaultTimeout);
        $this->isTokenValid();
        $this->getStatus();
    }

    private function isTokenValid()
    {
        $response = $this->getRequest('GET','/v1/auth/token/lookup-self');
        if(!$response) {
            die("Vault Token Invalid". PHP_EOL);
        }
        return true;
    }

    private function getStatus()
    {
        $response = $this->getRequest('GET','/v1/sys/seal-status');
        if($response->sealed) {
            die("Vault Sealed");
        }
    }

    public function getSecret($key) {
        $response = $this->getRequest('GET','/v1/secret/data/'.$key);
        return $response->data->data;
    }

    private function getRequest($method,$path)
    {
        try {
            return json_decode($this->client->request($method,$path)->getBody());
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if($e->getCode() == 404) {
                die('Key Not Found ' . $key . PHP_EOL);
            }
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            die("Vault Server Not Found" . $e->getMessage() . PHP_EOL);
        }
    }

    private function getClient($vaultAddr, $vaultToken, $vaultTimeout)
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => $vaultAddr,
            'timeout'  => $vaultTimeout,
            'headers' => [
                'X-Vault-Token' => $vaultToken,
                'Accept' => 'application/json',
            ]
        ]);
        return $client;
    }
}
