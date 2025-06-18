<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EvaluadorViajeEnviada extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($datos, $viaje, $adjuntarArchivos=false, $adjuntarPlanilla=false)
    {
        $this->datos = $datos;
        $this->viaje = $viaje;
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

        $mail = $this->view('emails.evaluador_viaje')
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
            if (!empty($this->viaje->curriculum) && file_exists(public_path($this->viaje->curriculum))) {
                $mail->attach(public_path($this->viaje->curriculum));
            }

            if (!empty($this->viaje->trabajo) && file_exists(public_path($this->viaje->trabajo))) {
                $mail->attach(public_path($this->viaje->trabajo));
            }

            if (!empty($this->viaje->aceptacion) && file_exists(public_path($this->viaje->aceptacion))) {
                $mail->attach(public_path($this->viaje->aceptacion));
            }

            if (!empty($this->viaje->invitacion) && file_exists(public_path($this->viaje->invitacion))) {
                $mail->attach(public_path($this->viaje->invitacion));
            }

            if (!empty($this->viaje->convenioB) && file_exists(public_path($this->viaje->convenioB))) {
                $mail->attach(public_path($this->viaje->convenioB));
            }

            if (!empty($this->viaje->aval) && file_exists(public_path($this->viaje->aval))) {
                $mail->attach(public_path($this->viaje->aval));
            }

            if (!empty($this->viaje->cvprofesor) && file_exists(public_path($this->viaje->cvprofesor))) {
                $mail->attach(public_path($this->viaje->cvprofesor));
            }

            if (!empty($this->viaje->convenioC) && file_exists(public_path($this->viaje->convenioC))) {
                $mail->attach(public_path($this->viaje->convenioC));
            }
        }

        return $mail;
    }
}
