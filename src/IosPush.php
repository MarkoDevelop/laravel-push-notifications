<?php

namespace Chipolo\Push;

use Exception;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\ES256;
use Jose\Component\Core\Converter\StandardConverter;
use Jose\Component\Signature\Serializer\CompactSerializer;

class IosPush extends BasePush
{
    protected $sandbox = false;
    protected $topic;
    private $repeated  = 0;

    private function createToken(): string
    {
        $algorithmManager = AlgorithmManager::create([
            new ES256(),
        ]);

        $jwk           = JWKFactory::createFromKeyFile(config('chipolo-push.ios.certificate-path'));
        $jsonConverter = new StandardConverter();
        $jwsBuilder    = new JWSBuilder($jsonConverter, $algorithmManager);

        $payload = $jsonConverter->encode([
            'iat' => time(),
            'iss' => config('chipolo-push.ios.team-id'),
        ]);

        $jws = $jwsBuilder
            ->create()
            ->withPayload($payload)
            ->addSignature($jwk, [
                'alg' => 'ES256',
                'kid' => config('chipolo-push.ios.secret'),
            ])
            ->build();

        $serializer = new CompactSerializer($jsonConverter);

        $token = $serializer->serialize($jws);

        return $token;
    }

    public function setTopic(bool $topic)
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

    public function setSandboxMode(bool $sandbox)
    {
        $this->sandbox = $sandbox;

        return $this;
    }

    private function getPath($token)
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
        array $payload,
        string $topic
    ): CurlResponse {
        $response = $this->setUrl($this->getPath($token))
            ->setPayload($payload)
            ->setHeaders([
                'Content-Type'     => 'application/json',
                'Apns-Expiration'  => 0,
                'Apns-Topic'       => $this->getTopic(),
                'Authorization'    => 'Bearer ' . $this->createToken(),
        ])->handle();

        if ($response->getStatusCode() != 200 && $this->repeated < 3) {
            $this->repeated++;
            $this->send($token, $payload, $topic);
        }

        $this->repeated = 0;
        return $response;
    }
}
