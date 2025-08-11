<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
</head>
<body>
    <h1>Edit Your Profile</h1>

    <form method="POST" action="{{ route('patient.edit') }}">
        @csrf
        @method('PUT') {{-- For updating resources --}}

        <div>
            <label for="date_of_birth">Date of Birth:</label>
            <input type="date" id="date_of_birth" name="date_of_birth" value="{{ $patient->date_of_birth ?? '' }}">
        </div>

        <div>
            <label for="gender">Gender:</label>
            <select id="gender" name="gender">
                <option value="">Select Gender</option>
                <option value="male" {{ $patient->gender === 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ $patient->gender === 'female' ? 'selected' : '' }}>Female</option>
                <option value="other" {{ $patient->gender === 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <div>
            <label for="contact_number">Contact Number:</label>
            <input type="text" id="contact_number" name="contact_number" value="{{ $patient->contact_number ?? '' }}">
        </div>

        <button type="submit">Update Profile</button>
    </form>

    <p><a href="{{ route('home') }}">Back to Home</a></p>
</body>
</html>