<?php

namespace Chipolo\Push;

use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\ES256;
use Jose\Component\Core\Converter\StandardConverter;
use Jose\Component\Signature\Serializer\CompactSerializer;

class IosPush extends BasePush
{
    protected $sandbox = false;
    private $repeated  = 0;

    private function createToken(): string
    {
        $algorithmManager = AlgorithmManager::create([
            new ES256(),
        ]);

        $jwk = JWKFactory::createFromKeyFile(config('chipolo-push.ios.certificat-path'));

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
    ): CurlReponse {
        $response = $this->setUrl($this->getPath($token))
            ->setPayload($payload)
            ->setHeaders([
            'Content-Type'     => 'application/json',
            'Apns-Expiration:' => 0,
            'Apns-Topic'       => $topic,
            'Authorization'    => 'Bearer ' . $this->createToken(),
        ])->handle();

        if ($response->getStatusCode() != 200 && $this->repeated < 3) {
            $this->repeated++;
            $this->send($token, $payload, $topic);
        }

        $this->repeated = 0;
        return $response;
    }

    public function send1($deviceToken, $authToken, $payload)
    {
        $path = '/3/device/' . $deviceToken;
        $curl = curl_init();
        curl_setopt_array($curl, [
                CURLOPT_URL            => 'https://api.push.apple.com:443' . $path,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_2_0,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'apns-expiration: 0',
                    'apns-topic: com.nollieapps.Chipolo',
                    'authorization: bearer ' . $authToken,
                ],
            ]);
        $response = curl_exec($curl);
        $err      = curl_error($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($err) {
            echo 'cURL Error #:' . $err;
        } else {
            echo $response;
        }
        echo $httpcode;
    }
}
