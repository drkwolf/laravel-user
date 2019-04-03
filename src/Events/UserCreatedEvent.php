<?php namespace drkwolf\Larauser\Events;

use drkwolf\Larauser\Entities\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserCreatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var User
     */
    public $User;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, $role)
    {
        $this->User = $user;
        $this->role = $role;
    }

    /**
     * broadcast to coaches, players, tutors,
     * TODO add admin, comity members (team type managers)
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $channels = ['User.'.$this->User->id => $this->User];

        return $channels;
    }

//    public function broadcastWhen() {
//        return $this->User->exists();
//    }

    public function broadcastWith()
    {
        return [ ];
    }
}
