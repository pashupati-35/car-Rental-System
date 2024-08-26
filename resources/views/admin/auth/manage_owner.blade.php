@extends('admin.layouts.app')

@section('content')

    <div class="container mt-5">
        <h2 class="mb-4">Manage Owners</h2>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Contact Number</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($owners as $owner)
                <tr>
                    <td>{{ $owner->id }}</td>
                    <td>{{ $owner->full_name }}</td>
                    <td>{{ $owner->contact_number }}</td>
                    <td>{{ $owner->email }}</td>
                    <td>
                        <button class="btn btn-warning"
                                onclick="openModal('{{ route('admin.owner.edit', $owner->id) }}', 'Edit Owner')">Edit
                        </button>
                        <button class="btn btn-info"
                                onclick="openModal('{{ route('admin.owner.view', $owner->id) }}', 'View Owner')">View
                        </button>
                        <form action="{{ route('admin.owner.delete', $owner->id) }}" method="POST"
                              style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this owner?')">Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @include('admin.layouts.modal')
@endsection

@push('scripts')
    <script>
        function openModal(url, title) {
            console.log('Opening modal with URL:', url); // Debug log
            $('#ownerModalLabel').text(title);
            $('#ownerModalBody').load(url, function (response, status, xhr) {
                if (status === "success") {
                    console.log('Modal content loaded'); // Debug log
                    var myModal = new bootstrap.Modal(document.getElementById('ownerModal'));
                    myModal.show();
                } else {
                    console.log('Failed to load modal content:', xhr.statusText); // Debug log
                }
            });
        }
    </script>
@endpush
