<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int user_id
 * @property string gif_id
 * @property string alias
 */
class FavoriteGif extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gif_id',
        'alias',
    ];

    public function user()
    {
        $this->belongsTo(User::class);
    }
}
