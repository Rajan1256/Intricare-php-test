<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use Illuminate\Http\Request;

class CustomFieldController extends Controller
{
    public function index()
    {
        $fields = CustomField::all();
        return response()->json($fields);
    }

    public function store(Request $request)
    {
        $validated =  $request->validate([
            'field_name' => 'required|string|max:255|unique:custom_fields,field_name',
            'field_type' => 'required|string|in:text,number,date,checkbox',
        ]);
        $validated['is_required'] = (bool)($request->input('is_required', false));
        $field = CustomField::create($validated);

        return response()->json([
            'success' => true,
            'field' => $field
        ]);
    }

    public function update(Request $request, CustomField $customField)
    {
        $validated = $request->validate([
            'field_name' => 'required|string|max:255|unique:custom_fields,field_name,' . $customField->id,
            'field_type' => 'required|string|in:text,number,date,checkbox',
        ]);

        $validated['is_required'] = (bool)($request->input('is_required', false));

        $customField->update($validated);

        return response()->json([
            'success' => true,
            'field' => $customField
        ]);
    }

    public function show(CustomField $customField)
    {
        return $customField;
    }

    public function edit($id)
    {
        return CustomField::findOrFail($id);
    }


    public function destroy(CustomField $customField)
    {
        $customField->contactFields()->delete();
        $customField->delete();

        return response()->json(['success' => true]);
    }
}
