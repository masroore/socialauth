<?php

namespace Masroore\SocialAuth\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Masroore\SocialAuth\Models\SocialAccount;

class Login
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public SocialAccount $account)
    {
    }
}
