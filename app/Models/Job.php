<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    public $payloadArray;

    public function getDisplayNameAttribute()
    {
        return $this->getPayloadOfName('displayName');
    }

    public function getJobDataAttribute()
    {
        $data = $this->getPayloadOfName('data');
        $command = $data['command'];

        $all = unserialize($command);
        unset(
            $all->job,
            $all->connection,
            $all->queue,
            $all->chained,
            $all->chainConnection,
            $all->chainQueue,
            $all->chainCatchCallbacks,
            $all->delay,
            $all->afterCommit,
            $all->middleware,
            $all->before
        );

        $arrayData = (array) $all;

        return json_encode($arrayData);
    }

    protected function convertPayload(): array
    {
        if (!is_array($this->payloadArray)) {
            $this->payloadArray = json_decode($this->payload, true);
        }
        return $this->payloadArray;
    }

    protected function getPayloadOfName($name )
    {
        $this->convertPayload();
        return $this->payloadArray[$name] ?? null;
    }

}
