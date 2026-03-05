<?php

/**
 * se define el handler para manejar los errores de php.
 *
 * @author bernardo
 * @since 08-09-2011
 *
 */


// Turn off all error reporting
//error_reporting(0);

// Report simple running errors
//error_reporting(E_ERROR | E_WARNING | E_PARSE);

// Reporting E_NOTICE can be good too (to report uninitialized
// variables or catch variable name misspellings ...)
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

// Report all errors except E_NOTICE
// This is the default value set in php.ini
//error_reporting(E_ALL ^ E_NOTICE);

// Report all PHP errors (see changelog)
//error_reporting(E_ALL);

function error_level_tostring($intval, $separator){
	$errorlevels = array(
	2047 => 'E_ALL',
	1024 => 'E_USER_NOTICE',
	512 => 'E_USER_WARNING',
	256 => 'E_USER_ERROR',
	128 => 'E_COMPILE_WARNING',
	64 => 'E_COMPILE_ERROR',
	32 => 'E_CORE_WARNING',
	16 => 'E_CORE_ERROR',
	8 => 'E_NOTICE',
	4 => 'E_PARSE',
	2 => 'E_WARNING',
	1 => 'E_ERROR');
	$result = '';
	foreach($errorlevels as $number => $name)
	{
		if (($intval & $number) == $number) {
			$result .= ($result != '' ? $separator : '').$name; }
	}
	return $result;
}

function errorHandler($errno, $errstr, $errfile, $errline) {

	if( $errno!=8){
		echo "$errno  $errstr $errfile  $errline <br />";

		
		$msg  = error_level_tostring($errno, "|");
		$msg .= ": " . $errstr;
		CdtUtils::log_error( $msg );
	
		$msg = "file: " . $errfile;
		CdtUtils::log_error( $msg );
	
		$msg = "line: " . $errline;
		CdtUtils::log_error( $msg );
	}
	
	return true;
}


if( CDT_ERROR_HANDLER ){
	
	error_reporting(E_ALL);
	set_error_handler('errorHandler');
}

?>