<?php

/**
 * Excepci�n para indicar que el captcha no es correcta.
 * 
 * @author bernardo
 * @since 12-05-2011
 */
class CaptchaException extends GenericException {
    public function __construct() { // Cambio: Debe ser __construct, no CaptchaException
        $cod = 0;
        parent::__construct("Problema con el reCAPTCHA");
    }
}
