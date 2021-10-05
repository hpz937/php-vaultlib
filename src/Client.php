<?php

namespace Hpz937\Vault;

use Exception;

Class Client
{
    protected $client;
    protected $token;
    const VAULT_AUTH_TOKEN = 0;
    const VAULT_AUTH_USER = 1;

    public function __construct($vaultAddr,$vaultAuth,$vaultTimeout = 10.0,$authMethod = self::VAULT_AUTH_TOKEN)
    {
        $this->client = $this->getClient($vaultAddr, $vaultTimeout);
        if($authMethod == self::VAULT_AUTH_TOKEN)
        {
            $this->token = $vaultAuth;
        }
        elseif($authMethod == self::VAULT_AUTH_USER)
        {
            $response = $this->client->post(
                '/v1/auth/userpass/login/'.$vaultAuth[0],
                [
                    'body' => json_encode(array('password' => $vaultAuth[1]))
                ]
            );
            $body = json_decode($response->getBody());
            $this->token = $body->auth->client_token;
        }
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
            return json_decode($this->client->request($method,$path,['headers' => ['X-Vault-Token' => $this->token]])->getBody());
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if($e->getCode() == 404) {
                die('Key Not Found ' /* . $key */ . PHP_EOL);
            }
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            die("Vault Server Not Found" . $e->getMessage() . PHP_EOL);
        }
    }

    private function getClient($vaultAddr, $vaultTimeout)
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => $vaultAddr,
            'timeout'  => $vaultTimeout,
            'headers' => [
                'Accept' => 'application/json',
            ]
        ]);
        return $client;
    }
}