<x-mail::message>
# Password Reset Verification

You have requested to reset your password for the CAUSE Smart Society Management System. 

Please use the following 6-digit OTP to verify your identity. This OTP is valid for **10 minutes**.

<x-mail::panel>
# {{ $otp }}
</x-mail::panel>

If you did not request a password reset, please ignore this email or contact support immediately.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
