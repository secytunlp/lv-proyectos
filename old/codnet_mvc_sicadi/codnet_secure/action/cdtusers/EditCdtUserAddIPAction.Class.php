<?php 

/**
 * Acciín para agregar una IP en la edición de un CdtUser.
 * 
 * @author bernardo
 * @since 25-02-2013
 * 
 */
class EditCdtUserAddIPAction extends CdtOutputAction {

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
    	
    	$max = false;
    	if( count($ips) > 5 ){
    	
    		$response["error"] = "No puede configurar m&aacute;s de 5 IPs";
    		$max = true;
    	}
    	
    	if( !$max && !empty($ip) ){
    	
    		$exist = false;
    		foreach ($ips as $value) {
    			
    			if( $value == $ip ){
    				$exist = true;
    				break;
    			}
    				
    		}
    		if( !$exist ){
    			$ips[] = $ip;
    			$_SESSION["cdtuser_ips"] = serialize($ips);
    			
    		}else{
    			$msg = CDT_SECURE_MSG_CDTUSER_IP_ALREADY_EXISTS;
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
