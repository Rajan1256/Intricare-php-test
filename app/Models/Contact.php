<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'gender',
        'profile_image',
        'additional_file',
        'is_active'
    ];


    public function getCustomFieldsAttribute()
    {
        /*return $this->customFields()->with('field')->get()->mapWithKeys(function ($item) {
            return [$item->field->field_name => $item->field_value];
        });*/

        return $this->customFields()->with('field')->get();
    }

    public function customFields()
    {
        return $this->hasMany(ContactCustomField::class)->with('field');
    }

    public function mergedAsMaster()
    {
        return $this->hasMany(MergedContact::class, 'master_contact_id');
    }

    public function mergedAsSecondary()
    {
        return $this->hasMany(MergedContact::class, 'merged_contact_id');
    }
}
