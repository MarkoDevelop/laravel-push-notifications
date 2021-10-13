<?php

namespace Overthink\Push;

class CurlResponse
{
    protected $curl;
    protected $response;
    protected $error;
    protected $statusCode;

    public function __construct($curl, $response)
    {
        $this->curl = $curl;

        $this->setResponse($response)
            ->setError()
            ->setStatusCode();
    }

    /**
     * Set basic response
     *
     * @param string $response
     * @return CurlResponse
     */
    public function setResponse(string $response): CurlResponse
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Set curl error
     *
     * @return CurlResponse
     */
    public function setError(): CurlResponse
    {
        $this->error = curl_error($this->curl);

        return $this;
    }

    /**
     * Set status code
     *
     * @return CurlResponse
     */
    public function setStatusCode(): CurlResponse
    {
        $this->statusCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

        return $this;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getJsonResponse($assoc = false)
    {
        return json_decode($this->response, $assoc);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getError()
    {
        return $this->error;
    }

    public function __toString()
    {
        return $this->getResponse();
    }
}
