<?php

namespace Chipolo\Push\Events;

use Chipolo\Push\BasePush;
use Chipolo\Push\CurlResponse;
use Illuminate\Queue\SerializesModels;

class AfterSendingPush
{
    use SerializesModels;

    public $response;
    public $push;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(CurlResponse $response, BasePush $push)
    {
        $this->response = $response;
        $this->push     = $push;
    }
}
