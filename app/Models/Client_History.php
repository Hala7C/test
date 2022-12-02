<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client_History extends Model
{
    use HasFactory;
    protected $table='client_history';
    protected $fillable = ['ip', 'status','method','uri','body','header','response'];

}
