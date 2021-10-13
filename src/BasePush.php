<?php

namespace Overthink\Push;

use Overthink\Push\Events\AfterSendingPush;
use Overthink\Push\Events\BeforeSendingPush;

abstract class BasePush
{
    protected $url;
    protected $payload;
    protected $headers;
    protected $token;
    protected $curl;

    /**
     * Set url for request
     *
     * @param  string   $url
     * @return BasePush
     */
    public function setUrl(string $url): BasePush
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set token that was created for currents user
     * request
     *
     * @param string $token The token
     *
     * @return self
     */
    public function setToken($token): BasePush
    {
        $this->token = $token;

        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set post fields for request
     *
     * @param  array    $payload
     * @return BasePush
     */
    public function setPayload(array $payload): BasePush
    {
        $this->payload = $payload;

        return $this;
    }

    public function getPayload()
    {
        return json_encode($this->payload);
    }

    /**
     * Set headers in key => value form
     *
     * @param  array    $headers
     * @return BasePush
     */
    public function setHeaders(array $headers): BasePush
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Get headers in a "curl" way
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return array_map(function ($value, $header) {
            return $header . ': ' . $value;
        }, $this->headers, array_keys($this->headers));
    }

    public function handle(): CurlResponse
    {
        return backoff(function () {
            event(new BeforeSendingPush($this));

            $curl    = curl_init();
            $options = [
                CURLOPT_URL            => $this->getUrl(),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_2_0,
                CURLOPT_TIMEOUT        => 60,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS     => $this->getPayload(),
                CURLOPT_HTTPHEADER     => $this->getHeaders(),
            ];

            if (config('overthink-push.general.keep-alive', false)) {
                $options = array_merge($options, [
                    CURLOPT_TCP_KEEPALIVE => '1L',
                ]);
            }

            curl_setopt_array($curl, $options);

            $response   = curl_exec($curl);
            $this->curl = new CurlResponse($curl, $response);
            if (! config('overthink-push.general.keep-alive', false)) {
                curl_close($curl);
            }

            event(new AfterSendingPush($this->curl, $this));

            return $this->curl;
        }, 3);
    }

    public function convertToCurlCommand($extra = null): string
    {
        $data = [
            'curl -d',
            '\'' . str_replace('\\"', '"', trim(json_encode($this->getPayload()), '"')) . '\'',
            '-H',
            '"' . implode(';', $this->getHeaders()) . '"',
            '-X POST',
            '--http2',
            $this->getUrl(),
        ];

        if ($extra) {
            array_push($data, $extra);
        }

        return implode(' ', $data);
    }

    public function toArray(): array
    {
        return [
            'headers'    => $this->getHeaders(),
            'url'        => $this->getUrl(),
            'payload'    => $this->getPayload(),
        ];
    }

    public function dd()
    {
        dd($this->toArray());
    }

    public function __toString()
    {
        return json_encode($this->toArray());
    }

    abstract public function send(
        string $token,
        array $payload
    ): CurlResponse;
}
