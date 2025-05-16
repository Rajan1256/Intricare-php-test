<table class="table table-striped">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Gender</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($contacts as $contact)
        <tr>
            <td>
                @if($contact->profile_image)
                    <img src="{{ asset('storage/' . $contact->profile_image) }}"
                         class="rounded-circle me-2" width="30" height="30">
                @endif
                {{ $contact->name }}
            </td>
            <td>{{ $contact->email }}</td>
            <td>{{ $contact->phone }}</td>
            <td>{{ ucfirst($contact->gender) }}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary edit-contact"
                        data-id="{{ $contact->id }}">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger delete-contact"
                        data-id="{{ $contact->id }}">
                    <i class="bi bi-trash"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary merge-contact"
                        data-id="{{ $contact->id }}">
                    <i class="bi bi-arrow-merge"></i> Merge
                </button>
                <button class="btn btn-sm btn-outline-secondary show-merge-contact"
                        data-id="{{ $contact->id }}">
                    <i class="bi bi-arrow-merge"></i>Show Merge
                </button>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">No contacts found</td>
        </tr>
        @endforelse
    </tbody>
</table>

@if(!request()->ajax())
<div class="d-flex justify-content-between align-items-center mt-3">
    <div>
        <button class="btn btn-outline-primary" data-bs-toggle="modal"
                data-bs-target="#customFieldsModal">
            <i class="bi bi-gear"></i> Manage Custom Fields
        </button>
    </div>
    <div>
        {{ $contacts->links() }}
    </div>
</div>
@endif
