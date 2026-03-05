<?php 

/**
 * Acción para borrar una IP en la edición de un CdtUser.
 * 
 * @author bernardo
 * @since 25-02-2013
 * 
 */
class EditCdtUserDeleteIPAction  extends CdtOutputAction {

    /**
     * (non-PHPdoc)
     * @see CdtEditAction::getEntity();
     */
    protected function getEntity() {
        $values = array( "cd_cdtuser" => CdtUtils::getParam('cd_cdtuser'),
        				 "ip" => CdtUtils::getParam('ip')
        		);
        return $values;
    }

    protected function getLayout() {
        $oLayout = new CdtLayoutJson();
        return $oLayout;
    }

    protected function getOutputContent() {

    	$ip = CdtUtils::getParam('ip');
    	
    	$ips = unserialize($_SESSION["cdtuser_ips"]);
    	
    	$response = array();
    	
    	if( !empty($ip) ){

    		$news = array();
    		
    		$exists = false;
    		foreach ($ips as $value) {
    			
    			if( $value != $ip ){
    			
    				$news[] = $value;
    			
    			}else $exists = true;
    		}
    		
    		if( $exists ){
    			$ips = $news;
    			$_SESSION["cdtuser_ips"] = serialize($ips);
    		}else{
    			$msg = CDT_SECURE_MSG_CDTUSER_IP_NOT_EXISTS;
    			$params = array ( $ip );
    			$response["error"] = CdtFormatUtils::formatMessage( $msg, $params );
    		}
    					
    	}
    	$response["ips"] = $ips;
    	
    	return $response;
    }

    protected function getOutputTitle() {
        return "";
    }

}