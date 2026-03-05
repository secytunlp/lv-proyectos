<?php

/**
 * Encargado de renderizar la grilla 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 14-12-2011
 *
 */
class FindPopupRichGridRenderer extends RichGridRenderer{

	public function renderStyle(CMPGrid $oGrid, XTemplate $xtpl){
		parent::renderStyle($oGrid, $xtpl);
		
		$xtpl->assign('style', CDT_CMP_GRID_RICH_POPUP_STYLE_CSS );
		$xtpl->parse('main.style');
	}
	
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
		$oAction->setBl_multiple( false );
		$oAction->setDs_image( "css/smile/images/search.gif" );
		$oAction->setDs_style( "select" );
		
		$ds_callback = CdtUtils::getParam("callback_$gridId");
		
		$ds_callback = "$ds_callback($itemId);jQuery('#ui-dialog_$gridId').dialog('destroy');" ;
		
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

	
	public function renderActions( XTemplate $xtpl ){

		
	
	}
	
	public function getActionList(){
		return "cmp_findpopup";
	}	
	
}