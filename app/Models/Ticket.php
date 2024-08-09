<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['name', 'position', 'lane_id'];
    public function lane()
    {
        return $this->belongsTo(Lane::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}