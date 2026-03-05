<?php

/**
 * input field date de un formulario.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-03-2013
 *
 */
class CMPFormInputDate extends CMPFormInput {

	/**
	 * para editar el mes
	 * @var boolean
	 */
	private $changeMonth;

	/**
	 * para editar el anio
	 * @var boolean
	 */
	private $changeYear;
		
	/**
	 * fecha por default.
	 * @var string.
	 */
	private $defaultDate;

	/**
	 * cantidad e meses a visualizar.
	 * @var int
	 */
	private $numberOfMonths;
	
	/**
	 * para utilizar rangos.
	 * Acá setearíamos el id del input de fecha
	 * utilizado para el máximo. 
	 * Si este valor está seteado, este input sería 
	 * utilizado como el mínimo del rango.
	 * @var string
	 */
	private $minRangeFor;
	
	/**
	 * para utilizar rangos.
	 * Acá setearíamos el id del input de fecha
	 * utilizado para el mínimo. 
	 * Si este valor está seteado, este input sería 
	 * utilizado como el máximo del rango.
	 * @var string
	 */
	private $maxRangeFor;
	
	public function __construct($id, $name, $requiredMessage="", $value="", $size=30){
	
		parent::__construct($id, $name, $requiredMessage, $value, $size);
		
		$this->setChangeMonth(true);
		$this->setChangeYear(true);
		$this->setDefaultDate("+1w");
		$this->setNumberOfMonths(3);
		
	}
	
	public function getChangeMonth()
	{
	    return $this->changeMonth;
	}

	public function setChangeMonth($changeMonth)
	{
	    $this->changeMonth = $changeMonth;
	}

	public function getChangeYear()
	{
	    return $this->changeYear;
	}

	public function setChangeYear($changeYear)
	{
	    $this->changeYear = $changeYear;
	}

	public function getDefaultDate()
	{
	    return $this->defaultDate;
	}

	public function setDefaultDate($defaultDate)
	{
	    $this->defaultDate = $defaultDate;
	}

	public function getNumberOfMonths()
	{
	    return $this->numberOfMonths;
	}

	public function setNumberOfMonths($numberOfMonths)
	{
	    $this->numberOfMonths = $numberOfMonths;
	}

	public function getMinRangeFor()
	{
	    return $this->minRangeFor;
	}

	public function setMinRangeFor($minRangeFor)
	{
	    $this->minRangeFor = $minRangeFor;
	}

	public function getMaxRangeFor()
	{
	    return $this->maxRangeFor;
	}

	public function setMaxRangeFor($maxRangeFor)
	{
	    $this->maxRangeFor = $maxRangeFor;
	}
}