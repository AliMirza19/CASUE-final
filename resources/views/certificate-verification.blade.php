<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .verification-card { max-width: 600px; margin: 50px auto; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .valid-icon { color: #198754; font-size: 4rem; }
        .invalid-icon { color: #dc3545; font-size: 4rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card verification-card border-0">
            <div class="card-body text-center p-5">
                @if($isValid)
                    <i class="fas fa-check-circle valid-icon mb-4"></i>
                    <h2 class="text-success mb-3">Certificate Verified!</h2>
                    <p class="lead mb-4">This is a valid certificate issued by CAUSE Society Management.</p>
                    
                    <div class="text-start bg-light p-4 rounded text-dark">
                        <p class="mb-2"><strong><i class="fas fa-user me-2"></i>Participant Name:</strong> {{ $certificate->participant_name }}</p>
                        <p class="mb-2"><strong><i class="fas fa-calendar-alt me-2"></i>Event:</strong> {{ $certificate->event_name }}</p>
                        <p class="mb-2"><strong><i class="fas fa-barcode me-2"></i>Certificate ID:</strong> {{ $certificate->uuid }}</p>
                        <p class="mb-0"><strong><i class="fas fa-clock me-2"></i>Issued On:</strong> {{ $certificate->created_at->format('F j, Y') }}</p>
                    </div>
                @else
                    <i class="fas fa-times-circle invalid-icon mb-4"></i>
                    <h2 class="text-danger mb-3">Verification Failed</h2>
                    <p class="lead text-muted">{{ $message }}</p>
                @endif
                
                <div class="mt-5">
                    <a href="{{ url('/') }}" class="btn btn-outline-secondary"><i class="fas fa-home me-2"></i>Return Home</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
