<?php 

/** 
 * Manager para CdtUser
 *  
 * @author codnet archetype builder
 * @since 26-10-2011
 */ 
class CdtUserManager implements ICdtList{ 

	/**
	 * dao para CdtUser
	 * @var ICdtUserDAO
	 */
	private $cdtUserDAO;
	
	/**
	 * dao para CdtFunction
	 * @var ICdtFunctionDAO
	 */
	private $cdtFunctionDAO;
	
	public function __construct(){
		
		$this->setCdtUserDAO( CdtSecureFactoryDAO::getCdtUserDAO() );
		$this->setCdtFunctionDAO( CdtSecureFactoryDAO::getCdtFunctionDAO() );
	}	
	

	/**
	 * se recupera un usuario por nombre y password.
	 * @param string $username nombre de usuairo
	 * @param string $password clave del usuario
	 * @return CdtUser
	 */
	public function getUserByUsernamePassword($username, $password, $ip=''){
		
		//recuperamos el usuario por su nombre.
		$oUser = $this->getUserByUsername( $username );
		
		//si está limitado por ip, hacemos el control.
		$ipsStr = $oUser->getDs_ips();
		if(!empty($ipsStr)){
			$ips = explode(",", $ipsStr);
			$exists = false;
    		foreach ($ips as $value) {
    			if( $value == $ip ){
    				$exists = true;
    			}
    		}
    		if( !$exists ){
    			throw new GenericException( CDT_SECURE_MSG_INVALID_IP . $_SERVER[ 'REMOTE_ADDR']);
    		}
		}
		
		//vemos si está bloqueado (dado por la cantidad de intentos)
		if( $oUser->getNu_attemps() == CDT_SECURE_LOGIN_MAX_ATTEMPS )
			throw new GenericException( CDT_SECURE_MSG_USER_BLOCKED );
			
		//vemos si coincide la clave ingresada.
		$password = md5 ( $password );
		if( $password != $oUser->getDs_password() ){
			
			//incrementamos los intentos del usuario
			$oUser->setNu_attemps( $oUser->getNu_attemps() + 1 );
			$this->updateAttemps($oUser);
			$chances = CDT_SECURE_LOGIN_MAX_ATTEMPS - $oUser->getNu_attemps();
			
			if( $chances == 1 )
				$msg = CDT_SECURE_MSG_INVALID_PASSWORD_LAST_CHANCE;
			elseif( $chances == 0 )
				$msg = CDT_SECURE_MSG_USER_BLOCKED ;
			else		 
				$msg = CdtFormatUtils::formatMessage( CDT_SECURE_MSG_INVALID_PASSWORD, array($chances) );
			throw new GenericException( $msg );
		}
		
		//buscamos las funciones que puede realizar el usuario.
		$oUser->setFunctions ( $this->getCdtFunctionDAO()->getCdtUserFunctions( $oUser ) ) ;

		//reseteamos los intentos del usuario
		$oUser->setNu_attemps( 0 );
		$this->updateAttemps($oUser);
		
		
		return $oUser;
	}

	/**
	 * se recupera un usuario por nombre.
	 * @param string $username nombre de usuairo
	 * @return CdtUser
	 */
	public function getUserByUsername( $username ){
				
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('ds_username', $username, '=', new CdtCriteriaFormatStringValue());
		
		$oUser = $this->getCdtUserDAO()->getCdtUserWithUserGroup( $oCriteria );

		if( $oUser == null )
			throw new GenericException( CDT_SECURE_MSG_INVALID_USER );
			
		return $oUser;
	}

	/**
	 * se recupera un usuario por email.
	 * @param string $ds_email email del usuario
	 * @return CdtUser
	 */
	public function getUserByEmail( $ds_email ){
				
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('ds_email', $ds_email, '=', new CdtCriteriaFormatStringValue());
		
		$oUser = $this->getCdtUserDAO()->getCdtUserWithUserGroup( $oCriteria );

		if( $oUser == null )
			throw new GenericException( CDT_SECURE_MSG_INVALID_USER );
			
		return $oUser;
	}
	
	/**
	 * se agrega la nueva entity
	 * @param CdtUser $oCdtUser entity a agregar.
	 */
	public function addCdtUser(CdtUser $oCdtUser, $sendEmail=false, $subject="", XTemplate $xtpl=null) {
		 
		//validaciones;
		$this->validateNewUser( $oCdtUser->getDs_username(), $oCdtUser->getDs_email() );
		
		//generamos la clave
		$newPassword =  CdtUtils::textoRadom(8) ;
		$oCdtUser->setDs_password ( md5( $newPassword ) );
		
		//persistir en la bbdd.
		$this->getCdtUserDAO()->addCdtUser($oCdtUser);
		
		//enviamos el email al nuevo usuario.
		$emailTo = $oCdtUser->getDs_email();
		if( $sendEmail && !empty( $emailTo ) ){
			
			$nameTo = $oCdtUser->getDs_name();
			
			//template
			if( empty( $xtpl)  )
				$xtpl = new XTemplate( CDT_SECURE_TEMPLATE_MAIL_NEW_USER );
			
			//armamos el email.
			$bodyEmail = $this->buildNewUserEmail($oCdtUser, $newPassword, $xtpl );
			
			//subject
			if(empty($subject))
        		$subject = CDT_SECURE_MSG_NEW_USER_MAIL_SUBJECT;
        
        	//enviamos el mail.
			CdtUtils::sendMail($nameTo, $emailTo, $subject, $bodyEmail);
		}
			
		return $newPassword;
	}

	private function buildNewUserEmail( CdtUser $oUser, $newPassword, XTemplate $xtpl ){
    	
        $xtpl->assign('WEB_PATH', WEB_PATH);
        $xtpl->assign('ds_name', $oUser->getDs_name());
    	$xtpl->assign('ds_username', $oUser->getDs_username());
    	$xtpl->assign('ds_password', $newPassword );
    	
        $xtpl->parse('main');
        return $xtpl->text('main');
    }

	/**
	 * se modifica la entity
	 * @param CdtUser $oCdtUser entity a modificar.
	 */
	public function updateCdtUser(CdtUser $oCdtUser) { 
		//TODO validaciones; 
		//persistir en la bbdd.
		$this->getCdtUserDAO()->updateCdtUser($oCdtUser);
		
	}

	/**
	 * se modifican los attemps del user.
	 * @param CdtUser $oCdtUser entity a modificar.
	 */
	public function updateAttemps(CdtUser $oCdtUser) { 
		//persistir en la bbdd.
		$this->getCdtUserDAO()->updateAttemps($oCdtUser);
		
	}
	
	/**
	 * se elimina la entity
	 * @param int identificador de la entity a eliminar.
	 */
	public function deleteCdtUser($id) { 
		
		//se puede eliminar m�ltiple, as� que $id puede ser una colecci�n de ids.
		$ids = explode( ",", $id);
		
		foreach ($ids as $next) {

			$oCdtUser = new CdtUser();
			$oCdtUser->setCd_user($next);
			$this->getCdtUserDAO()->deleteCdtUser($oCdtUser);
		}
		
	}

	
	/**
	 * se obtiene una colecci�n de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return ItemCollection[CdtUser]
	 */
	public function getCdtUsers(CdtSearchCriteria $oCriteria) { 
		return $this->getCdtUserDAO()->getCdtUsers($oCriteria); 
	}


	/**
	 * se obtiene la cantidad de entities dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return int
	 */
	public function getCdtUsersCount(CdtSearchCriteria $oCriteria) { 
		return $this->getCdtUserDAO()->getCdtUsersCount($oCriteria); 
	}


	/**
	 * se obtiene un entity dado el filtro de b�squeda
	 * @param CdtSearchCriteria $oCriteria filtro de b�squeda.
	 * @return CdtUser
	 */
	public function getCdtUser(CdtSearchCriteria $oCriteria) { 
		return $this->getCdtUserDAO()->getCdtUser($oCriteria); 
	}
	
	public function getCdtUserWithUserGroup( CdtSearchCriteria $oCriteria ) {
		return $this->getCdtUserDAO()->getCdtUserWithUserGroup($oCriteria);		
	}

	//	interface ICdtList

	
	/**
	 * (non-PHPdoc)
	 * @see ICdtList::getEntities();
	 */
	public function getEntities( CdtSearchCriteria $oCriteria) { 
		return $this->getCdtUsers($oCriteria); 
	}

	
	/**
	 * (non-PHPdoc)
	 * @see ICdtList::getEntitiesCount();
	 */
	public function getEntitiesCount ( CdtSearchCriteria $oCriteria ) { 
		return $this->getCdtUsersCount($oCriteria); 
	}


	//TODO
	
	
	//se recupera un usuario dado su email.
	//si no existe, se da de alta.
	public function createAndGetUsuario( $ds_nomusuario){
		
		try{
			
			$oUsuario = $this->getUsuarioPorNombre( $ds_nomusuario );
					
		}catch(GenericException $ex){

			//no existe, lo creamos.
			$oUsuario = new Usuario();
			$aux = explode( '@', $ds_nomusuario);
			$oUsuario->setDs_apynom( $aux[0] );
			$oUsuario->setDs_mail( $ds_nomusuario );
			$oUsuario->setDs_password( $ds_nomusuario );
			$oUsuario->setDs_nomusuario( $ds_nomusuario );
			//setear el perfil por default.
			$oPerfil = new Perfil();
			$oPerfil->setCd_perfil( CDT_SEGURIDAD_PERFIL_DEFAULT_ID );
			$oPerfil = PerfilQuery::getPerfilPorId( $oPerfil );
			$oUsuario->setPerfil( $oPerfil );
				
			$this->agregarUsuario( $oUsuario );
			
			$oUsuario = $this->getUsuarioPorNombre( $ds_nomusuario );
		}
		
		return $oUsuario;
	}

	

	public function signup( CdtUser $oUser ){
				
		//chequeamos el captcha.
		//TODO ver c�mo mejorarlo.
		
		include("libs/captcha/securimage.php");
		$img = new Securimage();
		$valid = $img->check(CdtUtils::getParamPOST('captcha'));
		if(!$valid)
			throw new CaptchaException();
		
		CdtUtils::log_debug( "signup 1 ");
		
		//creamos la registraci�n
		$oRegistration = new CdtRegistration();
		
		$oRegistration->setDs_username( $oUser->getDs_username() );
		$oRegistration->setDs_password( $oUser->getDs_password() ); 
		$oRegistration->setDs_email( $oUser->getDs_email() );
		
		CdtUtils::log_debug( "signup 2 ");
		
		$oManager = new CdtRegistrationManager();
		$oManager->addCdtRegistration( $oRegistration );
		
				
	}	

	public function activateRegistration( $ds_activationCode ){

		$oRegistrationManager = new CdtRegistrationManager();
		
		//buscamos la registraci�n
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('ds_activationcode', $ds_activationCode, "=", new CdtCriteriaFormatStringValue());
		
		$oRegistration = $oRegistrationManager->getCdtRegistration( $oCriteria ); 
		if($oRegistration==null || $oRegistration->getCd_registration()==0){
			throw new GenericException( CDT_SECURE_MSG_ACTIVATION_CODE_INVALID );			
		}
		
		//vemos si ya expir�
		$dt_expiredTime = $oRegistration->getDt_date();
		$dt_expiredTime= date("Ymd", strtotime("$dt_expiredTime + 30 days"));
		$dt_date = date("Ymd");
		if($dt_expiredTime < $dt_date ){
			throw new GenericException( CDT_SECURE_MSG_ACTIVATION_CODE_EXPIRED );			
		}
		
		$oUser = $oRegistration->createCdtUser();
		
		//setear el usergroup por default.
		$oUserGroupManager = new CdtUserGroupManager();
		$oUserGroup = $oUserGroupManager->getCdtUserGroupById( CDT_SECURE_USERGROUP_DEFAULT_ID );
		
		$oUser->setCdtUserGroup( $oUserGroup );
		
		//persistir el usuario en la bbdd.
		$this->addCdtUser( $oUser );		
		
		//borrar la registraci�n.
		$oRegistrationManager->deleteCdtRegistration( $oRegistration->getCd_registration() );
		
		
		//TODO login del usuario.
		//$this->login( $oUsuario->getDs_nomusuario(), $oUsuario->getDs_password());
		
	}	
	
	/**
	 * se le env�a una nueva contrase�a a un usuario
	 * @param $ds_user puede ser el email o el username
	 */
	public function sendNewPassword( $ds_user, $subject="" ){
		
		//$ds_user puede ser el email o el username
		try{
			
			$oUser = $this->getUserByUsername( $ds_user );
				
		}catch (GenericException $ex){
			
			//si no existe buscamos por email.
			$oUser = $this->getUserByEmail( $ds_user );
		}
		

		//generamos la nueva clave.
		$newPassword =  CdtUtils::textoRadom(8) ;
		$oUser->setDs_password ( md5( $newPassword ) );
		
		//modificamos el usuario.
		$this->getCdtUserDAO()->updatePassword( $oUser );
		
		//enviamos el email con la nueva contrase�a.
		$to = $oUser->getDs_email();
		$nameTo = $oUser->getDs_name();
		if(!empty($namteTo))
			$nameTo = str_replace(",","", $namteTo);
		else	
			$nameTo = $oUser->getDs_username();
			
		$xtpl = new XTemplate( CDT_SECURE_TEMPLATE_MAIL_FORGOT_PASSWORD );
		$xtpl->assign('name', $nameTo);
		$xtpl->assign('password', $newPassword);
		$xtpl->parse('main');		
		$msg = $xtpl->text('main');
		
        if(empty($subject))
        	$subject = CDT_SECURE_MSG_FORGOT_PASSWORD_MAIL_SUBJECT;
        
		CdtUtils::sendMail($nameTo, $to, $subject, $msg);
		
		
	}

	/**
	 * se modifica la cuenta del usuario
	 * @param CdtUser $oCdtUser entity a modificar.
	 * @param string $ds_new_password nueva clave para el usuario..
	 */
	public function updateCdtUserProfile(CdtUser $oCdtUser, $ds_newPassword=null) { 

		$oOldUser = $this->getUserByUsername( $oCdtUser->getDs_username() );
		
		$oCdtUser->setCd_usergroup( $oOldUser->getCd_usergroup() );
		
		if(!empty($ds_newPassword)){
			
			//chequeamos la clave actual.

			$ds_oldPassword =  $oOldUser->getDs_password() ;
			$ds_password = md5 ( $oCdtUser->getDs_password() );
			$ds_newPassword =  md5( $ds_newPassword ) ;
			
			if( $ds_oldPassword != $ds_password )
				throw new GenericException( CDT_SECURE_MSG_INVALID_PASSWORD );

			$oCdtUser->setDs_password( $ds_newPassword );
			
			//actualizar la clave de usuario.
			$this->getCdtUserDAO()->updatePassword($oCdtUser);
		}
		
		//persistir en la bbdd.
		$this->getCdtUserDAO()->updateCdtUser($oCdtUser);
	}
	
	
	/**
	 * se realizan las validaciones para un nuevo usuario
	 * @param $ds_username
	 * @param $ds_email
	 * @throws GenericException
	 */
	protected function validateNewUser( $ds_username, $ds_email ){
		
		//que no exista el nombre de usuario.
		$oUser = new CdtUser();
		$oUser->setDs_username( $ds_username );
		
		if($this->getCdtUserDAO()->existUsername( $oUser )){
			CdtUtils::log_debug( "el usuario ya existe");
			throw new GenericException( CDT_SECURE_MSG_CDTUSER_DS_USERNAME_DUPLICATED );
		}
		//que no est� registrado el email para otro usuario.
		$oUser->setDs_email( $ds_email );

		if($this->getCdtUserDAO()->existEmail( $oUser ))
			throw new GenericException( CDT_SECURE_MSG_CDTUSER_DS_EMAIL_DUPLICATED );
		
		
	}	

	/**
	 * se desbloquea un usuario
	 * @param int identificador del usuario a desbloquear.
	 */
	public function unblockCdtUser($id) { 
		
		$oUser = $this->getUserById($id);
		
		//reseteamos los attemps.
		$oUser->setNu_attemps(0);
		
		//generamos una nueva password.
		$newPassword =  CdtUtils::textoRadom(8) ;
		$oUser->setDs_password ( md5( $newPassword ) );
				
		$this->getCdtUserDAO()->updatePassword($oUser);
		$this->getCdtUserDAO()->updateAttemps($oUser);
		
		//enviamos el email con la nueva contrase�a.
		$oUser->getDs_email();
		$nameTo = $oUser->getDs_name();
		if(!empty($namteTo))
			$nameTo = str_replace(",","", $namteTo);
		else	
			$nameTo = $oUser->getDs_username();
			
		$xtpl = new XTemplate( CDT_SECURE_TEMPLATE_MAIL_FORGOT_PASSWORD );
		$xtpl->assign('name', $nameTo);
		$xtpl->assign('password', $newPassword);
		$xtpl->parse('main');		
		$msg = $xtpl->text('main');
		
        if(empty($subject))
        	$subject = CDT_SECURE_MSG_FORGOT_PASSWORD_MAIL_SUBJECT;
        
		CdtUtils::sendMail($nameTo, $to, $subject, $msg);
		
		
	}

	/**
	 * se recupera un usuario por id.
	 * @param int $id id del usuairo
	 * @return CdtUser
	 */
	public function getUserById( $id ){
				
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_user', $id, '=');
		
		$oUser = $this->getCdtUserDAO()->getCdtUser( $oCriteria );

		if( $oUser == null )
			throw new GenericException( CDT_SECURE_MSG_INVALID_USER );
			
		return $oUser;
	}

	public function getCdtUserDAO()
	{
	    return $this->cdtUserDAO;
	}

	public function setCdtUserDAO($cdtUserDAO)
	{
	    $this->cdtUserDAO = $cdtUserDAO;
	}

	public function getCdtFunctionDAO()
	{
	    return $this->cdtFunctionDAO;
	}

	public function setCdtFunctionDAO($cdtFunctionDAO)
	{
	    $this->cdtFunctionDAO = $cdtFunctionDAO;
	}
}
 
?>
