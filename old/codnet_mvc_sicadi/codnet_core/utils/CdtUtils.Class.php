<?php

/**
 * Utilidades.
 * Funciones comunes que sirven para distintos propÔøΩsitos.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 18/10/2011
 */
class CdtUtils {

    /**
     * se ejecuta una url por curl por mÔøΩtodo GET
     * @param string $url url a invocar
     * @throws GenericException excepciÔøΩn por fallo.
     * @return response
     */
    public static function curlGET($url) {
        // Create a curl handle
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_GET,true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1");
        curl_setopt($ch, CURLOPT_HEADER, false);
        //curl_setopt($ch, CURLOPT_COOKIEFILE,$this->cookie);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept-Language: es-es,en"));
        //curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 160);
        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);


        // Execute
        $response = curl_exec($ch);

        // Check if any error occured
        if (curl_errno($ch))
            throw new GenericException(curl_error($ch));

        // Close handle
        curl_close($ch);

        return $response;
    }

    /**
     * se ejecuta una url por curl por mÔøΩtodo POST
     * @param string $url url a invocar
     * @param unknown_type $params parÔøΩmetros con los valores a enviar por post
     * @throws GenericException excepciÔøΩn por fallo.
     * @return response
     */
    public static function curlPOST($url, $params) {
        // Create a curl handle
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        /* curl_setopt($ch, CURLOPT_HEADER, false);

          curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept-Language: es-es,en")); */

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        /* curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
          curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
          curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
          curl_setopt($ch, CURLOPT_TIMEOUT, 160);
          curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE); */


        // Execute
        $response = curl_exec($ch);

        // Check if any error occured
        if (curl_errno($ch))
            throw new GenericException(curl_error($ch));

        // Close handle
        curl_close($ch);

        return $response;
    }
    
    /**
     * log genÔøΩrico.
     * @param string $msg mensaje a loguear.
     */
    public static function log($msg, $class=null, $level=null) {
		//Logger::configure(APP_PATH . "/conf/log4php.xml");

    	//chequeamos si se estÔøΩ utilizando la librerÔøΩa log4php
        if (class_exists('Logger')) {

        	if($level==null)
				$level = LoggerLevel::getLevelDebug();
			
			if($class==null)	
				$class= __CLASS__;
				
	    	Logger::getLogger( $class )->log($level, $msg );
	        	
        }else{
        
        	 $nombreFile = date('Ymd') . '_cdt_log';
	         $dt = date('Y-m-d G:i:s');
	
	         $_Log = fopen(APP_PATH . "logs/" . $nombreFile . ".log", "a+") or die("Operation Failed!");
	
	         fputs($_Log, $dt . " --> " . $msg . "\n");
	
	         fclose($_Log);
        }
    	
    }
    

    /**
     * log en el archivo destinado a mensajes informativos.
     * @param string $msg mensaje a loguear.
     */
    public static function log_message($msg, $class=__CLASS__) {

        if (CDT_MESSAGES_LOG) {
        	
        	//chequeamos si se estÔøΩ utilizando la librerÔøΩa log4php
        	if (class_exists('Logger')) {

        		//TODO ver si se puede hacer una ÔøΩnica vez la configuraciÔøΩn. 
        		//Logger::configure(CDT_LOG4PHP_CONFIG_FILE);
        					
        		Logger::getLogger( $class )->info( $msg );
        		
        	}else{
        		
                $nombreFile = date('Ymd') . '_cdt_messages';
	            $dt = date('Y-m-d G:i:s');
	
	            $_Log = fopen(APP_PATH . "logs/" . $nombreFile . ".log", "a+") or die("Operation Failed!");
	
	            fputs($_Log, $dt . " --> " . $msg . "\n");
	
	            fclose($_Log);
        	}
        }
    }

    /**
     * log en el archivo destinado a mensajes de debug.
     * @param string $msg mensajes a loguear
     */
    public static function log_debug($msg, $class=__CLASS__) {

        if (CDT_DEBUG_LOG) {
        	
        	//chequeamos si se estÔøΩ utilizando la librerÔøΩa log4php
        	if (class_exists('Logger')) {

        		//TODO ver si se puede hacer una ÔøΩnica vez la configuraciÔøΩn. 
        		//Logger::configure(CDT_LOG4PHP_CONFIG_FILE);
        					
        		Logger::getLogger($class)->debug( $msg );
        		
        	}else{
	            $nombreFile = date('Ymd') . '_cdt_debug';
	            $dt = date('Y-m-d G:i:s');
	
	            $_Log = fopen(APP_PATH . "logs/" . $nombreFile . ".log", "a+") or die("Operation Failed!");
	
	            fputs($_Log, $dt . " --> " . $msg . "\n");
	
	            fclose($_Log);
        	}
        }
        
    }

    /**
     * log en el archivo destinado a mensajes de error.
     * @param string $msg mensaje a loguear.
     */
    public static function log_error($msg, $class=__CLASS__) {

        if (CDT_ERROR_LOG) {
        	
        	
        	//chequeamos si se estÔøΩ utilizando la librerÔøΩa log4php
        	if (class_exists('Logger')) {

        		//TODO ver si se puede hacer una ÔøΩnica vez la configuraciÔøΩn. 
        		//Logger::configure(CDT_LOG4PHP_CONFIG_FILE);
				
        		Logger::getLogger($class)->error( $msg );
        		
        	}else{
        	
        	
	            $nombreFile = date('Ymd') . '_cdt_errors';
	            $dt = date('Y-m-d G:i:s');
	
	            $_Log = fopen(APP_PATH . "logs/" . $nombreFile . ".log", "a+") or die("Operation Failed!");
	
	            fputs($_Log, $dt . " --> " . $msg . "\n");
	
	            fclose($_Log);
        	}
        }
    }

    /**
     * Descifra el nombre de un action a partir de una url
     * @param string $url string que contiene el nombre del action
     * @return nombre de un action
     */
    public static function getActionFromUrl($url) {

        //buscamos si existe el texto "action".
        $pos_action = strpos($url, "action");


        if (!$pos_action) {

            //el url es del estilo somepath/home.html?params
            $pos = strpos($url, ".html");
            if ($pos) {
                $ds_action = explode(".", $url);
                $ds_action = $ds_action[0];
                //si tiene "/", las quitamos
                if (strrchr($ds_action, '/'))
                    $ds_action = strrchr($ds_action, '/');
            }

            $ds_action_value = $ds_action;
        }else {
            //el url es del action "...?action=value"..
            $ds_action = substr($url, $pos_action);
            $length = strpos($ds_action, "&");
            if ($length)
                $ds_action = substr($ds_action, 0, $length);
            $pos_equal = strpos($ds_action, "=");
            $ds_action_value = substr($ds_action, $pos_equal + 1);
        }


        return $ds_action_value;
    }

    /**
     * nos dice si hay un error asociado al request.
     * @return true|false
     */
    public static function hasRequestError() {
        //return (self::getParam("error_code") != "" || self::getParam("error_msg") != "");
        $data = self::getParamSESSION(APP_NAME, false, false);
        if( empty ($data) )
        	return false;
        
        
    	$error["code"] = (isset($data["error_code"]))?$data["error_code"]:'';
        $error["msg"] = (isset($data["error_msg"]))?$data["error_msg"]:'';
        
        return ( !empty($error["code"]) || !empty($error["msg"]));
        
    }

    /**
     * retorna el error asociado al request.
     * @return array(code, msg)
     */
    public static function getRequestError() {

    	/*
        $error["code"] = self::getParam("error_code", false, false);
        $error["msg"] = self::getParam("error_msg");
		*/
    	$data = self::getParamSESSION(APP_NAME, false, false);
    	
        $error["code"] = $data["error_code"];
        $error["msg"] = $data["error_msg"];
		return $error;
    }

    public static function cleanRequestError() {

        /*
    	unset($_GET["error_code"]);
        unset($_GET["error_msg"]);
        */
    	unset($_SESSION[APP_NAME]["error_code"]);
        unset($_SESSION[APP_NAME]["error_msg"]);
        
    }

    /**
     * asocia un error al request.
     * @return array(code, msg)
     */
    public static function setRequestError(GenericException $ex) {

    	/*
        $_GET["error_code"] = $ex->getCode();
        $_GET["error_msg"] = $ex->getMessage();
        */
    	$_SESSION[APP_NAME]["error_code"] = $ex->getCode();
        $_SESSION[APP_NAME]["error_msg"] = CdtUtils::getMessage($ex);//$ex->getMessage();
        
    }

    /**
     * obtiene el valor del parÔøΩmetro de sesiÔøΩn.
     * @param string $name nombre del parÔøΩmetro
     * @param string $default valor por default en caso de no estar seteado
     * @return param value
     */
    public static function getParamSESSION($name, $default='') {
		
		
        if (isset($_SESSION[$name])) {
            $value = $_SESSION[$name];
        }
        if (empty($value))
            $value = $default;
        return $value;
    }

    /**
     * obtiene el valor del parÔøΩmetro del request x GET.
     * @param string $name nombre del parÔøΩmetro
     * @param string $default valor por default en caso de no estar seteado
     * @param $filter si debe aplicarse el inputFilter
     * @param $encode si debe aplicarse el encode al valor.
     * @return param value
     */
    public static function getParam($name, $default='', $filter = true, $encode = true) {
        
		$value='';
		// Leer el archivo XML que contiene las acciones v·lidas
		$validActions = array();
		
		$xml = simplexml_load_file(APP_PATH .  'includes/navigation.xml');
		foreach ($xml->action as $action) {
			$validActions[] = (string)$action['name'];
		}
		//CdtUtils::logObject($validActions);
		if (isset($_GET[$name])) {
			$action = trim($_GET[$name]);
			//CdtUtils::log($action);
			 // Verificar si el par·metro es 'action'
			if ($name === 'action') {
				//CdtUtils::log('valida');
				// Verificar si la acciÛn est· en la lista de acciones v·lidas
				if (!in_array($action, $validActions)) {
					// La acciÛn no es v·lida, puedes registrar un intento de acceso no autorizado o redirigir a una p·gina de error.
					// Por ejemplo, puedes redirigir al usuario a una p·gina de acceso denegado.
					throw new GenericException( CDT_SECURE_MSG_PERMISSION_DENIED);
				}
			}
			$value = trim($_GET[$name]);
			$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
			$value = strip_tags($value);
			$value = substr($value, 0, 255);
            if ($filter) {
                $inputFilter = new InputFilter();
                $value = $inputFilter->process($value);
                if ($encode){
					
					// Escapar datos de salida para prevenir XSS utilizando json_encode
					//$value = json_encode($value, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
					$value = InputFilter::urlEncode($value);
				}
                    
				
            }
            else
                $value = addslashes(self::limpiarCadena($value));
        }

        if (($value === false) || ($value === 0))
            return $value;

        if (empty($value))
            $value = $default;
        return $value;
    }


	/*
     * Replacer for FILTER_SANITIZE_STRING deprecated with PHP 8.1
     */
    public static function filter_string_polyfill(string $string): string {
        
        $str = preg_replace('/\x00|<[^>]*>?/', '', $string);
        return str_replace(["'", '"'], ["'", '"'], $str);

        
    }
    // end of filter_string_polyfill()

    /**
     * obtiene el valor del parÔøΩmetro del request x POST.
     * @param string $name nombre del parÔøΩmetro
     * @param string $default valor por default en caso de no estar seteado
     * @param $filter si debe aplicarse el inputFilter
     * @param $encode si debe aplicarse el encode al valor.
     * @return param value
     */
    public static function getParamPOST($name, $default='', $filter = true, $encode = false) {
       
		CdtUtils::log('POST parametro nombre: '.$name);
		$value='';
		if (isset($_POST [$name])) {
			CdtUtils::log('POST parametro valor: '.$_POST [$name]);
			//$value = filter_var($_POST[$name], FILTER_SANITIZE_STRING);
			$value = self::filter_string_polyfill($_POST[$name]);
            if ($filter) {
                $inputFilter = new InputFilter();
                $value = $inputFilter->process($value);
                if ($encode)
                    $value = InputFilter::urlEncode($value);
            }
            else
                $value = addslashes(self::limpiarCadena($value));
        }

        if (($value === false) || ($value === 0) || ($value === "0"))
            return $value;

        if (empty($value))
            $value = $default;


		CdtUtils::log('POST parametro devolucion: '.$value);
        return $value;
    }

    public static function getParamFILES($name, $default='') {
        if (isset($_FILES[$name])) {
            $value = $_FILES[$name];
        }
        if (empty($value))
            $value = $default;
        return $value;
    }

    /**
     * evalÔøΩa si la cadena $str comienza con la cadena $strPattern
     * @param string $str
     * @param strnig $strPattern
     * @return boolean
     */
    public static function match($str, $strPattern) {

        $str = str_replace('/', '', $str);
        $strPattern = str_replace('/', '', $strPattern);

        if (strlen($str) >= strlen($strPattern)) {

            $strSub = substr($str, 0, strlen($strPattern));

            return ($strSub == $strPattern);
        }
        return false;
    }

    /*
     * retorna la acciÔøΩn que se estÔøΩ ejecutando.
     */

    public static function getCurrentAction() {
        $inputFilter = new InputFilter();

        if (isset($_GET ['action'])) {
            $action = $inputFilter->process($_GET['action']);
        } else {

            if (isset($_GET ['accion']))
                $action = $inputFilter->process($_GET['accion']);
            else
                $action = 'page_not_found';
        }

        return $action;
    }

    public static function sendMailPop($nameTo, $to, $subject, $msg) {


        require_once(CDT_EXTERNAL_LIB_PATH . "mailer/class.phpmailer.php");
        require_once(CDT_EXTERNAL_LIB_PATH . "mailer/class.smtp.php");


        //para que no de la salida del mailer.
        ob_start();

        $mail = new PHPMailer();

        $mail->From = CDT_POP_MAIL_FROM;
        $mail->FromName = CDT_POP_MAIL_FROM_NAME;
        $mail->Host = CDT_POP_MAIL_HOST;
        $mail->Mailer = CDT_POP_MAIL_MAILER;
        $mail->Username = CDT_POP_MAIL_USERNAME;
        $mail->Password = CDT_POP_MAIL_PASSWORD;
        if( defined("CDT_POP_MAIL_SMTP_AUTH") ){
        	if( CDT_POP_MAIL_SMTP_AUTH )
        	$mail->SMTPAuth = true;
        }else
        	$mail->SMTPAuth = true;
        
        $mail->Subject = $subject;

        $body = $msg;

        $mail->Body = $body;
        $mail->AltBody = $body;

        $mail->AddAddress($to, $nameTo);

        if (!$mail->Send())
            throw new GenericException("Ocurri√≥ un error en el env√≠o del mail a $nameTo <$to>" . $mail->ErrorInfo);

        // Clear all addresses and attachments for next loop
        $mail->ClearAddresses();
        $mail->ClearAttachments();

        //para que no de la salida del mailer.
        ob_end_clean();
    }

	public static function logObject($object){
		ob_start();
	    var_dump($object);
	    CdtUtils::log(ob_get_clean());
	    ob_end_clean();
	}

    public static function sendMail($nameTo, $to, $subject, $msg, $from=CDT_POP_MAIL_FROM) {

    	//para tests redireccionamos el env√≠o de emails.
    	if( CDT_TEST_MODE ){
    		$to = CDT_MAIL_TO_IN_TEST_MODE;
    		$nameTo = "Test Mode";
    	}
    	
        if (CDT_MAIL_ENVIO_POP)
            self::sendMailPop($nameTo, $to, $subject, $msg);
        else {

            //para que no de la salida del mailer.
            ob_start();

            $to2 = $nameTo . " <" . $to . ">";
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
            $headers .= "From: " . $from;

            if (!mail($to2, $subject, $msg, $headers)){
                self::log_error("Ocurri√≥ un error en el env√≠o del mail a $to2") ;
            	throw new GenericException("Ocurri√≥ un error en el env√≠o del mail a $to2");
            }
            //para que no de la salida del mailer.
            ob_end_clean();
        }
    }

    static function textoRadom($length = 8) {
        $string = "";
        $possible = "0123456789abcdfghjkmnpqrstvwxyz";
        $i = 0;
        while ($i < $length) {
            $char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);
            $string .= $char;
            $i++;
        }
        return $string;
    }

    static function _log($str, $_Log) {
        $dt = date('Y-m-d H:i:s');
        fputs($_Log, $dt . " --> " . $str . "\n");
    }

    static function existObjectComparator($array, $i, $comparator) {
        foreach ($array as $item) {
            if ($comparator->equals($item, $i)) {
                return true;
            }
        }
        return false;
    }

    static function getObjectComparator($array, $i, $comparator) {
        foreach ($array as $item) {
            if ($comparator->equals($item, $i)) {
                return $item;
            }
        }
        return false;
    }

    /**
     * nos dice si hay un mensaje asociado al request.
     * @return true|false
     */
    public static function hasRequestInfo() {
        //return (self::getParam("info_code") != "" || self::getParam("info_msg") != "");
        $data = self::getParamSESSION(APP_NAME, false, false);
        if( empty ($data) )
        	return false;
        
        	
    	$info["code"] = (isset($data["info_code"]))?$data["info_code"]:'';
        $info["msg"] = (isset($data["info_msg"]))?$data["info_msg"]:'';
        
        return ( !empty($info["code"]) || !empty($info["msg"]));
    }

    /**
     * retorna el mensaje de info asociado al request.
     * @return array(code, msg)
     */
    public static function getRequestInfo() {

    	/*
        $info["code"] = self::getParam("info_code", false, false);
        $info["msg"] = self::getParam("info_msg", false, false);
		*/
    	$data = self::getParamSESSION(APP_NAME, false, false);
    	$info["code"] = $data["info_code"];
        $info["msg"] = $data["info_msg"];
        return $info;
    }

    public static function cleanRequestInfo() {

    	/*
        unset($_GET["info_code"]);
        unset($_GET["info_msg"]);
        */
    	unset($_SESSION[APP_NAME]["info_code"]);
        unset($_SESSION[APP_NAME]["info_msg"]);
    }

    /**
     * asocia un error al request.
     * @return array(code, msg)
     */
    public static function setRequestInfo($code, $msg) {

    	/*
        $_GET["info_code"] = $code;
        $_GET["info_msg"] = $msg;
        */
    	
        $_SESSION[APP_NAME]["info_code"] = $code;
        $_SESSION[APP_NAME]["info_msg"] = $msg;
    }

    //verifica si la variable de entrada es booleana
    static function validarBooleano($entrada) {
        //$opciones=array('1','0','true','false','si', 'no','verdadero', 'false');
        $valores = array('1' => '1', '0' => '0', 'true' => '1', 'false' => '0', 'si' => '1', 'no' => '0', 'verdadero' => '1', 'falso' => '0');
        $entrada = strtolower($entrada);
        if (array_key_exists($entrada, $valores)) {
            $salida = $valores[$entrada];
        }
        else
            $salida = 2; //el 2 lo tomo como falso;
 		return ($salida);
    }
    
 	/**
     * parsea los codigos de error.
     * @return string
     */
    public static function getMessage(GenericException $ex) {
		$message = '';
    	switch($ex->getCode())
		{
			case '1451':
				$message = CDT_SECURE_MSG_EXCEPTION_CANNOT_DELETE_ITEMS_RELATED;
				break;
			case '1062':
				$message = CDT_SECURE_MSG_EXCEPTION_DUPLICATE_DATA;
				break;
			default:
				$message=$ex->getMessage();
		}
        return $message;
   	 }
   	 
	public static function encodeValue($value){
		if( CDT_UI_UTF8_ENCODE ){
			if ($value !== null) {
				//return utf8_encode($value);
				return mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
			}
		}
			
		else return $value;
	}
   	 
	public static function decodeValue($value){
		if( CDT_UI_UTF8_ENCODE ){
			if ($value !== null) {
				//return utf8_decode($value);
				return iconv("UTF-8", "ISO-8859-1", $value);
			}
		}
			
		else return $value;
	}
	
	public static function limpiarCadena($valor)

	{

        $valor = str_ireplace("SELECT","",$valor);

        $valor = str_ireplace("COPY","",$valor);

        $valor = str_ireplace("DELETE","",$valor);

        $valor = str_ireplace("DROP","",$valor);

        $valor = str_ireplace("DUMP","",$valor);

        $valor = str_ireplace(" OR ","",$valor);

        $valor = str_ireplace("%","",$valor);

        $valor = str_ireplace("LIKE","",$valor);

        $valor = str_ireplace("ñ","",$valor);

        $valor = str_ireplace("^","",$valor);

        $valor = str_ireplace("[","",$valor);

        $valor = str_ireplace("]","",$valor);

        $valor = str_ireplace("\\","",$valor);

        $valor = str_ireplace("!","",$valor);

        $valor = str_ireplace("°","",$valor);

        $valor = str_ireplace("?","",$valor);

        $valor = str_ireplace("=","",$valor);

        $valor = str_ireplace("&","",$valor);

        return $valor;

	}

	

}