<?php

namespace Chipolo\Push\Events;

use Chipolo\Push\BasePush;
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
