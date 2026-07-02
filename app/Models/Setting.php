<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'address',
        'email',
        'hotline',
        'social',
        'logo',
        'footer_logo',
        'favicon',
        'footer_column_1_title',
        'footer_column_1_content',
        'footer_column_2_title',
        'footer_column_2_content',
        'footer_column_3_title',
        'footer_column_3_content',
        'footer_column_4_title',
        'footer_column_4_content',
    ];

    protected $casts = [
        'social' => 'array',
    ];
}
