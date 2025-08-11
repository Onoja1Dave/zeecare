<!DOCTYPE html>
<html>
<head>
    <title>Your Progress Notes</title>
</head>
<body>
    <h1>Your Progress Notes</h1>

    @if ($notes->isEmpty())
        <p>No progress notes available.</p>
    @else
        <ul>
            @foreach ($notes as $note)
                <li>
                    <p><strong>Date:</strong> {{ $note->created_at->format('Y-m-d H:i') }}</p>
                    <p><strong>Doctor:</strong> {{ $note->doctor->name }}</p>
                    <p><strong>Note:</strong></p>
                    <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
                        {!! nl2br(e($note->content)) !!}
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    <p><a href="{{ route('home') }}">Back to Home</a></p>
</body>
</html>