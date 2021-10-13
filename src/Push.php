<?php

namespace Overthink\Push;

use Illuminate\Support\Collection;
use Overthink\Push\Contracts\DeviceContract;

class Push
{
    protected $devices = [];
    protected $payload;

    public function setDevice(DeviceContract $device): Push
    {
        array_push($this->devices, $device);

        return $this;
    }

    public function setDevices(Collection $devices): Push
    {
        $devices->each(function($device) {
            $this->setDevice($device);
        });

        return $this;
    }

    public function setPushPayload(PushPayload $payload): Push
    {
        $this->payload = $payload;

        return $this;
    }

    public function send()
    {
        collect($this->devices)->each(function($device) {
            if ($device->getOperatingSystem() == 'android') {
                $this->sendAndroidPush($device);
            } else {
                $this->sendIosPush($device);
            }
        });
    }

    protected function sendIosPush($device)
    {
        (new IosPush)
            ->setSandboxMode($device->isDevelopment())
            ->setTopic($device->getTopic())
            ->send($device->getPushToken(), $this->payload->getIosPayload());
    }

    protected function sendAndroidPush($device)
    {
        (new AndroidPush)
            ->send($device->getPushToken(), $this->payload->getAndroidPayload());
    }
}
