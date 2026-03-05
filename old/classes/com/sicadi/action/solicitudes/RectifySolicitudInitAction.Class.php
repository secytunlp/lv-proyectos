<?php

/**
 * Acción para inicializar el contexto
 * para rechazar una solicitud
 *
 * @author Marcos
 * @since 21-03-2014
 *
 */

class RectifySolicitudInitAction extends EditEntityInitAction {

	/**
	 * (non-PHPdoc)
	 * @see classes/com/gestion/action/entities/EditEntityInitAction::getNewFormInstance()
	 */
	public function getNewFormInstance($action){
		$_SESSION[APP_NAME]['csrf_token'] = bin2hex(random_bytes(32)); 
		return new CMPRectifySolicitudForm($action);
		
	}

	/**
	 * (non-PHPdoc)
	 * @see classes/com/gestion/action/entities/EditEntityInitAction::getNewEntityInstance()
	 */
	public function getNewEntityInstance(){
		$rectifySolicitud = new DenySolicitud();
		
		$solicitud_oid = CdtUtils::getParam('id');
		$oUser = CdtSecureUtils::getUserLogged();
		if (!CdtSecureUtils::hasPermission ( $oUser, CYT_FUNCTION_ENVIO_DEFINITIVO )) {
			throw new GenericException( CDT_SECURE_MSG_PERMISSION_DENIED );
		}
			
		if (!empty( $solicitud_oid )) {
			$solicitud = new Solicitud();
			$solicitud->setOid($solicitud_oid);
			$rectifySolicitud->setSolicitud($solicitud);
		}
		
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('solicitud_oid', $solicitud_oid, '=');
		$oCriteria->addNull('fechaHasta');
		$managerSolicitudEstado =  CYTSecureManagerFactory::getSolicitudEstadoManager();
		$oSolicitudEstado = $managerSolicitudEstado->getEntity($oCriteria);
		if (($oSolicitudEstado->getEstado()->getOid()!=CYT_ESTADO_SOLICITUD_RECIBIDA)) {
			
			throw new GenericException( CYT_MSG_SOLICITUD_RECTIFICAR_PROHIBIDO);
		}
	
		return $rectifySolicitud;
	}

	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CYT_MSG_SOLICITUD_RECTIFICAR;
	}

	/**
	 * (non-PHPdoc)
	 * @see classes/com/gestion/action/entities/EditEntityInitAction::getSubmitAction()
	 */
	protected function getSubmitAction(){
		return "rectify_solicitud";
	}


}
