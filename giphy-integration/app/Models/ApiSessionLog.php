<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int user_id
 * @property string service
 * @property string request_body
 * @property string http_status
 * @property string response
 * @property string origin_ip
 */
class ApiSessionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service',
        'request_body',
        'http_status',
        'response',
        'origin_ip',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
