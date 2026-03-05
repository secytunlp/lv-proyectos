<?php

/**
 * Encargado de renderizar la grilla 
 *
 * @author marcos
 * @since 31-10-2012
 *
 */
class FindPopupMultipleRichGridRenderer extends RichGridRenderer{

	public function renderRowActions( $item, XTemplate $xtpl ){

		$model = $this->getGrid()->getGridModel();
		$gridId = $this->getGrid()->getId();
		
		if(!empty($item))
				$itemId  = $model->getEntityId( $item );
			 else
			 	$itemId = "getSelected_$gridId()";
			 	
		$oAction = new GridActionModel();
		$oAction->setDs_action( "action" );
		$oAction->setDs_name( "name" );
		$oAction->setDs_label( CDT_SECURE_LBL_SELECT );
		$oAction->setBl_multiple( true );
		
		$oAction->setDs_style( "select" );
		
		$ds_callback = CdtUtils::getParam("callback_$gridId");
		
		$ds_callback = "$ds_callback($itemId);jQuery('#list_popup_1').dialog('destroy');" ;//OJO!!! está harcodeada la grilla que lo llama
		
		$oAction->setDs_callback( $ds_callback  );
		$oAction->setBl_popUp( false );
			 	
		$this->renderRowAction( $xtpl, $oAction, $itemId);
		
	}

	public function renderHidden( XTemplate $xtpl ){
	
		parent::renderHidden( $xtpl );

		$gridId = $this->getGrid()->getId();
    	
		$xtpl->assign( "name", "callback_$gridId" );
		$xtpl->assign( "id", "callback_$gridId" );
		$xtpl->assign( "value", CdtUtils::getParam("callback_$gridId") );
		$xtpl->parse( 'main.extra_hidden' );
		
	}
	
	public function renderActions( XTemplate $xtpl ){}
	

	
}