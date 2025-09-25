<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Queue extends Model
{
    use HasFactory;

    protected $fillable = [
        'queue_type_id',
        'queue_number',
        'customer_name',
        'customer_phone',
        'status',
        'called_at',
        'served_at',
    ];

    protected function casts(): array
    {
        return [
            'called_at' => 'datetime',
            'served_at' => 'datetime',
        ];
    }

    public function queueType()
    {
        return $this->belongsTo(QueueType::class);
    }

    public static function generateQueueNumber($queueTypeId)
    {
        $queueType = QueueType::find($queueTypeId);
        $todayCount = self::where('queue_type_id', $queueTypeId)
            ->whereDate('created_at', today())
            ->count();

        return $queueType->code . str_pad($todayCount + 1, 3, '0', STR_PAD_LEFT);
    }
}
