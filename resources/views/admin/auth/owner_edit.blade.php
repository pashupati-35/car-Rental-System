<div class="container mt-5">
    <h2>Edit Owner</h2>
    <form action="{{ route('admin.owner.update', $owner->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" name="full_name" class="form-control" value="{{ $owner->full_name }}" required>
        </div>
        <div class="form-group">
            <label for="contact_number">Contact Number</label>
            <input type="text" name="contact_number" class="form-control" value="{{ $owner->contact_number }}" required>
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" name="address" class="form-control" value="{{ $owner->address }}" required>
        </div>
        <div class="form-group">
            <label for="gender">Gender</label>
            <select name="gender" class="form-select" required>
                <option value="male" {{ $owner->gender == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ $owner->gender == 'female' ? 'selected' : '' }}>Female</option>
                <option value="other" {{ $owner->gender == 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $owner->email }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
