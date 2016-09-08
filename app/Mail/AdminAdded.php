<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Admin;

class AdminAdded extends Mailable
{
    use Queueable, SerializesModels;

    public $admin, $password, $greeting, $level, $introLines, $actionText, $actionUrl, $outroLines;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Admin $admin, String $password)
    {
        $this->admin = $admin;
        $this->password = $password;
        $this->greeting = 'Hello';
        $this->introLines[0] = 'Your account has been created. Please use the following credentials to login.';
        $this->actionText = 'Visit WebGl';
        $this->actionUrl = url('admin');
        $this->outroLines[0] = 'Username: ' . $admin->email;
        $this->outroLines[1] = 'Password: ' . $password; 
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.adminAdded');
    }
}
