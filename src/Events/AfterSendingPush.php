<?php

namespace Chipolo\Push\Events;

use Illuminate\Queue\SerializesModels;
use Chipolo\Push\CurlResponse;

class AfterSendingPush
{
    use SerializesModels;

    public $response;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(CurlResponse $response)
    {
        $this->response = $response;
    }
}
