@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Contacts</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addContactModal">
                <i class="bi bi-plus"></i> Add Contact
            </button>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" id="nameFilter" class="form-control" placeholder="Filter by name">
                </div>
                <div class="col-md-3">
                    <input type="text" id="emailFilter" class="form-control" placeholder="Filter by email">
                </div>
                <div class="col-md-3">
                    <select id="genderFilter" class="form-select">
                        <option value="">All Genders</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button id="resetFilters" class="btn btn-outline-secondary">Reset Filters</button>
                </div>
            </div>

            <div id="contactsTable">
                @include('contacts.partials.table', ['contacts' => $contacts])
            </div>
        </div>
    </div>


    <div class="modal fade" id="addContactModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addContactForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone *</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gender *</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="male"
                                            value="male" required>
                                        <label class="form-check-label" for="male">Male</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="female"
                                            value="female">
                                        <label class="form-check-label" for="female">Female</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="other"
                                            value="other">
                                        <label class="form-check-label" for="other">Other</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Profile Image</label>
                                <input type="file" name="profile_image" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Additional File</label>
                                <input type="file" name="additional_file" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6>Custom Fields</h6>
                            <div id="customFieldsContainer">
                                @foreach ($customFields as $field)
                                    <div class="mb-3">
                                        <label class="form-label">{{ $field->field_name }} @if ($field->is_required)
                                                *
                                            @endif
                                        </label>
                                        @if ($field->field_type === 'text')
                                            <input type="text" name="{{ $field->field_name }}" class="form-control"
                                                @if ($field->is_required) required @endif>
                                        @elseif($field->field_type === 'number')
                                            <input type="number" name="{{ $field->field_name }}" class="form-control"
                                                @if ($field->is_required) required @endif>
                                        @elseif($field->field_type === 'date')
                                            <input type="date" name="{{ $field->field_name }}" class="form-control"
                                                @if ($field->is_required) required @endif>
                                        @elseif($field->field_type === 'checkbox')
                                            <input type="checkbox" name="{{ $field->field_name }}"
                                                class="form-check-input" value="1"
                                                @if ($field->is_required) required @endif>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Contact</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editContactModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editContactForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editId">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" name="name" id="editName" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" id="editEmail" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone *</label>
                                <input type="text" name="phone" id="editPhone" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gender *</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="editMale"
                                            value="male" required>
                                        <label class="form-check-label" for="editMale">Male</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="editFemale"
                                            value="female">
                                        <label class="form-check-label" for="editFemale">Female</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="editOther"
                                            value="other">
                                        <label class="form-check-label" for="editOther">Other</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Profile Image</label>
                                <input type="file" name="profile_image" class="form-control" accept="image/*">
                                <div id="editProfileImagePreview" class="mt-2"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Additional File</label>
                                <input type="file" name="additional_file" class="form-control">
                                <div id="editAdditionalFilePreview" class="mt-2"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6>Custom Fields</h6>
                            <div id="editCustomFieldsContainer">

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Contact</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="mergeContactsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Merge Contacts</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="mergeContactsForm">
                    <div class="modal-body">
                        <input type="hidden" name="merged_contact_id" id="mergedContactId">
                        <div class="alert alert-info">
                            <p>You are about to merge contacts. The contact you select as "Master" will be kept, and the
                                other contact will be marked as merged.</p>
                            <p class="mb-0"><strong>Note:</strong> No data will be permanently deleted. Merged contact
                                data will be preserved under the master record.</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select Master Contact *</label>
                            <select name="master_contact_id" id="masterContactSelect" class="form-select" required>
                                <option value="">Select a contact</option>

                            </select>
                        </div>
                        <div id="mergePreview" class="border p-3 rounded">
                            <h6>Merge Preview</h6>
                            <div id="mergeDetails">

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Confirm Merge</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="showMergeContactsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Merge Contacts</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="mergeContactsForm">
                    <div class="modal-body">
                        <div id="mergePreview" class="border p-3 rounded">
                            <h6>Merge Preview</h6>
                            <div id="showMergeDetails">

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="customFieldsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Custom Fields</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <button id="addCustomFieldBtn" class="btn btn-sm btn-primary mb-3">
                            <i class="bi bi-plus"></i> Add Field
                        </button>
                        <div id="customFieldsList">

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="customFieldFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customFieldModalTitle">Add Custom Field</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="customFieldForm">
                    <div class="modal-body">
                        <input type="hidden" id="customFieldId">
                        <div class="mb-3">
                            <label class="form-label">Field Name *</label>
                            <input type="text" id="fieldName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Field Type *</label>
                            <select id="fieldType" class="form-select" required>
                                <option value="text">Text</option>
                                <option value="number">Number</option>
                                <option value="date">Date</option>
                                <option value="checkbox">Checkbox</option>
                            </select>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="isRequired">
                            <label class="form-check-label" for="isRequired">Required Field</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Field</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {

                function loadCustomFields() {
                    $.get('/custom-fields', function(data) {
                        let html = '';
                        data.forEach(field => {
                            html += `
                    <div class="card mb-2" data-id="${field.id}">
                        <div class="card-body p-2 d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${field.field_name}</strong> (${field.field_type})
                                ${field.is_required ? '<span class="badge bg-danger ms-2">Required</span>' : ''}
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-primary edit-field" data-id="${field.id}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-field" data-id="${field.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                        });
                        $('#customFieldsList').html(html || '<p>No custom fields defined yet.</p>');
                    });
                }


                function loadContacts() {
                    const name = $('#nameFilter').val();
                    const email = $('#emailFilter').val();
                    const gender = $('#genderFilter').val();

                    $.ajax({
                        url: '{{ route('contacts.index') }}',
                        data: {
                            name: name,
                            email: email,
                            gender: gender,
                            ajax: true,
                            html: true
                        },
                        success: function(response) {
                            $('#contactsTable').html(response);
                        },
                        error: function(xhr) {
                            console.error('Error loading contacts:', xhr.responseText);
                            $('#contactsTable').html(
                                '<div class="alert alert-danger">Failed to load contacts. Please try again.</div>'
                            );
                        }
                    });
                }


                $('#nameFilter, #emailFilter, #genderFilter').on('change keyup', function() {
                    loadContacts();
                });

                $('#resetFilters').click(function() {
                    $('#nameFilter').val('');
                    $('#emailFilter').val('');
                    $('#genderFilter').val('');
                    loadContacts();
                });


                $('#addContactForm').submit(function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    $.ajax({
                        url: '{{ route('contacts.store') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $('#addContactModal').modal('hide');
                            $('#addContactForm')[0].reset();
                            showAlert('success', response.message);
                            loadContacts();
                        },
                        error: function(xhr) {
                            showErrors(xhr.responseJSON.errors);
                        }
                    });
                });


                $(document).on('click', '.edit-contact', function() {
                    const contactId = $(this).data('id');

                    $.get('/contacts/' + contactId, function(response) {
                        console.log(response);
                        const contact = response.contact;

                        const customFieldValues = response.custom_fields;
                        console.log(customFieldValues);
                        $('#editId').val(contact.id);
                        $('#editName').val(contact.name);
                        $('#editEmail').val(contact.email);
                        $('#editPhone').val(contact.phone);
                        $(`#edit${contact.gender.charAt(0).toUpperCase() + contact.gender.slice(1)}`)
                            .prop('checked', true);


                        if (contact.profile_image) {
                            $('#editProfileImagePreview').html(`
                    <small>Current image:</small>
                    <img src="/storage/${contact.profile_image}" class="img-thumbnail" style="max-height: 100px;">
                `);
                        } else {
                            $('#editProfileImagePreview').html('<small>No image uploaded</small>');
                        }


                        if (contact.additional_file) {
                            $('#editAdditionalFilePreview').html(`
                    <small>Current file: ${contact.additional_file.split('/').pop()}</small>
                `);
                        } else {
                            $('#editAdditionalFilePreview').html('<small>No file uploaded</small>');
                        }


                        let customFieldsHtml = '';

                        $.get('/custom-fields', function(fields) {
                            console.log('cs-field', fields);
                            fields.forEach((field, index) => {

                                const fieldValue = (customFieldValues.length != 0 ?
                                        customFieldValues[index]['field_value'] : '') ||
                                    field.field_name;


                                customFieldsHtml += `<div class="mb-3">
            <label class="form-label">${field.field_name} ${field.is_required ? '*' : ''}</label>`;

                                if (field.field_type === 'text') {
                                    customFieldsHtml +=
                                        `
            <input type="text" name="${field.field_name}" value="${fieldValue}" class="form-control" ${field.is_required ? 'required' : ''}>`;
                                } else if (field.field_type === 'number') {
                                    customFieldsHtml +=
                                        `
            <input type="number" name="${field.field_name}" value="${fieldValue}" class="form-control" ${field.is_required ? 'required' : ''}>`;
                                } else if (field.field_type === 'date') {
                                    customFieldsHtml +=
                                        `
            <input type="date" name="${field.field_name}" value="${fieldValue}" class="form-control" ${field.is_required ? 'required' : ''}>`;
                                } else if (field.field_type === 'checkbox') {
                                    customFieldsHtml +=
                                        `
            <input type="checkbox" name="${field.field_name}" class="form-check-input" value="1" ${fieldValue ? 'checked' : ''} ${field.is_required ? 'required' : ''}>`;
                                }

                                customFieldsHtml += `</div>`;
                            });


                            $('#editCustomFieldsContainer').html(customFieldsHtml);
                            $('#editContactModal').modal('show');
                        });
                    });
                });


                $('#editContactForm').submit(function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    $.ajax({
                        url: '/contacts/' + $('#editId').val(),
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'X-HTTP-Method-Override': 'PUT'
                        },
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $('#editContactModal').modal('hide');
                            showAlert('success', response.message);
                            loadContacts();
                        },
                        error: function(xhr) {
                            showErrors(xhr.responseJSON.errors);
                        }
                    });
                });


                $(document).on('click', '.delete-contact', function() {
                    if (confirm('Are you sure you want to delete this contact?')) {
                        const contactId = $(this).data('id');

                        $.ajax({
                            url: '/contacts/' + contactId,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                showAlert('success', response.message);
                                loadContacts();
                            }
                        });
                    }
                });


                $(document).on('click', '.merge-contact', function() {
                    const contactId = $(this).data('id');
                    $('#mergedContactId').val(contactId);


                    $.get('/contacts', function(datacontacts) {
                        let contacts = datacontacts.contacts;
                        let options = '<option value="">Select a contact</option>';
                        contacts.forEach(contact => {
                            if (contact.id != contactId) {
                                options +=
                                    `<option value="${contact.id}">${contact.name} (${contact.email})</option>`;
                            }
                        });
                        $('#masterContactSelect').html(options);
                        $('#mergeContactsModal').modal('show');
                    });
                });


                $(document).on('click', '.show-merge-contact', function() {
                    const contactId = $(this).data('id');
                    $('#mergedContactId').val(contactId);


                    $.get('/contacts/' + contactId, function(masterContact) {

                        let mergedContact = masterContact.merged_users;

                        let html =
                            `<p><strong>Master Contact:</strong> ${masterContact.contact.name} (${masterContact.contact.email})</p>`;
                        mergedContact.forEach(contact => {

                            html += `<hr><h6>Data to be merged:</h6><ul>`;
                            html += `<li>Merged Contact: ${contact.merged_contact.name} (${contact.merged_contact.email})</li>`;
                            if (contact.merged_contact.phone) {
                                html +=
                                    `<li>Phone: ${contact.merged_contact.phone}</li>`;
                            }
                            if (contact.merged_contact.profile_image) {
                                html += `<li>Profile Image</li><img src="/storage/${contact.merged_contact.profile_image}" class="img-thumbnail" style="max-height: 100px;">`;
                            }
                            if (contact.merged_contact.additional_file) {
                                html += `<li>Additional File</li>`;
                            }

                             const mergedCustomFields = JSON.parse(contact.merged_data);
                            console.log('ss->',mergedCustomFields);
                             for (const field in mergedCustomFields) {
                                     html +=
                                         `<li>"${field}": ${mergedCustomFields[field]['merged_value']}</li>`;
                             }

                            html += `</ul>`;
                            $('#showMergeDetails').html(html);
                        });
                        $('#showMergeContactsModal').modal('show');
                    });
                });


                $('#masterContactSelect').change(function() {
                    const masterId = $(this).val();
                    const mergedId = $('#mergedContactId').val();

                    if (masterId) {
                        $.get('/contacts/' + masterId, function(masterContact) {
                            console.log(masterContact);
                            $.get('/contacts/' + mergedId, function(mergedContact) {
                                let html = `
                        <p><strong>Master Contact:</strong> ${masterContact.contact.name} (${masterContact.contact.email})</p>
                        <p><strong>Merged Contact:</strong> ${mergedContact.contact.name} (${mergedContact.contact.email})</p>
                        <hr>
                        <h6>Data to be merged:</h6>
                        <ul>
                    `;


                                if (!masterContact.contact.phone && mergedContact.contact
                                    .phone) {
                                    html +=
                                        `<li>Phone: ${mergedContact.contact.contactphone}</li>`;
                                }
                                if (!masterContact.contact.profile_image && mergedContact
                                    .contact.profile_image) {
                                    html += `<li>Profile Image</li>`;
                                }
                                if (!masterContact.contact.additional_file && mergedContact
                                    .contact.additional_file) {
                                    html += `<li>Additional File</li>`;
                                }


                                const masterCustomFields = masterContact.custom_fields;
                                const mergedCustomFields = mergedContact.custom_fields;

                                for (const field in mergedCustomFields) {
                                    if (!(field in masterCustomFields)) {
                                        html +=
                                            `<li>Custom Field "${field}": ${mergedCustomFields[field]['field_value']}</li>`;
                                    } else if (masterCustomFields[field] !== mergedCustomFields[
                                            field]) {
                                        html += `<li>Custom Field "${field}":
                                <span class="text-muted">(Master: ${masterCustomFields[field]['field_value']},
                                Merged: ${mergedCustomFields[field]['field_value']})</span></li>`;
                                    }
                                }

                                html += `</ul>`;
                                $('#mergeDetails').html(html);
                            });
                        });
                    } else {
                        $('#mergeDetails').html('<p>Select a master contact to see merge details.</p>');
                    }
                });


                $('#mergeContactsForm').submit(function(e) {
                    e.preventDefault();
                    const formData = $(this).serialize();
                    const mergedId = $('#mergedContactId').val();
                    $.ajax({
                        url: '/contacts/' + mergedId + '/merge',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            master_contact_id: $('#masterContactId').val()
                        },
                        data: formData,
                        success: function(response) {
                            $('#mergeContactsModal').modal('hide');
                            showAlert('success', response.message);
                            loadContacts();
                        }
                    });
                });


                $('#customFieldsModal').on('show.bs.modal', function() {
                    loadCustomFields();
                });


                $('#addCustomFieldBtn').click(function() {
                    $('#customFieldModalTitle').text('Add Custom Field');
                    $('#customFieldForm')[0].reset();
                    $('#customFieldId').val('');
                    $('#customFieldFormModal').modal('show');
                });


                $(document).on('click', '.edit-field', function() {
                    const fieldId = $(this).data('id');

                    $(document).on('click', '.edit-field', function() {
                        const fieldId = $(this).data('id');

                        $.get('/custom-fields/' + fieldId + '/edit', function(field) {
                            $('#customFieldModalTitle').text('Edit Custom Field');
                            $('#customFieldId').val(field.id);
                            $('#fieldName').val(field.field_name);
                            $('#fieldType').val(field.field_type);
                            $('#isRequired').prop('checked', field.is_required);
                            $('#customFieldFormModal').modal('show');
                        }).fail(function(xhr, status, error) {
                            console.error("Error fetching field:", error);
                        });
                    });
                });


                $('#customFieldForm').submit(function(e) {
                    e.preventDefault();

                    const fieldId = $('#customFieldId').val();
                    const url = fieldId ? '/custom-fields/' + fieldId : '/custom-fields';
                    const method = fieldId ? 'PUT' : 'POST';

                    $.ajax({
                        url: url,
                        method: method,
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            field_name: $('#fieldName').val(),
                            field_type: $('#fieldType').val(),
                            is_required: $('#isRequired').is(':checked')
                        },
                        success: function() {
                            $('#customFieldFormModal').modal('hide');
                            showAlert('success', 'Custom field saved successfully');
                            loadCustomFields();


                            $.get('/custom-fields', function(fields) {
                                let html = '';
                                fields.forEach(field => {
                                    html += `
                            <div class="mb-3">
                                <label class="form-label">${field.field_name} ${field.is_required ? '*' : ''}</label>
                        `;

                                    if (field.field_type === 'text') {
                                        html += `
                                <input type="text" name="${field.field_name}" class="form-control" ${field.is_required ? 'required' : ''}>
                            `;
                                    } else if (field.field_type === 'number') {
                                        html += `
                                <input type="number" name="${field.field_name}" class="form-control" ${field.is_required ? 'required' : ''}>
                            `;
                                    } else if (field.field_type === 'date') {
                                        html += `
                                <input type="date" name="${field.field_name}" class="form-control" ${field.is_required ? 'required' : ''}>
                            `;
                                    } else if (field.field_type === 'checkbox') {
                                        html += `
                                <input type="checkbox" name="${field.field_name}" class="form-check-input" value="1" ${field.is_required ? 'required' : ''}>
                            `;
                                    }

                                    html += `</div>`;
                                });

                                $('#customFieldsContainer').html(html);
                            });
                        }
                    });
                });


                $(document).on('click', '.delete-field', function() {
                    if (confirm(
                            'Are you sure you want to delete this custom field? All contact data for this field will also be deleted.'
                        )) {
                        const fieldId = $(this).data('id');

                        $.ajax({
                            url: '/custom-fields/' + fieldId,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function() {
                                showAlert('success', 'Custom field deleted successfully');
                                loadCustomFields();
                            }
                        });
                    }
                });


                function showAlert(type, message) {
                    const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`;
                    $('.container').prepend(alertHtml);
                    setTimeout(() => $('.alert').alert('close'), 5000);
                }


                function showErrors(errors) {
                    let errorHtml = '<div class="alert alert-danger"><ul>';
                    for (const field in errors) {
                        errorHtml += `<li>${errors[field][0]}</li>`;
                    }
                    errorHtml += '</ul></div>';
                    $('.modal-body').prepend(errorHtml);
                }
            });
        </script>
    @endpush
@endsection
