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
        return $this->belongsToMany(User::class, 'members', 'group_id', 'user_id', 'id', 'id')->withTimestamps();
    }
    public function documents()
    {
        return $this->belongsToMany(Document::class, 'document_group', 'group_id', 'document_id', 'id', 'id')->withTimestamps();
    }
}
