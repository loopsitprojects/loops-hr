<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #009688; color: white; text-decoration: none; border-radius: 5px; }
        .details { background: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Interview Invitation</h2>
        <p>Dear {{ $recipientName ?? 'Candidate' }},</p>
        
        <p>An interview has been scheduled for the position of <strong>{{ $interview->candidate->designation->name ?? $interview->candidate->designation ?? 'Potential Hire' }}</strong>.</p>
        
        <div class="details">
            <p><strong>Candidate:</strong> {{ $interview->candidate->name }}</p>
            <p><strong>Interviewer:</strong> {{ $interview->interviewer->name }}</p>
            <p><strong>Date:</strong> {{ $interview->scheduled_at->format('l, F j, Y') }}</p>
            <p><strong>Time:</strong> {{ $interview->scheduled_at->format('g:i A') }} ({{ $interview->duration }} mins)</p>
        </div>

        <p>Please join the Google Meet using the link below at the scheduled time:</p>
        
        <p>
            <a href="{{ $interview->meet_link }}" class="btn">Join Google Meet</a>
        </p>
        
        <p>or copy this link: <br> {{ $interview->meet_link }}</p>

        <p>Best regards,<br>{{ config('app.name') }}</p>
    </div>
</body>
</html>
