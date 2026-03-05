<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SicadiEnviada extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($datos, $solicitud, $adjuntarArchivos=false, $adjuntarPlanilla=false)
    {
        $this->datos = $datos;
        $this->solicitud = $solicitud;
        $this->adjuntarArchivos = $adjuntarArchivos;
        $this->adjuntarPlanilla = $adjuntarPlanilla;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $mail = $this->view('emails.sicadi')
            ->subject($this->datos['asunto'])
            ->with($this->datos)
            ->from($this->datos['from_email'], $this->datos['from_name']) // Obtener desde $datos
            ->replyTo($this->datos['from_email'], $this->datos['from_name']); // Obtener desde $datos
        if ($this->adjuntarPlanilla) {
            if (file_exists($this->adjuntarPlanilla)) {
                $mail->attach($this->adjuntarPlanilla);
            }
        }

        if ($this->adjuntarArchivos) {
            // Attach the curriculum file if it exists
            if (!empty($this->solicitud->curriculum) && file_exists(public_path($this->solicitud->curriculum))) {
                $mail->attach(public_path($this->solicitud->curriculum));
            }


        }

        return $mail;
    }
}
