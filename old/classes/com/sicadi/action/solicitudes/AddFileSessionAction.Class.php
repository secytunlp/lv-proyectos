<?php 

/**
 * Acción para dar de alta un archivo de solicitud.
 * El alta es sólo en sesión para ir armando la solicitud.
 * 
 * @author Marcos
 * @since 09-01-2014
 * 
 */
class AddFileSessionAction extends CdtAction{

	
	public function getVariableSessionName(){
		return "archivos";
	}
	
	public function execute(){
		$error='';
		$nuevo='';
		if(isset($_SESSION[$this->getVariableSessionName()]))
			$archivos = unserialize( $_SESSION[$this->getVariableSessionName()] );
		else 
			$archivos = array();
		foreach ($_FILES as $key => $value) {
			if ($value["size"]<=CYT_FILE_MAX_SIZE) {
                $extenciones_permitidas = CYT_EXTENSIONES_PERMITIDAS;
                $mostrar = 0;
				switch ($key) {
            		case 'ds_curriculum':
            		$nombre = CYT_LBL_SOLICITUD_A_CURRICULUM;
            		$sigla = CYT_LBL_SOLICITUD_A_CURRICULUM_SIGLA;
            		break;
            		case 'ds_resbeca':
            		$nombre = CYT_LBL_SOLICITUD_BECA_RESOLUCION;
            		$sigla = CYT_LBL_SOLICITUD_BECA_RESOLUCION_SIGLA;
            		break;
            		case 'ds_rescarrera':
            		$nombre = CYT_LBL_SOLICITUD_CARRERA_RESOLUCION;
            		$sigla = CYT_LBL_SOLICITUD_CARRERA_RESOLUCION_SIGLA;
            		break;
                    case 'ds_archivo':
                    $nombre = CYT_LBL_SOLICITUD_PROYECTOS_ARCHIVO;
                    $sigla = CYT_LBL_SOLICITUD_PROYECTOS_ARCHIVO_SIGLA;
                    break;
                    case 'ds_foto':
                        $nombre = CYT_LBL_SOLICITUD_FOTO;
                        $sigla = CYT_LBL_SOLICITUD_FOTO_SIGLA;
                        $extenciones_permitidas = CYT_EXTENSIONES_PERMITIDAS_IMG;
                        $mostrar = 1;
                        break;
                    case 'ds_informe1':
                        $nombre = CYT_LBL_SOLICITUD_A_INFORME1;
                        $sigla = CYT_LBL_SOLICITUD_A_INFORME1_SIGLA;
                        break;
                    case 'ds_informe2':
                        $nombre = CYT_LBL_SOLICITUD_A_INFORME2;
                        $sigla = CYT_LBL_SOLICITUD_A_INFORME2_SIGLA;
                        break;
                    case 'ds_informe3':
                        $nombre = CYT_LBL_SOLICITUD_A_INFORME3;
                        $sigla = CYT_LBL_SOLICITUD_A_INFORME3_SIGLA;
                        break;

            	}
				$explode_name = explode('.', $value['name']);
	            //Se valida así y no con el mime type porque este no funciona par algunos programas
	            $pos_ext = count($explode_name) - 1;
	            if (in_array(strtolower($explode_name[$pos_ext]), explode(",",$extenciones_permitidas))) {
	            	//CdtUtils::log("FILE: "   . $key.' - '.$value['name']);
	            	$dir = CYT_PATH_PDFS.'/';
					if (!file_exists($dir)) mkdir($dir, 0777); 
					$dir .= CYT_PERIODO_YEAR.'/';
					if (!file_exists($dir)) mkdir($dir, 0777); 
					$oUser = CdtSecureUtils::getUserLogged();
            		$separarCUIL = explode('-',trim($oUser->getDs_username()));
					$dir .= $separarCUIL[1].'/';
					if (!file_exists($dir)) mkdir($dir, 0777);
					
					$oCriteria = new CdtSearchCriteria();
					$oCriteria->addFilter('nu_documento', $separarCUIL[1], '=');
					
					$oDocenteManager =  CYTSecureManagerFactory::getDocenteManager();
					$oDocente = $oDocenteManager->getEntity($oCriteria);
					$ds_apellido = CYTSecureUtils::stripAccents(stripslashes(str_replace("'","_",$oDocente->getDs_apellido())));			
					$nuevo='TMP_'.$sigla.'_'.$ds_apellido.".".$explode_name[$pos_ext];
					
		     		$handle=opendir($dir);
					while ($archivo = readdir($handle))
					{
				        if ((is_file($dir.$archivo))&&((strchr($archivo,'TMP_'.$sigla.'_'))||(strchr($archivo,$sigla.'_'))))
				         {
				         	unlink($dir.$archivo);
						}
					}
					closedir($handle);
			
					
			        if (!move_uploaded_file($value['tmp_name'], $dir.$nuevo)){
						$error .='<span style="color:#FF0000; font-weight:bold">'.CYT_MSG_FILE_UPLOAD_ERROR.$nombre.'</span>';
			        }
			        else{
			        	if ($mostrar){
                            $error .= '<img style="width:100px;border-radius:50%;" src="'.$dir.$nuevo.'">';
                        }
                        else{
                            $error = '<span style="color:#009900; font-weight:bold">'.CYT_MSG_FILE_UPLOAD_EXITO.$value["name"]."(".$value["size"].")".'</span>';
                        }

			        }
					
	            }
	            else {
	            	
	            	$error .='<span style="color:#FF0000; font-weight:bold">'.CYT_MSG_FORMATO_INVALIDO.$nombre.'</span>';
	            }
			
			if ($error) {
				echo $error;
			}
			//else{
				//CdtUtils::log("FILE: "   . $key.' => '.$value);
				$value['nuevo']=$nuevo;
				$archivos[$key]=$value;
			//}
		}
		else {
	            	
            	$error .='<span style="color:#FF0000; font-weight:bold">'.$value['name'].CYT_MSG_FILE_MAX_SIZE.'</span>';
            	echo $error;
            }
		}        
		$_SESSION[$this->getVariableSessionName()] = serialize($archivos);
		//CdtUtils::logObject($_SESSION[$this->getVariableSessionName()]);
		//vamos a retornar por json los presupuestos de la solicitud.
		
		//usamos el renderer para reutilizar lo que mostramos de los presupuestos.
		

	}


	
}