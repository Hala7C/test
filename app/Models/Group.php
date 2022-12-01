<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    public $fillable = ['name'];
    public function users()
    {
        return $this->belongsToMany(User::class, 'members', 'group_id', 'user_id', 'id', 'id');
    }
    public function documents()
    {
        return $this->hasMany(Document::class, 'group_id', 'id');
    }
}
