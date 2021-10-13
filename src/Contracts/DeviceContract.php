<?php

namespace Overthink\Push\Contracts;

interface DeviceContract
{
    /**
     * Return either "android" or "ios"
     *
     * @return string
     */
    public function getOperatingSystem(): string;

    /**
     * Return push token for this device
     *
     * @return string
     */
    public function getPushToken(): string;

    /**
     * Is this push intended for production or development,
     * "sandbox" mode, iOS only
     *
     * @return boolean
     */
    public function isDevelopment(): bool;

    /**
     * Get apns topic for push, iOS only
     *
     * @return string
     */
    public function getTopic(): string;
}
