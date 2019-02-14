<?php

namespace Chipolo\Push;

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
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => $this->getUrl(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_2_0,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $this->getPayload(),
            CURLOPT_HTTPHEADER     => $this->getHeaders(),
        ]);

        $response   = curl_exec($curl);
        $this->curl = new CurlResponse($curl, $response);
        curl_close($curl);

        return $this->curl;
    }

    abstract public function send(
        string $token,
        array $payload
    ): CurlResponse;
}
