<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Edit_File extends Model
{
    use HasFactory;
    protected $table = 'edit_file';
    public $fillable = ['edit_date','reservation_id'];
    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id','id');
    }
}
