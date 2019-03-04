<?php
namespace Chipolo\Push;

class PushPayload
{
    protected $iosPayload     = [];
    protected $androidPayload = [];

    public function setAndroidPayload($payload)
    {
        $this->androidPayload = array_merge_recursive($this->androidPayload, $payload);

        return $this;
    }

    public function setIosPayload($payload)
    {
        $this->iosPayload = array_merge_recursive($this->iosPayload, $payload);

        return $this;
    }

    public function addGeneralPayload($payload)
    {
        $this->addAndroidPayload($payload);
        $this->addIosPayload($payload);

        return $this;
    }

    public function getAndroidPayload()
    {
        return $this->androidPayload;
    }

    public function getIosPayload()
    {
        return $this->iosPayload;
    }
}
