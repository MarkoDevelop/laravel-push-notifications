<?php

namespace Overthink\Push\Events;

use Overthink\Push\BasePush;
use Illuminate\Queue\SerializesModels;

class BeforeSendingPush
{
    use SerializesModels;

    public $push;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(BasePush $push)
    {
        $this->push = $push;
    }
}
