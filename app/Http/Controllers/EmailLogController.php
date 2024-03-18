<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use Illuminate\Http\Request;

class EmailLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $mails = EmailLog::where('status', 'sent')->orderBy('created_at')->orderBy('status')->paginate(50);
        return view('admin.emails.index', ['emails' => $mails]);
    }

    public function pending () {
        $mails = EmailLog::where('status', 'pending')->orderBy('created_at')->paginate(50);
        return view('admin.emails.index', ['emails' => $mails]);
    }

    public function resend(Request $request, EmailLog $id)
    {
        $id->status = 'pending';
        $id->save();
        return back()->with('message', 'Email updated successfully!');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EmailLog $emailLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmailLog $emailLog)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmailLog $id)
    {
        dd($request->all());
        $id->content = $request->message;
        $id->save();
        return back()->with('message', 'Email has been updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailLog $emailLog)
    {
        //
    }

    public function sendMail ($count = 100) {
        sendEmail($count);
    }

    public function sweep () {
        EmailLog::where('status', '!=', 'pending')->delete();

        return back()->with('message', 'Emails cleared successfully');
    }
}
