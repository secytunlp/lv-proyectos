<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SolicitudEnviada extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($datos, $integrante, $adjuntarArchivos=false, $adjuntarPlanilla=false)
    {
        $this->datos = $datos;
        $this->integrante = $integrante;
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

        $mail = $this->view('emails.solicitud')
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
            if (!empty($this->integrante->curriculum) && file_exists(public_path($this->integrante->curriculum))) {
                $mail->attach(public_path($this->integrante->curriculum));
            }

            // Attach the actividades file if it exists
            if (!empty($this->integrante->actividades) && file_exists(public_path($this->integrante->actividades))) {
                $mail->attach(public_path($this->integrante->actividades));
            }

            // Attach the resolucion file if it exists and the type is 'Becario, Tesista'
            if ($this->integrante->tipo == 'Becario, Tesista' && !empty($this->integrante->resolucion) && file_exists(public_path($this->integrante->resolucion))) {
                $mail->attach(public_path($this->integrante->resolucion));
            }
        }

        return $mail;
    }
}
