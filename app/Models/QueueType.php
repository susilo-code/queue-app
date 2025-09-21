<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QueueType extends Model
{
    //
     use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'color',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    public function todayQueues()
    {
        return $this->hasMany(Queue::class)->whereDate('created_at', today());
    }

    public function waitingQueues()
    {
        return $this->todayQueues()->where('status', 'waiting');
    }
}
