<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'type',
        'content',
        'data',
        'generated_by',
        'file_path',
        'report_date',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'report_date' => 'date',
        ];
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
