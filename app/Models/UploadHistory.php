<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadHistory extends Model
{
    use HasFactory;

    // Tambahkan ini agar bisa di-input via Controller
    protected $fillable = [
        'file_name',
        'type',
        'status',
        'message',
        'user_name'
    ];
}
