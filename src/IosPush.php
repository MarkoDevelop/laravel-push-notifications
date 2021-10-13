<?php

namespace Overthink\Push;

use Exception;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\ES512;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\CompactSerializer;

class IosPush extends BasePush
{
    protected $sandbox = false;
    protected $topic;

    private function createToken(): string
    {
        $algorithmManager = new AlgorithmManager([
            new ES512(),
        ]);

        $jwsBuilder = new JWSBuilder($algorithmManager);
        $payload = json_encode([
            'iss' => config('overthink-push.ios.team-id'),
            'iat' => time(),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $privateECKey =  JWKFactory::createFromKeyFile(config('overthink-push.ios.certificate-path'), null, [
            'kid' => config('overthink-push.ios.secret'),
            'alg' => 'ES512',
            'use' => 'sig'
        ]);

        $jws = $jwsBuilder
            ->create()
            ->withPayload($payload)
            ->addSignature($privateECKey, [
                'alg' => 'ES512',
                'kid' => $privateECKey->get('kid'),
            ])
            ->build();

        $serializer = new CompactSerializer();

        return $serializer->serialize($jws);
    }

    public function setTopic(string $topic)
    {
        $this->topic = $topic;

        return $this;
    }

    private function getTopic()
    {
        if (! is_null($this->topic)) {
            return $this->topic;
        }

        throw new Exception('iOS topic must be set!');
    }

    public function setSandboxMode(bool $sandbox): IosPush
    {
        $this->sandbox = $sandbox;

        return $this;
    }

    private function getPath($token): string
    {
        if ($this->sandbox) {
            $base = 'https://api.development.push.apple.com:443';
        } else {
            $base = 'https://api.push.apple.com:443';
        }

        return implode('/', [
            $base,
            '3/device',
            $token,
        ]);
    }

    public function send(
        string $token,
        array $payload
    ): CurlResponse {
        $this->setToken($token);

        return $this->setUrl($this->getPath($token))
            ->setPayload($payload)
            ->setHeaders([
                'Content-Type'     => 'application/json',
                'Apns-Expiration'  => 0,
                'Apns-Topic'       => $this->getTopic(),
                'Authorization'    => 'Bearer ' . $this->createToken(),
        ])->handle();
    }
}
