<?php

namespace App\Services;

class Mail {
    public function sendMail($user, $mail, $subject, $data){
        $to_name = $user->email;
        $to_email = $user->email;
        $from_email = config('mail.mailers.smtp.username');
        $from_name = 'HospitalQ';
        \Mail::send('emails.' . $mail, $data, function($message) use ($to_name, $to_email, $subject, $from_email, $from_name) {
            $message->to($to_email, $to_name)
            ->subject($subject);
            $message->from($from_email, $from_name);
        });
    }
}