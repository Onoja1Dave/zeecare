<!DOCTYPE html>
<html>
<head>
    <title>Your Prescriptions</title>
</head>
<body>
    <h1>Your Prescriptions</h1>

    @if ($prescriptions->isEmpty())
        <p>You have no prescriptions.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Drug Name</th>
                    <th>Dosage</th>
                    <th>Frequency</th>
                    <th>Duration</th>
                    <th>Prescribed By</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prescriptions as $prescription)
                    <tr>
                        <td>{{ $prescription->drug_name }}</td>
                        <td>{{ $prescription->dosage ?? '-' }}</td>
                        <td>{{ $prescription->frequency ?? '-' }}</td>
                        <td>{{ $prescription->duration ?? '-' }}</td>
                        <td>{{ $prescription->doctor->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p> <a href="{{ route('patient.dashboard') }}" >Back to Home</a></p>
</body>
</html>