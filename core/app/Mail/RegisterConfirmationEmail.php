<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class RegisterConfirmation extends Mailable
{

    /**
     * The order instance.
     *
     * @var Order
     */
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email-confirm-registration')
        ->with(['data' => $this->data]);
    }
}
