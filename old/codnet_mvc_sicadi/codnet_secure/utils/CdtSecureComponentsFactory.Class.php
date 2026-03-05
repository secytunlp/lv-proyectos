<?php 

/** 
 * Factory para componentes 
 *  
 * @author codnet archetype builder
 * @since 05-11-2011
 */ 
class CdtSecureComponentsFactory { 

	
	public static function getFindObjectCdtActionFunction(CdtActionFunction $oCdtActionFunction, $inputId='cd_actionfunction_autocomplete', $required=true, $required_msg=""){
		$oFindObject = new CMPFindObject();

		$oFindObject->setInputName( $inputId );
		$oFindObject->setInputId( $inputId );
		$oFindObject->setRequestClass( 'CdtActionFunctionManager' );
		$oFindObject->setRequestMethod( 'getCdtActionFunction' );
		$oFindObject->setGridModel( 'CdtActionFunctionGridModel' );
		
		$oFindObject->setItemCode( 'cd_actionfunction' );
		$oFindObject->setItemLabel( 'cd_actionfunction' );
		$oFindObject->setItemClass( 'CdtActionFunction' );

		$oFindObject->setItem( $oCdtActionFunction );
		
		$oFindObject->setFunctionCallback('cdtactionfunction_change');
		$oFindObject->setItemAttributesCallback( 'cd_actionfunction' );
		
		$oFindObject->setObligatorio( $required );
		$oFindObject->setMsgObligatorio( $required_msg );
		
		return $oFindObject;
	}	
	
	public static function getFindObjectCdtFunction(CdtFunction $oCdtFunction, $inputId='cd_function_autocomplete', $required=true, $required_msg=""){
		$oFindObject = new CMPFindObject();

		$oFindObject->setInputName( $inputId );
		$oFindObject->setInputId( $inputId );
		$oFindObject->setRequestClass( 'CdtFunctionManager' );
		$oFindObject->setRequestMethod( 'getCdtFunction' );
		$oFindObject->setGridModel( 'CdtFunctionGridModel' );
		$oFindObject->setItemCode( 'cd_function' );
		$oFindObject->setItemLabel( 'ds_function' );
		$oFindObject->setItemClass( 'CdtFunction' );

		$oFindObject->setItem( $oCdtFunction );
		
		$oFindObject->setFunctionCallback('cdtfunction_change');
		$oFindObject->setItemAttributesCallback( 'cd_function' );
		
		$oFindObject->setObligatorio( $required );
		$oFindObject->setMsgObligatorio( $required_msg );
		
		return $oFindObject;
	}	
	
	public static function getFindObjectCdtMenuGroup(CdtMenuGroup $oCdtMenuGroup, $inputId='cd_menugroup_autocomplete', $required=true, $required_msg=""){
		$oFindObject = new CMPFindObject();

		$oFindObject->setInputName( $inputId );
		$oFindObject->setInputId( $inputId );
		$oFindObject->setRequestClass( 'CdtMenuGroupManager' );
		$oFindObject->setRequestMethod( 'getCdtMenuGroup' );
		$oFindObject->setGridModel( 'CdtMenuGroupGridModel' );
		$oFindObject->setItemCode( 'cd_menugroup' );
		$oFindObject->setItemLabel( 'ds_name' );
		$oFindObject->setItemClass( 'CdtMenuGroup' );

		$oFindObject->setItem( $oCdtMenuGroup );
		
		$oFindObject->setFunctionCallback('cdtmenugroup_change');
		$oFindObject->setItemAttributesCallback( 'cd_menugroup' );
		
		$oFindObject->setObligatorio( $required );
		$oFindObject->setMsgObligatorio( $required_msg );
		
		return $oFindObject;
	}	
	
	public static function getFindObjectCdtMenuOption(CdtMenuOption $oCdtMenuOption, $inputId='cd_menuoption_autocomplete', $required=true, $required_msg=""){
		$oFindObject = new CMPFindObject();

		$oFindObject->setInputName( $inputId );
		$oFindObject->setInputId( $inputId );
		$oFindObject->setRequestClass( 'CdtMenuOptionManager' );
		$oFindObject->setRequestMethod( 'getCdtMenuOption' );
		$oFindObject->setGridModel( 'CdtMenuOptionGridModel' );
		$oFindObject->setItemCode( 'cd_menuoption' );
		$oFindObject->setItemLabel( 'ds_menuoption' );
		$oFindObject->setItemClass( 'CdtMenuOption' );

		$oFindObject->setItem( $oCdtMenuOption );
		
		$oFindObject->setFunctionCallback('cdtmenuoption_change');
		$oFindObject->setItemAttributesCallback( 'cd_menuoption' );
		
		$oFindObject->setObligatorio( $required );
		$oFindObject->setMsgObligatorio( $required_msg );
		
		return $oFindObject;
	}	
	
	public static function getFindObjectCdtRegistration(CdtRegistration $oCdtRegistration, $inputId='cd_registration_autocomplete', $required=true, $required_msg=""){
		$oFindObject = new CMPFindObject();

		$oFindObject->setInputName( $inputId );
		$oFindObject->setInputId( $inputId );
		$oFindObject->setRequestClass( 'CdtRegistrationManager' );
		$oFindObject->setRequestMethod( 'getCdtRegistration' );
		$oFindObject->setGridModel( 'CdtRegistrationGridModel' );
		$oFindObject->setItemCode( 'cd_registration' );
		$oFindObject->setItemLabel( 'cd_registration' );
		$oFindObject->setItemClass( 'CdtRegistration' );

		$oFindObject->setItem( $oCdtRegistration );
		
		$oFindObject->setFunctionCallback('cdtregistration_change');
		$oFindObject->setItemAttributesCallback( 'cd_registration' );
		
		$oFindObject->setObligatorio( $required );
		$oFindObject->setMsgObligatorio( $required_msg );
		
		return $oFindObject;
	}	
	
	public static function getFindObjectCdtUser(CdtUser $oCdtUser, $inputId='cd_user_autocomplete', $required=true, $required_msg=""){
		$oFindObject = new CMPFindObject();

		$oFindObject->setInputName( $inputId );
		$oFindObject->setInputId( $inputId );
		$oFindObject->setRequestClass( 'CdtUserManager' );
		$oFindObject->setRequestMethod( 'getCdtUser' );
		$oFindObject->setGridModel( 'CdtUserGridModel' );
		$oFindObject->setItemCode( 'cd_user' );
		$oFindObject->setItemLabel( 'ds_username' );
		$oFindObject->setItemClass( 'CdtUser' );

		$oFindObject->setItem( $oCdtUser );
		
		$oFindObject->setFunctionCallback('cdtuser_change');
		$oFindObject->setItemAttributesCallback( 'cd_user' );
		
		$oFindObject->setObligatorio( $required );
		$oFindObject->setMsgObligatorio( $required_msg );
		
		return $oFindObject;
	}	
	
	public static function getFindObjectCdtUserGroup(CdtUserGroup $oCdtUserGroup, $inputId='cd_usergroup_autocomplete', $required=true, $required_msg=""){
		$oFindObject = new CMPFindObject();

		$oFindObject->setInputName( $inputId );
		$oFindObject->setInputId( $inputId );
		$oFindObject->setRequestClass( 'CdtUserGroupManager' );
		$oFindObject->setRequestMethod( 'getCdtUserGroup' );
		$oFindObject->setGridModel( 'CdtUserGroupGridModel' );
		$oFindObject->setItemCode( 'cd_usergroup' );
		$oFindObject->setItemLabel( 'ds_usergroup' );
		$oFindObject->setItemClass( 'CdtUserGroup' );

		$oFindObject->setItem( $oCdtUserGroup );
		
		$oFindObject->setFunctionCallback('cdtusergroup_change');
		$oFindObject->setItemAttributesCallback( 'cd_usergroup' );
		
		$oFindObject->setObligatorio( $required );
		$oFindObject->setMsgObligatorio( $required_msg );
		
		return $oFindObject;
	}	
	
	public static function getFindObjectCdtUserGroupFunction(CdtUserGroupFunction $oCdtUserGroupFunction, $inputId='cd_usergroup_function_autocomplete', $required=true, $required_msg=""){
		$oFindObject = new CMPFindObject();

		$oFindObject->setInputName( $inputId );
		$oFindObject->setInputId( $inputId );
		$oFindObject->setRequestClass( 'CdtUserGroupFunctionManager' );
		$oFindObject->setRequestMethod( 'getCdtUserGroupFunction' );
		$oFindObject->setGridModel( 'CdtUserGroupFunctionGridModel' );
		$oFindObject->setItemCode( 'cd_usergroup_function' );
		$oFindObject->setItemLabel( 'cd_usergroup_function' );
		$oFindObject->setItemClass( 'CdtUserGroupFunction' );

		$oFindObject->setItem( $oCdtUserGroupFunction );
		
		$oFindObject->setFunctionCallback('cdtusergroupfunction_change');
		$oFindObject->setItemAttributesCallback( 'cd_usergroup_function' );
		
		$oFindObject->setObligatorio( $required );
		$oFindObject->setMsgObligatorio( $required_msg );
		
		return $oFindObject;
	}	
	
} 
?>
