<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $fillable = ['name'];
    protected $table = 'groups';
    public function users()
    {
        return $this->belongsToMany(User::class, 'members', 'group_id', 'user_id', 'id', 'id')
            ->using(Member::class)
            ->as('members')
            ->withPivot(['group_id', 'user_id', 'group_role'])
            ->withTimestamps();
    }
    public function documents()
    {
        return $this->belongsToMany(Document::class, 'document_group', 'group_id', 'document_id', 'id', 'id')->withTimestamps();
    }
}
