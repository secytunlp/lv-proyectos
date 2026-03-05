<?php

/**
 * colabora en la creación de Fields
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-02-2013
 *
 */
class FieldBuilder{

	
	public static function buildFieldFindEntity ($value, $label, $id, $name, CMPEntityAutocomplete $autocomplete, $finderClazz, $gridClazz,$requiredMessage=""){
	
		$findEntityInput = new CMPFormFindEntity( $id, $name, $requiredMessage);
		$findEntityInput->setRenderer( new InputFindEntityRenderer() );
		$findEntityInput->setGridClazz($gridClazz);
		$findEntityInput->setFinderClazz($finderClazz);
		
		
		//autocomplete del findentity
		$autocomplete->setInputId( "autocomplete_$id");
		$autocomplete->setInputName("autocomplete_$name");
		$autocomplete->setRequiredMessage($requiredMessage);
		$findEntityInput->setInputAutocomplete( new CMPFormEntityAutocomplete( $autocomplete ) );
		
		//code del findentity
		$inputCode = new CMPFormInput( $id, $name, "", $autocomplete->getEntityCode(), 5 );
		$inputCode->setRenderer( new InputTextRenderer() );
		
		$findEntityInput->setInputCode($inputCode);
		
		
		$findEntityInput->setInputValue($value);
		
		$field = new FormField( $label, $findEntityInput);
		
		return $field;
	}
	

	public static function buildFieldTextArea( $label, $id, $requiredMessage="", $value="", $rows=10, $cols=30 ){
		$input = new CMPFormTextArea( $id, $id, $requiredMessage, $value);
		$input->setRenderer( new InputTextAreaRenderer() );
		$input->setRows($rows);
		$input->setCols($cols);
		$input->addProperty("style", "height:100px");
		$field = new FormField($label, $input);
		return $field;
	}
	
	public static function buildFieldText( $label, $id, $requiredMessage="", $value="", $size=30 ){
		$input = new CMPFormInput( $id, $id, $requiredMessage, $value, $size );
		$input->setRenderer( new InputTextRenderer() );
		$field = new FormField($label, $input);
		return $field;
	}
	
	public static function buildFieldReadOnly( $label, $id, $name="", $value="" ){
		
		if(empty($name))
			$name = $id;
		
		$input = new CMPFormInput( $id, $name, "", $value );
		$input->setRenderer( new InputReadOnlyRenderer() );
		$field = new FormField($label, $input);
		return $field;
	}

    public static function buildFieldDisabled( $label, $id, $name="", $value="" ){

        if(empty($name))
            $name = $id;

        $input = new CMPFormInput( $id, $name, "", $value );
        $input->setRenderer( new InputDisabledRenderer() );
        $field = new FormField($label, $input);
        return $field;
    }

	public static function buildFieldCheckbox( $label,  $id, $name, $isChecked=false, $requiredMessage="", $value="", $size=30 ){
		$input = new CMPFormInputCheckbox( $id, $name, $requiredMessage, $value, $size );
		$input->setIsChecked($isChecked);
		$field = new FormField($label, $input);
		return $field;
	}
	
	public static function buildFieldCheckboxes( $label, $name, $checkboxes, $requiredMessage="" ){
		$input = new CMPFormInputCheckboxes( $name, $requiredMessage );
		$input->setCheckboxes($checkboxes);
		$field = new FormField($label, $input);
		return $field;
	}

	public static function buildFieldRadio( $label, $id, $name, $isChecked=false, $value="", $requiredMessage="", $size=30 ){
		$input = new CMPFormInputRadio( $id, $name, $requiredMessage, $value, $size );
		$input->addProperty("name", $name);
		$input->setIsChecked($isChecked);
		$field = new FormField($label, $input);
		return $field;
	}
	
	public static function buildFieldRadios( $label, $name, $radios, $requiredMessage="" ){
		$input = new CMPFormInputRadios( $name, $requiredMessage );
		$input->setRadios($radios);
		$field = new FormField($label, $input);
		return $field;
	}
	
	public static function buildFieldDate( $label, $id, $requiredMessage="", $format="d/m/Y", $value="", $size=10 ){
		$input = new CMPFormInputDate( $id, $id, $requiredMessage, $value, $size );
		$input->setRenderer( new InputDateRenderer() );
		$input->setFormat($format);
		$field = new FormField($label, $input);
		return $field;
	}
	
	public static function buildFieldDateReadOnly( $label, $id, $format="d/m/Y", $value="" ){
		$input = new CMPFormInput( $id, $id, "", $value );
		$input->setRenderer( new InputDateReadOnlyRenderer() );
		$input->setFormat($format);
		$field = new FormField($label, $input);
		return $field;
	}
	
	public static function buildFieldNumber( $label, $id, $requiredMessage="", $value="", $size=10, $invalidFormatMessage=CDT_CMP_FORM_MSG_INVALID_NUMBER ){
		$input = new CMPFormInput( $id, $id, $requiredMessage, $value, $size );
		$input->setRenderer( new InputNumberRenderer() );
		$input->setInvalidFormatMessage($invalidFormatMessage);
		$field = new FormField($label, $input);
		return $field;
	}
	
	public static function buildFieldEmail( $label, $id, $requiredMessage="", $value="", $size=30, $invalidFormatMessage=CDT_CMP_FORM_MSG_INVALID_EMAIL ){
		$input = new CMPFormInput( $id, $id, $requiredMessage, $value, $size );
		$input->setRenderer( new InputEmailRenderer() );
		$input->setInvalidFormatMessage($invalidFormatMessage);
		$field = new FormField($label, $input);
		return $field;
	}
	
	public static function buildFieldSelect( $label, $id, $options, $requiredMessage="", $value="", $size=1, $emptyLabel="", $inputId="" ){
		if ($inputId) {
			$input = new CMPFormSelect( $inputId, $id, $requiredMessage, $value, $size );
		}
		else 
			$input = new CMPFormSelect( $id, $id, $requiredMessage, $value, $size );
		$input->setOptions($options);
		$input->setEmptyOptionLabel($emptyLabel);
		$field = new FormField($label, $input);
		return $field;
	}

	public static function buildFieldFindObject( $label, CMPFindObject $findObject ){
		$input = new CMPFormFindObject($findObject);
		$field = new FormField($label, $input);
		return $field;
	}
	
	public static function buildFieldEntityAutocomplete( $label, CMPEntityAutocomplete $autocomplete, $id, $requiredMessage="", $value="", $size=30 ){
		
		$autocomplete->setInputId( $id);
		$autocomplete->setInputName($id);
		$autocomplete->setRequiredMessage($requiredMessage);
		$autocomplete->setFunctionCallback( "autocomplete_callback_" . $id );
		$input = new CMPFormEntityAutocomplete($autocomplete);
		$field = new FormField($label, $input);
		return $field;
	}
	
	public static function buildInputHidden( $id, $value, $inputId="" ){
		if ($inputId) {
			$input = new CMPFormInputHidden( $inputId, $id, $value );
		}
		else $input = new CMPFormInputHidden( $id, $id, $value );
		$input->setRenderer( new InputTextRenderer() );
		return $input;
	}
	
	public static function buildFieldPassword( $label, $id, $requiredMessage="", $value="", $size=30 ){
		$input = new CMPFormInputPassword( $id, $id, $requiredMessage, $value, $size );
		$input->setRenderer( new InputTextRenderer() );
		$field = new FormField($label, $input);
		return $field;
	}
	
}