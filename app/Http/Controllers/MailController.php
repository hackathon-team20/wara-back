<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\TopicMail;
class MailController extends Controller
{
    public function mail(Request $request)
    {
        // ユーザーリストを取得
        $users = User::where('is_admin', false)->get();

        // メールを送信
        foreach ($users as $user) {
            Mail::to($user->email)->send(new TopicMail($request->input('email_subject'), $request->input('email_body')));
        }

        return response()->json(['status' => 'success', 'message' => 'Topic posted and emails sent successfully.'], 200);
    }
}
