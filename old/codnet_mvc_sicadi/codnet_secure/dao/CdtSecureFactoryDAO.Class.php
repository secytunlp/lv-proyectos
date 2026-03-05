<?php 

/** 
 * Factory para los DAOs de seguridad
 *  
 * @author bernardo
 * @since 24-05-2012
 */ 
class CdtSecureFactoryDAO { 


	/**
	 * dao para CdtActionFunction
	 * @return ICdtActionFunctionDAO
	 */
	public static function getCdtActionFunctionDAO() { 
		
		return CdtReflectionUtils::newInstance( CDT_ACTION_FUNCTION_DAO );

	}
	
	/**
	 * dao para CdtFunction
	 * @return ICdtFunctionDAO
	 */
	public static function getCdtFunctionDAO() { 
		
		return CdtReflectionUtils::newInstance( CDT_FUNCTION_DAO );

	}

	/**
	 * dao para CdtMenuGroup
	 * @return ICdtMenuGroupDAO
	 */
	public static function getCdtMenuGroupDAO() { 
		
		return CdtReflectionUtils::newInstance( CDT_MENU_GROUP_DAO );

	}

	/**
	 * dao para CdtMenuOption
	 * @return ICdtMenuOptionDAO
	 */
	public static function getCdtMenuOptionDAO() { 
		
		return CdtReflectionUtils::newInstance( CDT_MENU_OPTION_DAO );

	}

	
	/**
	 * dao para CdtRegistration
	 * @return ICdtRegistrationDAO
	 */
	public static function getCdtRegistrationDAO() { 
		
		return CdtReflectionUtils::newInstance( CDT_REGISTRATION_DAO );

	}

	/**
	 * dao para CdtUser
	 * @return ICdtUserDAO
	 */
	public static function getCdtUserDAO() { 
		
		return CdtReflectionUtils::newInstance( CDT_USER_DAO );

	}

	/**
	 * dao para CdtUserGroup
	 * @return ICdtUserGroupDAO
	 */
	public static function getCdtUserGroupDAO() { 
		
		return CdtReflectionUtils::newInstance( CDT_USER_GROUP_DAO );

	}

	/**
	 * dao para CdtUserGroupFunction
	 * @return ICdtUserGroupFunctionDAO
	 */
	public static function getCdtUserGroupFunctionDAO() { 
		
		return CdtReflectionUtils::newInstance( CDT_USER_GROUP_FUNCTION_DAO );

	}	
} 
?>
