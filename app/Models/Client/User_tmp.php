<?php

namespace App\Models\Client;

use App\Models\Client\Abstracts\AbstractUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_tmp extends AbstractUser
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'code'
    ];
}
