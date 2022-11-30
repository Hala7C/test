<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'path', 'status', 'user_id', 'group_id'];
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'document_id', 'id');
    }
    public function OrderdReservations()
    {
        return $this->hasMany(Reservation::class, 'document_id', 'id')->orderBy('created_at', 'desc')->get();
    }
    public function latestReservations()
{
    return $this->hasMany(Reservation::class, 'document_id', 'id')->latest('created_at')->first();
}

}
