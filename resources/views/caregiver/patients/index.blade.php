@extends('layouts.caregiver')

@section('content')
<div class="container mt-4">
    <h2>Patients Under My Care</h2>

    @if($patients->isEmpty())
        <p>You currently have no patients assigned to you.</p>
    @else
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact Number</th> 
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($patients as $patient)
                    <tr>
                        <td>{{ $patient->name }}</td> 
                        <td>{{ $patient->contact_number }}</td> 
                        <td>
                            <a href="{{ route('caregiver.patients.show', $patient->id) }}" class="btn btn-sm btn-info">View Details</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection