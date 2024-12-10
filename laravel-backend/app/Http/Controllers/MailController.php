<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendMail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string',
            'messageContent' => 'required|string',
        ]);

        $data = [
            'subject' => $validated['subject'],
            'messageContent' => $validated['messageContent'],
        ];

        Mail::send('emails.notification', $data, function ($message) use ($validated) {
            $message->to($validated['email'])
                ->subject($validated['subject']);
        });

        return response()->json(['message' => 'Email đã được gửi thành công!']);
    }
}
