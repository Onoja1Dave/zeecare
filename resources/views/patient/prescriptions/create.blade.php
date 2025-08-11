<!DOCTYPE html>
<html>
<head>
    <title>Create New Prescription</title>
    </head>
<body>
    <h1>Create New Prescription</h1>

    <form action="{{ route('prescriptions.store') }}" method="POST">
        @csrf

        <div>
            <label for="patient_id">Patient:</label>
            <select name="patient_id" id="patient_id" required>
                <option value="">-- Select Patient --</option>
                @foreach ($patients as $patient)
                    <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="doctor_id">Prescribing Doctor:</label>
            <select name="doctor_id" id="doctor_id" required>
                <option value="">-- Select Doctor --</option>
                @foreach ($doctors as $doctor)
                    <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="drug_name">Drug Name:</label>
            <input type="text" name="drug_name" id="drug_name" required>
        </div>

        <div>
            <label for="dosage">Dosage:</label>
            <input type="text" name="dosage" id="dosage" required>
        </div>

        <div>
            <label for="frequency">Frequency:</label>
            <input type="text" name="frequency" placeholder="e.g., Once daily, Twice a day" id="frequency" required>
        </div>

        <div>
            <label for="duration">Duration (optional):</label>
            <input type="text" name="duration" placeholder="e.g., 7 days, 2 weeks" id="duration">
        </div>

        <button type="submit">Save Prescription</button>
    </form>

    <a href="{{ route('home') }}">Back to Dashboard</a>
</body>
</html>