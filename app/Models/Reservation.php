<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    protected $table = 'reservations';
    public $fillable = ['user_id','document_id','date'];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function document(){
        return $this->belongsTo(Document::class,'document_id','id');
    }

    public function edits(){
        return $this->hasMany(Edit_File::class,'reservation_id','id');
    }
    public function Orderededits(){
        return $this->hasMany(Edit_File::class,'reservation_id','id')->orderBy('created_at', 'desc')->get();
    }
}
