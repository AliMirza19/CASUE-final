<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateVerificationController extends Controller
{
    public function verify($uuid)
    {
        $certificate = Certificate::where('uuid', $uuid)->first();

        if (!$certificate) {
            return view('certificate-verification', [
                'isValid' => false,
                'message' => 'Invalid or missing certificate record.'
            ]);
        }

        return view('certificate-verification', [
            'isValid' => true,
            'certificate' => $certificate
        ]);
    }
}
