<?php

namespace Overthink\Push;

class PushPayload
{
    protected $iosPayload     = [];
    protected $androidPayload = [];

    public function setAndroidPayload($payload): PushPayload
    {
        $this->androidPayload = array_merge_recursive($this->androidPayload, $payload);

        return $this;
    }

    public function setIosPayload($payload): PushPayload
    {
        $this->iosPayload = array_merge_recursive($this->iosPayload, $payload);

        return $this;
    }

    public function addGeneralPayload($payload): PushPayload
    {
        $this->setAndroidPayload($payload);
        $this->setIosPayload($payload);

        return $this;
    }

    public function getAndroidPayload(): array
    {
        return $this->androidPayload;
    }

    public function getIosPayload(): array
    {
        return $this->iosPayload;
    }
}
