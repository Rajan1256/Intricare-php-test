<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactCustomField extends Model
{
    use HasFactory;

    protected $fillable = ['contact_id', 'custom_field_id', 'field_value'];

    public function field()
    {
        return $this->belongsTo(CustomField::class, 'custom_field_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
