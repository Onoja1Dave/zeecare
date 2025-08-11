<!DOCTYPE html>
<html>
<head>
    <title>Create New Patient</title>
</head>
<body>
    <h1>Create New Patient</h1>

    <form action="{{ route('patients.store') }}" method="POST">
        @csrf

        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name">
        </div>

        <div>
            <label for="date_of_birth">Date of Birth:</label>
            <input type="date" id="date_of_birth" name="date_of_birth">
        </div>

        <div>
            <label for="gender">Gender:</label>
            <select id="gender" name="gender">
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
        </div>

        <div>
            <label for="contact_number">Contact Number:</label>
            <input type="text" id="contact_number" name="contact_number">
        </div>

        <button type="submit">Save Patient</button>
    </form>
</body>
</html>