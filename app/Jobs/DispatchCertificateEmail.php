<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\CertificateMail;
use Illuminate\Support\Facades\Log;

class DispatchCertificateEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email;
    public $name;
    public $filePath;
    public $eventName;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $name, $filePath, $eventName = 'Event')
    {
        $this->email = $email;
        $this->name = $name;
        $this->filePath = $filePath;
        $this->eventName = $eventName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($this->email)->send(new CertificateMail($this->name, $this->filePath, $this->eventName));
                // Optionally delete the file if you only want to send it via email.
                // However, the controller deletes the directory after zipping. 
                // We should pass a permanent path if we are dispatching, or ensure the file isn't deleted before the job runs!
                // Best practice: The Controller should save a permanent copy for the job.
            } catch (\Exception $e) {
                Log::error('Failed to send certificate email to ' . $this->email . ': ' . $e->getMessage());
            }
        }
    }
}
