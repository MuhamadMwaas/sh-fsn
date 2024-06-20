<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'code', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function codeRecords()
    {
        return $this->hasMany(Coderecord::class);
    }
    public function user()
    {
        return $this->hasMany(User::class);
    }

    public function client()
    {
        if ($this->codeRecords->first()) {
            return $this->codeRecords->first()->user;
        }
    }
    public function purchaseDate()
    {
        if ($this->codeRecords->first()) {
            return $this->codeRecords->first()->created_at;
        }
    }
    public function isRecorded()
    {
        // قم بالتحقق من وجود الكود في جدول سجل الأكواد
        return $this->codeRecords()->exists();
    }
}
