<div class="container mt-5  " >
    <h2>View Owner</h2>
    <div class="form-group">
        <label for="full_name">Full Name</label>
        <label>
            <input type="text" name="full_name" class="form-control" value="{{ $owner->full_name }}" disabled>
        </label>
    </div>
    <div class="form-group">
        <label for="contact_number">Contact Number</label>
        <label>
            <input type="text" name="contact_number" class="form-control" value="{{ $owner->contact_number }}" disabled>
        </label>
    </div>
    <div class="form-group">
        <label for="address">Address</label>
        <label>
            <input type="text" name="address" class="form-control" value="{{ $owner->address }}" disabled>
        </label>
    </div>
    <div class="form-group">
        <label for="gender">Gender</label>
        <label>
            <input type="text" name="gender" class="form-control" value="{{ ucfirst($owner->gender) }}" disabled>
        </label>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <label>
            <input type="email" name="email" class="form-control" value="{{ $owner->email }}" disabled>
        </label>
    </div>
</div>
