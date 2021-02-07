<?php

namespace App\Services;

class Mail {
    public function sendMail($user, $subject, $body){
        $to_name = $user->full_name;
        $to_email = $user->email;
        $data = array('name'=> $user->full_name, 'body' => $body);
        Mail::send('emails.mail', $data, function($message) use ($to_name, $to_email) {
        $message->to($to_email, $to_name)
        ->subject($subject);
        $message->from(env('MAIL_USERNAME'));
        });
    }
}