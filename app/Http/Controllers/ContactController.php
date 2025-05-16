<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\CustomField;
use App\Models\ContactCustomField;
use App\Models\MergedContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::with(['customFields.field'])->where('is_active', true);
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->has('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->has('gender')) {
            if($request->gender!=""){
                $query->where('gender',  $request->gender);
            }
        }

        if ($request->ajax()) {
            $contacts = $query->get();


            if ($request->input('html', false)) {
                return view('contacts.partials.table', [
                    'contacts' => $contacts,
                    'customFields' => CustomField::all()
                ])->render();
            }


            return response()->json([
                'contacts' => $contacts
            ]);
        }


        $contacts = $query->paginate(10);
        $customFields = CustomField::all();

        return view('contacts.index', compact('contacts', 'customFields'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:male,female,other',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'additional_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'email', 'phone', 'gender']);

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('profile_images', 'public');
        }

        if ($request->hasFile('additional_file')) {
            $data['additional_file'] = $request->file('additional_file')->store('additional_files', 'public');
        }

        $contact = Contact::create($data);


        $customFields = CustomField::all();
        foreach ($customFields as $field) {
            if ($request->has($field->field_name)) {
                ContactCustomField::create([
                    'contact_id' => $contact->id,
                    'custom_field_id' => $field->id,
                    'field_value' => $request->input($field->field_name)
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Contact created successfully',
            'contact' => $contact->load('customFields.field')
        ]);
    }


    public function update(Request $request, Contact $contact)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email,' . $contact->id,
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:male,female,other',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'additional_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'email', 'phone', 'gender']);

        if ($request->hasFile('profile_image')) {
            if ($contact->profile_image) {
                Storage::disk('public')->delete($contact->profile_image);
            }
            $data['profile_image'] = $request->file('profile_image')->store('profile_images', 'public');
        }

        if ($request->hasFile('additional_file')) {
            if ($contact->additional_file) {
                Storage::disk('public')->delete($contact->additional_file);
            }
            $data['additional_file'] = $request->file('additional_file')->store('additional_files', 'public');
        }

        $contact->update($data);


        $customFields = CustomField::all();

        foreach ($customFields as $field) {
            $existingField = $contact->customFields()->where('custom_field_id', $field->id)->first();

            if ($request->has($field->field_name)) {
                if ($existingField) {
                    $existingField->update(['field_value' => $request->input($field->field_name)]);
                } else {
                    ContactCustomField::create([
                        'contact_id' => $contact->id,
                        'custom_field_id' => $field->id,
                        'field_value' => $request->input($field->field_name)
                    ]);
                }
            } elseif ($existingField) {
                $existingField->delete();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Contact updated successfully',
            'contact' => $contact->load('customFields.field')
        ]);
    }

    public function show(Contact $contact)
    {
        return response()->json([
            'contact' => $contact,
            'custom_fields' => ContactCustomField::where('contact_id', $contact->id)
                ->with('field')
                ->get(),
            'merged_users'=> MergedContact::where('master_contact_id', $contact->id)->with('mergedContact')->get()
        ]);
    }


    public function destroy(Contact $contact)
    {
        if ($contact->profile_image) {
            Storage::disk('public')->delete($contact->profile_image);
        }
        if ($contact->additional_file) {
            Storage::disk('public')->delete($contact->additional_file);
        }

        $contact->customFields()->delete();
        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact deleted successfully'
        ]);
    }

    public function showMergeForm(Contact $contact)
    {
        $contacts = Contact::where('id', '!=', $contact->id)
            ->where('is_active', true)
            ->get();

        return view('contacts.merge', compact('contact', 'contacts'));
    }

    public function merge(Request $request, Contact $contact)
    {
        $request->validate([
            'master_contact_id' => 'required|exists:contacts,id',
        ]);

        $contact->load(['customFields.field']);
        $masterContact = Contact::with(['customFields.field'])->findOrFail($request->master_contact_id);


        if (!$masterContact->phone && $contact->phone) {
            $masterContact->phone = $contact->phone;
        }
        if (!$masterContact->profile_image && $contact->profile_image) {
            $masterContact->profile_image = $contact->profile_image;
        }
        if (!$masterContact->additional_file && $contact->additional_file) {
            $masterContact->additional_file = $contact->additional_file;
        }
        $masterContact->save();

        $mergedData = [];

        foreach ($contact->custom_fields as $customField) {

            if (!$customField->field) {
                continue;
            }

            $existingField = $masterContact->customFields
                ->where('custom_field_id', $customField->custom_field_id)
                ->first();

            if (!$existingField) {
                ContactCustomField::create([
                    'contact_id' => $masterContact->id,
                    'custom_field_id' => $customField->custom_field_id,
                    'field_value' => $customField->field_value
                ]);
                $mergedData[$customField->field->field_name] = $customField->field_value;
            } else {
                if ($existingField->field_value != $customField->field_value) {
                    $mergedData[$customField->field->field_name] = [
                        'master_value' => $existingField->field_value,
                        'merged_value' => $customField->field_value
                    ];
                }
            }
        }

        $contact->update(['is_active' => false]);

        MergedContact::create([
            'master_contact_id' => $masterContact->id,
            'merged_contact_id' => $contact->id,
            'merged_data' => json_encode($mergedData)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contacts merged successfully',
            'master_contact' => $masterContact->load('customFields.field')
        ]);
    }
}
