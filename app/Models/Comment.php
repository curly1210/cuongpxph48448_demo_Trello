<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    public $fillable = ['content', 'ticket_id', 'user_id'];
    public function tickets()
    {
        return $this->belongsTo(Ticket::class);
    }
    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
