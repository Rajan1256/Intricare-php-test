<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    use HasFactory;

    protected $fillable = ['field_name', 'field_type', 'is_required'];

    public function contactFields()
    {
        return $this->hasMany(ContactCustomField::class);
    }
}
