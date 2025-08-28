<?php

namespace App\Events;

use App\Models\User;
use App\Models\Level;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EnrollmentCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;



    public $user;
    public $level;

    public function __construct(User $user, Level $level)
    {
        $this->user = $user;
        $this->level = $level;
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('levels'); // يمكن تعديل اسم القناة حسب رغبتك
    }

    public function broadcastAs()
    {
        return 'level.enrollment.created';
    }
}
