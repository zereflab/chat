<?php

namespace Musonza\Chat\Eventing;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Musonza\Chat\Models\Message;

class MessageWasSent extends Event implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;
    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('mc-chat-conversation.'.$this->message->conversation_id);
    }

    public function broadcastWith()
    {
        
         $receiver_name = null;
        $receiver_dp = null;

        if($this->message->sender::class == 'App\Models\User'){
            $receiver_name = $this->message->sender->fullname();
            $receiver_dp = $this->message->sender->user_dp();
        }elseif($this->message->sender::class == 'App\Models\Admin'){
            $receiver_name = $this->message->sender->name;
            $receiver_dp = $this->message->sender->admin_dp();
        }
        
        return [
            'message' => [
                'id'              => $this->message->getKey(),
                'body'            => $this->message->body,
                'conversation_id' => $this->message->conversation_id,
                'type'            => $this->message->type,
                'created_at'      => $this->message->created_at,
                'sender'          => $this->message->sender,
                'receiver_name'   => $receiver_name,
                'receiver_dp'     => $receiver_dp
            ],
        ];
    }
}
