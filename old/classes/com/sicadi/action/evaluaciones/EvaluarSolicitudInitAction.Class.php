<?php

/**
 * AcciÃ³n para inicializar el contexto
 * para editar una evaluaciÃ³n.
 *
 * @author Marcos
 * @since 21-05-2014
 *
 */

class EvaluarSolicitudInitAction extends UpdateEntityInitAction {


	protected function getEntity(){

		//$entity = parent::getEntity();
		$oUser = CdtSecureUtils::getUserLogged();

		$solicitud_oid = CdtUtils::getParam('id');

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_solicitud', $solicitud_oid, '=');
		$oCriteria->addFilter('cd_usuario', $oUser->getCd_user(), '=');
		$oCriteria->addNull('fechaHasta');
		$managerEvaluacion =  ManagerFactory::getEvaluacionManager();
		$entity = $managerEvaluacion->getEntity($oCriteria);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('evaluacion_oid', $entity->getOid(), '=');
		$oCriteria->addNull('fechaHasta');
		$managerEvaluacionEstado =  CYTSecureManagerFactory::getEvaluacionEstadoManager();
		$oEvaluacionEstado = $managerEvaluacionEstado->getEntity($oCriteria);
		if (($oEvaluacionEstado->getEstado()->getOid()==CYT_ESTADO_SOLICITUD_EVALUADA)) {

			throw new GenericException( CYT_MSG_EVALUACION_MODIFICAR_PROHIBIDO);
		}
		if (($oEvaluacionEstado->getEstado()->getOid()==CYT_ESTADO_SOLICITUD_RECIBIDA)) {

			throw new GenericException( CYT_MSG_EVALUACION_EVALUAR_PROHIBIDO);
		}

		$oSolicitudManager =  ManagerFactory::getSolicitudManager();
		$oSolicitud = $oSolicitudManager->getObjectByCode($solicitud_oid);

		$oDocenteManager =  CYTSecureManagerFactory::getDocenteManager();
		$oDocente = $oDocenteManager->getObjectByCode($oSolicitud->getDocente()->getOid());

		$entity->setDs_investigador($oDocente->getDs_apellido().', '.$oDocente->getDs_nombre());

		$oFacultadManager =  CYTSecureManagerFactory::getFacultadManager();
		$oFacultad = $oFacultadManager->getObjectByCode($oSolicitud->getFacultadplanilla()->getOid());

		$entity->setDs_facultad($oFacultad->getDs_facultad());

		$oPeriodoManager = CYTSecureManagerFactory::getPeriodoManager();
		$oPerioActual = $oPeriodoManager->getPeriodoActual(CYT_PERIODO_YEAR);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_periodo', $oPerioActual->getOid(), '=');
		$managerModeloPlanilla =  ManagerFactory::getModeloPlanillaManager();
		$oModeloPlanilla = $managerModeloPlanilla->getEntity($oCriteria);

		$entity->setModeloPlanilla_oid($oModeloPlanilla->getOid());
		$entity->setNu_max($oModeloPlanilla->getNu_max());

		$html = '';


		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $oModeloPlanilla->getOid(), '=');
		$oCriteria->addOrder('nu_max','DESC');
		$managerPosgradoMaximo =  ManagerFactory::getPosgradoMaximoManager();
		$oPosgradosMaximos = $managerPosgradoMaximo->getEntities($oCriteria);

		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$managerPuntajePosgrado =  ManagerFactory::getPuntajePosgradoManager();
		$oPuntajePosgrado = $managerPuntajePosgrado->getEntity($oCriteria);

		$html .='<table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666 ;margin-bottom:5px">
				 <tr style="border-style: solid; border-width: 1px; border-color: #666">
                      <Td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;">A</td> <td style="background-color: #eee;color:#333;"
                      colspan="6"><div align="center"><strong>ANTECEDENTES  ACAD&Eacute;MICOS DEL SOLICITANTE</strong></div></td>
				 </tr>
				 <tr style="border-style: solid; border-width: 1px; border-color: #666">
                      <Td style="background-color: #eee;color:#333; width:80px">&nbsp;</td>
                      <td style="background-color: #eee;color:#333;" colspan="6"><div align="left">T&iacute;tulo de Posgrado:
                      <strong>hasta '.$oPosgradosMaximos->getObjectByIndex(0)->getNu_max().' puntos</strong> m&aacute;s por el t&iacute;tulo de posgrado obtenido en la
                      especialidad (si lo hubiere) </div></td>
				 </tr>
                    <tr style="border-style: solid; border-width: 1px; border-color: #666">';


		 $html .= '<td style="background-color: #eee;color:#333; width:80px">A1<br><strong>Max. '.$oPosgradosMaximos->getObjectByIndex(0)->getNu_max().'pt.</strong></td>';


		 for($i = 0; $i < $oPosgradosMaximos->size(); $i ++) {
		 	$checked = (($oPuntajePosgrado)&&($oPuntajePosgrado->getPosgradoMaximo()->getOid()==$oPosgradosMaximos->getObjectByIndex($i)->getOid()))?' CHECKED ':'';
			$html .='<td style="background-color: #fff; border-width: 0px; border-color: #fff"><input name="cd_posgradomaximo" id="cd_posgradomaximo'.$i.'" type="radio" value="'.$oPosgradosMaximos->getObjectByIndex($i)->getOid().'-'.$oPosgradosMaximos->getObjectByIndex($i)->getNu_max().'"
			onclick="sumar_total();"'.$checked.$disabled.' />
			<input name="nu_maxposgrado'.$i.'" id="nu_maxposgrado'.$i.'" type="hidden" value="'.$oPosgradosMaximos->getObjectByIndex($i)->getNu_max().'"/>'.
			$oPosgradosMaximos->getObjectByIndex($i)->getPosgradoplanilla()->getDs_posgradoplanilla().' ('.$oPosgradosMaximos->getObjectByIndex($i)->getNu_max().'pt.)</td>';

		}

	    $html .='<td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;"><div align="right"><input name="nu_posgrado" id="nu_posgrado"
	    type="hidden" value="0"/><span id="spanPosgrado"></span></div></td>';

		$html .='</tr></table><table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666;margin-bottom:5px">
		<tr style="border-style: solid; border-width: 1px; border-color: #666">';
	    $oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $oModeloPlanilla->getOid(), '=');
		$oCriteria->addOrder('cd_antacadmaximo');
		$managerAntacadMaximo =  ManagerFactory::getAntacadMaximoManager();
		$oAntacadsMaximos = $managerAntacadMaximo->getEntities($oCriteria);


		$submax=0;
		$max=0;
		$html.='<input type="hidden"  name="nu_cantantacad" id="nu_cantantacad" value="'.$oAntacadsMaximos->size().'">';
		 for($i = 0; $i < $oAntacadsMaximos->size(); $i ++) {

			if ($submax!=$oAntacadsMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getOid()){
				$max +=$oAntacadsMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getNu_max();
				if ($i!=0 && ($oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max()!=0))
					$html .='<tr style="background-color: #eee;color:#333;""><td></td><td colspan="3"><div align="right">
					Subtotal (max. '.$oAntacadsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().')</div><input type="hidden"
					name="nu_maxgrupoantacad'.$oAntacadsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'"
					id="nu_maxgrupoantacad'.$oAntacadsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'"
					value="'.$oAntacadsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().'">
					<div id="divgrupoAntacad'.$oAntacadsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" class="fValidator-a"></td></tr>';
					$submax=$oAntacadsMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getOid();
			}


			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
			$oCriteria->addFilter('cd_antacadmaximo', $oAntacadsMaximos->getObjectByIndex($i)->getOid(), '=');
			$managerPuntajeAntacad =  ManagerFactory::getPuntajeAntacadManager();
			$oPuntajeAntacad = $managerPuntajeAntacad->getEntity($oCriteria);

			//CYTSecureUtils::logObject($oPuntajeAntacad);
			$nu_puntaje = (($oPuntajeAntacad)&&($oPuntajeAntacad->getNu_puntaje()))?$oPuntajeAntacad->getNu_puntaje():'';
		 	$ds_tope = (($oAntacadsMaximos->getObjectByIndex($i)->getNu_max()!=0)&&($oAntacadsMaximos->getObjectByIndex($i)->getNu_min()==$oAntacadsMaximos->getObjectByIndex($i)->getNu_max()))?((($oAntacadsMaximos->getObjectByIndex($i)->getNu_min()==$oAntacadsMaximos->getObjectByIndex($i)->getNu_tope())&&($oAntacadsMaximos->getObjectByIndex($i)->getNu_min()==$oAntacadsMaximos->getObjectByIndex($i)->getNu_max()))?'':'<strong>Max. '.$oAntacadsMaximos->getObjectByIndex($i)->getNu_tope().'pt.</strong>'):'<strong>Max. '.$oAntacadsMaximos->getObjectByIndex($i)->getNu_tope().'pt.</strong>';
		 	$hasta = (($oAntacadsMaximos->getObjectByIndex($i)->getNu_max()!=0)&&($oAntacadsMaximos->getObjectByIndex($i)->getNu_min()==$oAntacadsMaximos->getObjectByIndex($i)->getNu_max()))?((($oAntacadsMaximos->getObjectByIndex($i)->getNu_min()==$oAntacadsMaximos->getObjectByIndex($i)->getNu_tope())&&($oAntacadsMaximos->getObjectByIndex($i)->getNu_min()==$oAntacadsMaximos->getObjectByIndex($i)->getNu_max()))?$oAntacadsMaximos->getObjectByIndex($i)->getNu_max(). ' pt.':$oAntacadsMaximos->getObjectByIndex($i)->getNu_max(). ' c/u'):'Hasta '.$oAntacadsMaximos->getObjectByIndex($i)->getNu_tope();
		 	$fvalidate = (($oAntacadsMaximos->getObjectByIndex($i)->getNu_max()!=0)&&($oAntacadsMaximos->getObjectByIndex($i)->getNu_min()==$oAntacadsMaximos->getObjectByIndex($i)->getNu_max()))?((($oAntacadsMaximos->getObjectByIndex($i)->getNu_min()==$oAntacadsMaximos->getObjectByIndex($i)->getNu_tope())&&($oAntacadsMaximos->getObjectByIndex($i)->getNu_min()==$oAntacadsMaximos->getObjectByIndex($i)->getNu_max()))?'':'isInteger'):'number';
		 	$mValidate = ($fvalidate='isInteger')?CYT_CMP_FORM_MSG_INVALID_NUMBER:CDT_CMP_FORM_MSG_INVALID_NUMBER;
		 	$checked = (($oAntacadsMaximos->getObjectByIndex($i)->getNu_min())&&($nu_puntaje)&&($oPuntajeAntacad->getOid())&&($oPuntajeAntacad->getAntacadMaximo()->getOid()==$oAntacadsMaximos->getObjectByIndex($i)->getOid()))?' CHECKED ':'';
			//CdtUtils::log('Min: '.$oAntacadsMaximos->getObjectByIndex($i)->getNu_min().' -> Puntaje: '.$oPuntajeAntacad.' -> ID: '. $oPuntajeAntacad->getOid().' -> ID Max: '.$oPuntajeAntacad->getAntacadMaximo()->getOid().' -> ID Max: '.$oAntacadsMaximos->getObjectByIndex($i)->getOid());
		 	if (($oAntacadsMaximos->getObjectByIndex($i)->getNu_min()==0)&&($oAntacadsMaximos->getObjectByIndex($i)->getNu_min()==$oAntacadsMaximos->getObjectByIndex($i)->getNu_tope())&&($oAntacadsMaximos->getObjectByIndex($i)->getNu_min()==$oAntacadsMaximos->getObjectByIndex($i)->getNu_max())){
		 		/*$select1 = ($nu_puntaje=='1')?' SELECTED="SELECTED"':'';
		 		$select2 = ($nu_puntaje=='0.9')?' SELECTED="SELECTED"':'';
		 		$select3 = ($nu_puntaje=='0.8')?' SELECTED="SELECTED"':'';
		 		$select4 = ($nu_puntaje=='0.7')?' SELECTED="SELECTED"':'';
		 		$select5 = ($nu_puntaje=='0.6')?' SELECTED="SELECTED"':'';
		 		$select6 = ($nu_puntaje=='0.5')?' SELECTED="SELECTED"':'';*/
		 		$checkedposgrado = ($nu_puntaje==2)?' CHECKED ':'';
		 		$nu_puntaje=($nu_puntaje==2)?1:$nu_puntaje;
		 		$hoy = date(CYT_FECHA_CIERRE);
	 			$yeargrado =(CYTSecureUtils::dias($hoy, $oSolicitud->getDt_egresogrado())/360);
	 			if($yeargrado <6 ){
					$nu_factor = 1;
				}
				else if(($yeargrado >= 6)&&($yeargrado < 7)){
					$nu_factor = 0.9;
				}
				else if(($yeargrado >= 7)&&($yeargrado < 8)){
					$nu_factor = 0.8;
				}
				else if(($yeargrado >= 8)&&($yeargrado < 9)){
					$nu_factor = 0.7;
				}
				else if(($yeargrado >= 9)&&($yeargrado < 10)){
					$nu_factor = 0.6;
				}
				else if($yeargrado >= 10){
					$nu_factor = 0.5;
				}
		 		if ($checkedposgrado==''){


		 		}


		 		$html .= '<td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;">A'.($i+2).'<br></td>';
		 		$html .='<td colspan="4" style="background-color: #eee;color:#333;">Se aplicarÃ¡ un "factor de eficiencia" F que multiplicarÃ¡
		 		al resultado de la suma de los puntajes correspondientes a A.1), A.2) y A.3): Llamando G al nÃºmero de aÃ±os transcurridos
		 		desde la obtenciÃ³n del tÃ­tulo de grado. <br>Si ya obtuvo el grado acadÃ©mico superior de la especialidad, o G<6 entonces
		 		F=1 <br>Si aÃºn no obtuvo el grado acadÃ©mico superior de la especialidad y G>=6 entonces: SI 6=< G <7 entonces F=0.9 -- SI
		 		7=< G <8 entonces F=0.8 -- SI 8=< G <9 entonces F=0.7 -- SI 9=< G <10 entonces F=0.6 -- SI G>=10 entonces F=0.5</td>  ';
				$html .= '</tr>';
				$html .= '<td style="background-color: #eee;color:#333; width:80px"></td>';
		 		$html .='<td style="width:450px;background-color: #fff; border-width: 0px; border-color: #fff">'.str_replace('#puntaje#', '<B>'.$oAntacadsMaximos->getObjectByIndex($i)->getNu_max().'
		 		puntos</B>',$oAntacadsMaximos->getObjectByIndex($i)->getAntacadPlanilla()->getDs_antacadplanilla()).'</td>
		 		<td style="width:120px;background-color: #fff; border-width: 0px; border-color: #fff"><input type="hidden"  name="nu_maxantacad'.$i.'" id="nu_maxantacad'.$i.'"
		 		value="'.$oAntacadsMaximos->getObjectByIndex($i)->getNu_max().'"><input type="hidden"  name="nu_topeantacad'.$i.'" id="nu_topeantacad'.$i.'"
		 		value="'.$oAntacadsMaximos->getObjectByIndex($i)->getNu_tope().'"><input type="hidden"  name="nu_minantacad'.$i.'" id="nu_minantacad'.$i.'"
		 		value="'.$oAntacadsMaximos->getObjectByIndex($i)->getNu_min().'"><input type="hidden"  name="cd_puntajegrupoantacad'.$i.'"
		 		id="cd_puntajegrupoantacad'.$i.'" value="'.$oAntacadsMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getOid().'">Posgrado Sup. Especialidad:
		 		<input type="checkbox" size="5" name="bl_posgrado" id="bl_posgrado" value="2"'.$checkedposgrado.' DISABLED
		 		onclick="sumar_total();"'.$disabled.'></td><td style="background-color: #fff; border-width: 0px; border-color: #fff"><input type="hidden"  name="nu_factor" id="nu_factor"
		 		value="'.$nu_factor.'"><input type="hidden"  name="nu_puntajeantacadP'.$oAntacadsMaximos->getObjectByIndex($i)->getOid().'"
		 		id="nu_puntajeantacad'.$i.'" value="'.$nu_puntaje.'">F: <span id="spanF">'.$nu_puntaje.'</span></td>
		 		<td style="background-color: #eee;color:#333; width:80px; text-align: right; vertical-align:middle;"><span id="spanpuntajeantacad'.$i.'" >
		 		</span><div id="divpuntajeantacad'.$i.'" class="fValidator-a"></div></td>';
				$html .= '</tr>';
		 	}
		 	else{
		 		$html .= '<td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;">A'.($i+2).'<br>'.$ds_tope.'</td>';
			 	$input = (($oAntacadsMaximos->getObjectByIndex($i)->getNu_min()==$oAntacadsMaximos->getObjectByIndex($i)->getNu_tope())&&($oAntacadsMaximos->getObjectByIndex($i)->getNu_min()==$oAntacadsMaximos->getObjectByIndex($i)->getNu_max()))?'<input type="checkbox" size="5" name="nu_puntajeantacadP'.$oAntacadsMaximos->getObjectByIndex($i)->getOid().'" id="nu_puntajeantacad'.$i.'" value="'.$oAntacadsMaximos->getObjectByIndex($i)->getNu_max().'"'.$checked.' onclick="sumar_total();"'.$disabled.'>':'<input type="text" size="5" name="nu_puntajeantacadP'.$oAntacadsMaximos->getObjectByIndex($i)->getOid().'" id="nu_puntajeantacad'.$i.'" value="'.$nu_puntaje.'" onblur="sumar_total();" jval="{valid:function (val) { return '.$fvalidate.'(val,\''.$mValidate.'\');}}"'.$disabled.'>';


				$html .='<td style="width:450px;background-color: #fff; border-width: 0px; border-color: #fff">'.str_replace('#puntaje#', '<B>'.$oAntacadsMaximos->getObjectByIndex($i)->getNu_max().' puntos</B>',$oAntacadsMaximos->getObjectByIndex($i)->getAntacadPlanilla()->getDs_antacadplanilla()).'</td>  <td style="width:120;background-color: #fff; border-width: 0px; border-color: #fff""><input type="hidden"  name="nu_maxantacad'.$i.'" id="nu_maxantacad'.$i.'" value="'.$oAntacadsMaximos->getObjectByIndex($i)->getNu_max().'"><input type="hidden"  name="nu_topeantacad'.$i.'" id="nu_topeantacad'.$i.'" value="'.$oAntacadsMaximos->getObjectByIndex($i)->getNu_tope().'"><input type="hidden"  name="nu_minantacad'.$i.'" id="nu_minantacad'.$i.'" value="'.$oAntacadsMaximos->getObjectByIndex($i)->getNu_min().'"><input type="hidden"  name="cd_puntajegrupoantacad'.$i.'" id="cd_puntajegrupoantacad'.$i.'" value="'.$oAntacadsMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getOid().'">'.$hasta.'</td><td style="background-color: #fff; border-width: 0px; border-color: #fff">'.$input.'</td><td style="background-color: #eee;color:#333; width:80px; text-align: right; vertical-align:middle;"><span id="spanpuntajeantacad'.$i.'" ></span><div id="divpuntajeantacad'.$i.'" class="fValidator-a"></div><input name="nu_valorantacad'.$i.'" id="nu_valorantacad'.$i.'" type="hidden" value=""'.$disabled.'/></td>';
				$html .= '</tr>';
		 	}


		}
		if($oAntacadsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max()!=0){
	     $html .='<tr style="background-color: #eee;color:#333;""><td></td><td colspan="3"><div align="right"><strong>Subtotal A (max. '.$oAntacadsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().')</strong></div><input type="hidden"  name="nu_maxgrupoantacad'.$oAntacadsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" id="nu_maxgrupoantacad'.$oAntacadsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" value="'.$oAntacadsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().'"></td><td style="text-align:right"><strong><span id="spangrupoAntacad'.$oAntacadsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" ></span></strong><div id="divgrupoAntacad'.$oAntacadsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" class="fValidator-a"></div></td></tr>';
		}

	   $html .='</tr>
                </table>

				<table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666 ;margin-bottom:5px">
				 <tr style="border-style: solid; border-width: 1px; border-color: #666">
                      <Td style="background-color: #eee;color:#333; width:80px">B</td>
                      <td style="background-color: #eee;color:#333;" colspan="5"><div align="center"><strong>ANTECEDENTES DOCENTES</strong></div></td>
				 </tr>

			  </table>
                <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666;margin-bottom:5px">
                    <tr style="border-style: solid; border-width: 1px; border-color: #666">';
	   	$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $oModeloPlanilla->getOid(), '=');
		$oCargoMaximoManager =  ManagerFactory::getCargoMaximoManager();
		$cargos = $oCargoMaximoManager->getEntities($oCriteria);

		/*$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
		$oPuntajeCargoManager =  ManagerFactory::getPuntajeCargoManager();
		$oPuntajecargo = $oPuntajeCargoManager->getEntity($oCriteria);


		 if (empty($oPuntajecargo)) {*/
			$cd_cargo=0;

			switch ($oSolicitud->getCargo()->getOid()) {
				case '1':
					$cd_cargo = 1;
				break;
				case '2':
					$cd_cargo = 5;
				break;
				case '3':
					$cd_cargo = 3;
				break;
				case '4':
					$cd_cargo = 7;
				break;
				case '5':
					$cd_cargo = 9;
				break;
				case '7':
					$cd_cargo = 2;
				break;
				case '8':
					$cd_cargo = 6;
				break;
				case '9':
					$cd_cargo = 4;
				break;
				case '10':
					$cd_cargo = 8;
				break;
				case '11':
					$cd_cargo = 10;
				break;
			}

			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_modeloplanilla', $oModeloPlanilla->getOid(), '=');
			$oCriteria->addFilter('cd_cargoplanilla', $cd_cargo, '=');
			$oCargoMaximoManager =  ManagerFactory::getCargoMaximoManager();
			$oCargoMaximo = $oCargoMaximoManager->getEntity($oCriteria);
			$oPuntajecargo = new PuntajeCargo();
			$oPuntajecargo->setCargoMaximo($oCargoMaximo);



		 $html .= '<td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;">B1 </td>';

		 $html .= '<td><table style="width:100%">';
		for($i = 0; $i < $cargos->size(); $i ++) {
			$html .= '<tr>';
			for($j = 0; $j < 2; $j ++) {

				$checked = (($oPuntajecargo)&&($oPuntajecargo->getCargoMaximo()->getOid()==$cargos->getObjectByIndex($i+$j)->getOid()))?' CHECKED ':'';
				$html .='<td style="background-color: #fff; border-width: 0px; border-color: #fff"><input name="cd_cargomaximo" id="cd_cargomaximo'.($i+$j).'" type="radio" value="'.$cargos->getObjectByIndex($i+$j)->getOid().'-'.$cargos->getObjectByIndex($i+$j)->getNu_max().'" onclick="sumar_total();"'.$checked.' DISABLED/><input name="nu_maxcargo'.($i+$j).'" id="nu_maxcargo'.($i+$j).'" type="hidden" value="'.$cargos->getObjectByIndex($i+$j)->getNu_max().'"/>'.$cargos->getObjectByIndex($i+$j)->getCargoPlanilla()->getDs_cargoplanilla().' ('.$cargos->getObjectByIndex($i+$j)->getNu_max().'pt.)</td>';

			}
			$i++;
			$html .= '</tr>';
		}

	    $html .='</table></td><td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;"><div align="right"><span id="spanCargo"></span></div></td>';
		$html .='</tr>
                </table>
				<table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666 ;margin-bottom:5px">
				 <tr style="border-style: solid; border-width: 1px; border-color: #666">
                      <Td style="background-color: #eee;color:#333; width:80px">C</td>
                      <td style="background-color: #eee;color:#333;" colspan="5"><div align="center"><strong>OTROS ANTECEDENTES</strong></div></td>
				 </tr>

			  </table>
                <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666;margin-bottom:5px">
                    <tr style="border-style: solid; border-width: 1px; border-color: #666">';
		$oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $oModeloPlanilla->getOid(), '=');
		$oCriteria->addOrder('cd_antotrosmaximo');
		$managerAntotrosMaximo =  ManagerFactory::getAntotrosMaximoManager();
		$oAntotrossMaximos = $managerAntotrosMaximo->getEntities($oCriteria);




		$submax=0;
		$max=0;
		$sub=0;
		$j=0;
		$html .='<input type="hidden"  name="nu_cantantotros" id="nu_cantantotros" value="'.$oAntotrossMaximos->size().'">';
		 for($i = 0; $i < $oAntotrossMaximos->size(); $i ++) {
			if ($submax!=$oAntotrossMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getOid() ){
				$max +=$oAntotrossMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getNu_max();
				if ($i!=0 && $oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max()!=0)
					$html .='<tr style="background-color: #eee;color:#333;""><td></td><td colspan="3"><div align="right"><strong>Subtotal (max. '.$oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().')</strong></div><input type="hidden"  name="nu_maxgrupoantotros'.$oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" id="nu_maxgrupoantotros'.$oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" value="'.$oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().'"><strong><div id="divgrupoAntotros'.$oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" class="fValidator-a"></div></strong></td></tr>';
				$submax=$oAntotrossMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getOid();
			}
			if (($sub!=$oAntotrossMaximos->getObjectByIndex($i)->getAntotrosPlanilla()->getSubGrupo()->getOid() )&&($oAntotrossMaximos->getObjectByIndex($i)->getAntotrosPlanilla()->getSubGrupo()->getOid()) ){
				$j++;
				$html .= '<tr><td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;">C'.($j).'</td><td style="background-color: #eee;color:#333;" colspan="4"><div align="left"><strong>'.$oAntotrossMaximos->getObjectByIndex($i)->getAntotrosPlanilla()->getSubGrupo()->getDs_subgrupo().'</strong></div></td></tr>';

				$sub=$oAntotrossMaximos->getObjectByIndex($i)->getAntotrosPlanilla()->getSubGrupo()->getOid();
			}
			elseif (!$oAntotrossMaximos->getObjectByIndex($i)->getAntotrosPlanilla()->getSubGrupo()->getOid()) $j++;




			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
			$oCriteria->addFilter('cd_antotrosmaximo', $oAntotrossMaximos->getObjectByIndex($i)->getOid(), '=');
			$managerPuntajeAntotros =  ManagerFactory::getPuntajeAntotrosManager();
			$oPuntajeAntotros = $managerPuntajeAntotros->getEntity($oCriteria);



			$nu_puntaje = (($oPuntajeAntotros)&&($oPuntajeAntotros->getNu_puntaje()))?$oPuntajeAntotros->getNu_puntaje():'';
		 	$ds_tope = (($oAntotrossMaximos->getObjectByIndex($i)->getNu_tope()==0)||($oAntotrossMaximos->getObjectByIndex($i)->getNu_tope()==$oAntotrossMaximos->getObjectByIndex($i)->getNu_min()))?'':'<strong>Max. '.$oAntotrossMaximos->getObjectByIndex($i)->getNu_tope().'pt.</strong>';
		 	$hasta = (($oAntotrossMaximos->getObjectByIndex($i)->getNu_max()!=0)&&($oAntotrossMaximos->getObjectByIndex($i)->getNu_min()==$oAntotrossMaximos->getObjectByIndex($i)->getNu_max()))?((($oAntotrossMaximos->getObjectByIndex($i)->getNu_min()==$oAntotrossMaximos->getObjectByIndex($i)->getNu_tope())&&($oAntotrossMaximos->getObjectByIndex($i)->getNu_min()==$oAntotrossMaximos->getObjectByIndex($i)->getNu_max()))?$oAntotrossMaximos->getObjectByIndex($i)->getNu_max(). ' pt.':$oAntotrossMaximos->getObjectByIndex($i)->getNu_max(). ' c/u'):'Hasta '.$oAntotrossMaximos->getObjectByIndex($i)->getNu_tope();

		 	$fvalidate = (($oAntotrossMaximos->getObjectByIndex($i)->getNu_max()!=0)&&($oAntotrossMaximos->getObjectByIndex($i)->getNu_min()==$oAntotrossMaximos->getObjectByIndex($i)->getNu_max()))?((($oAntotrossMaximos->getObjectByIndex($i)->getNu_min()==$oAntotrossMaximos->getObjectByIndex($i)->getNu_tope())&&($oAntotrossMaximos->getObjectByIndex($i)->getNu_min()==$oAntotrossMaximos->getObjectByIndex($i)->getNu_max()))?'':'isInteger'):'number';
		 	$mValidate = ($fvalidate='isInteger')?CYT_CMP_FORM_MSG_INVALID_NUMBER:CDT_CMP_FORM_MSG_INVALID_NUMBER;


		 		$html .= ($oAntotrossMaximos->getObjectByIndex($i)->getAntotrosPlanilla()->getSubGrupo()->getOid() )?'<td style="background-color: #eee;color:#333; width:80px"></td>':'<td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;">C'.($j).'<br>'.$ds_tope.'</td>';
			 	$input = (($oAntotrossMaximos->getObjectByIndex($i)->getNu_min()==$oAntotrossMaximos->getObjectByIndex($i)->getNu_tope())&&($oAntotrossMaximos->getObjectByIndex($i)->getNu_min()==$oAntotrossMaximos->getObjectByIndex($i)->getNu_max()))?'<input type="checkbox" size="5" name="nu_puntajeantotrosP'.$oAntotrossMaximos->getObjectByIndex($i)->getOid().'" id="nu_puntajeantotros'.$i.'" value="'.$oAntotrossMaximos->getObjectByIndex($i)->getNu_max().'"'.$checked.' onclick="sumar_total();"'.$disabled.' DISABLED>':'<input type="text" size="5" name="nu_puntajeantotrosP'.$oAntotrossMaximos->getObjectByIndex($i)->getOid().'" id="nu_puntajeantotros'.$i.'" value="'.$nu_puntaje.'" onblur="sumar_total();" jval="{valid:function (val) { return '.$fvalidate.'(val,\''.$mValidate.'\');}}"'.$disabled.'>';


				$html .='<td style="width:450px;background-color: #fff; border-width: 0px; border-color: #fff">'.str_replace('#puntaje#', '<B>'.$oAntotrossMaximos->getObjectByIndex($i)->getNu_max().' puntos</B>',$oAntotrossMaximos->getObjectByIndex($i)->getAntotrosPlanilla()->getDs_antotrosplanilla()).'</td>  <td style="width:120px;background-color: #fff; border-width: 0px; border-color: #fff"><input type="hidden"  name="nu_maxantotros'.$i.'" id="nu_maxantotros'.$i.'" value="'.$oAntotrossMaximos->getObjectByIndex($i)->getNu_max().'"><input type="hidden"  name="nu_topeantotros'.$i.'" id="nu_topeantotros'.$i.'" value="'.$oAntotrossMaximos->getObjectByIndex($i)->getNu_tope().'"><input type="hidden"  name="nu_minantotros'.$i.'" id="nu_minantotros'.$i.'" value="'.$oAntotrossMaximos->getObjectByIndex($i)->getNu_min().'"><input type="hidden"  name="cd_puntajegrupoantotros'.$i.'" id="cd_puntajegrupoantotros'.$i.'" value="'.$oAntotrossMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getOid().'">'.$hasta.'</td><td style="background-color: #fff; border-width: 0px; border-color: #fff">'.$input.'</td><td style="background-color: #eee;color:#333; width:80px; text-align: right; vertical-align:middle;"><span id="spanpuntajeantotros'.$i.'" ></span><div id="divpuntajeantotros'.$i.'" class="fValidator-a"></div><input name="nu_valorantotros'.$i.'" id="nu_valorantotros'.$i.'" type="hidden" value=""'.$disabled.'/></td>';
				$html .= '</tr>';



		}
		if ($oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max()!=0){
	     $html .='<tr style="background-color: #eee;color:#333;""><td></td><td colspan="3"><div align="right"><strong>Subtotal (max. '.$oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().')</strong></div><input type="hidden"  name="nu_maxgrupoantotros'.$oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" id="nu_maxgrupoantotros'.$oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" value="'.$oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().'"></td><td style="text-align:right"><strong><span id="spangrupoAntotros'.$oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" ></span></strong><div id="divgrupoAntotros'.$oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" class="fValidator-a"></div></td></tr>';
		}

		if($oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getCd_grupopadre()){
			$oPuntajeGrupoManager =  ManagerFactory::getPuntajeGrupoManager();
			$oPuntajeGrupo = $oPuntajeGrupoManager->getObjectByCode($oAntotrossMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getCd_grupopadre());
			$html .='<tr style="background-color: #eee;color:#333;""><td colspan="4"><div align="right"><strong>Subtotal C (max. '.$oPuntajeGrupo->getNu_max().')</strong></div><input type="hidden"  name="nu_maxgrupootros" id="nu_maxgrupootros" value="'.$oPuntajeGrupo->getNu_max().'"></td><td style="text-align:right"><strong><span id="spangrupootros" ></span></strong><div id="divgrupootros" class="fValidator-a"></div></td></tr>';

		}

		$html .='</tr>
                </table>
                <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666 ;margin-bottom:5px">
				 <tr style="border-style: solid; border-width: 1px; border-color: #666">
                      <Td style="background-color: #eee;color:#333; width:80px">D</td>
                      <td style="background-color: #eee;color:#333;" colspan="5"><div align="center"><strong>FORMACI&Oacute;N DE RR.HH. Y PRODUCCI&Oacute;N CIENT&Iacute;FICA EN LOS ULTIMOS 5 A&Ntilde;OS  ('.(intval($oPerioActual->getDs_periodo())-4).', '.(intval($oPerioActual->getDs_periodo())-3).', '.(intval($oPerioActual->getDs_periodo())-2).', '.(intval($oPerioActual->getDs_periodo())-1).', '.(intval($oPerioActual->getDs_periodo())).')</strong></div></td>
				 </tr>

			  </table>
				  <table style="width:100%">
                    <tr></tr>

                </table>
                <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666;margin-bottom:5px">
                    <tr style="border-style: solid; border-width: 1px; border-color: #666">';
	    $oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $oModeloPlanilla->getOid(), '=');
		$oCriteria->addOrder('cd_antproduccionmaximo');
		$managerAntproduccionMaximo =  ManagerFactory::getAntproduccionMaximoManager();
		$oAntproduccionsMaximos = $managerAntproduccionMaximo->getEntities($oCriteria);



		$submax=0;
		$max=0;
		$sub=0;
		$j=0;
		$html .='<input type="hidden"  name="nu_cantantproduccion" id="nu_cantantproduccion" value="'.$oAntproduccionsMaximos->size().'">';
		 for($i = 0; $i < $oAntproduccionsMaximos->size(); $i ++) {

			if ($submax!=$oAntproduccionsMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getOid() ){
				$max +=$oAntproduccionsMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getNu_max();
				if ($i!=0 && $oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max()!=0)

					$html .='<tr style="background-color: #eee;color:#333;""><td colspan="4"><div align="right"><strong>Subtotal (max. '.$oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().')</strong></div><input type="hidden"  name="nu_maxgrupo'.$oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" id="nu_maxgrupo'.$oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" value="'.$oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().'"></td><td style="text-align:right"><strong><span id="spangrupoAntproduccion'.$oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" ></span></strong><div id="divgrupoAntproduccion'.$oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" class="fValidator-a"></div></td></tr>';
				$submax=$oAntproduccionsMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getOid();
			}

			if (($sub!=$oAntproduccionsMaximos->getObjectByIndex($i)->getAntproduccionPlanilla()->getSubGrupo()->getOid() )&&($oAntproduccionsMaximos->getObjectByIndex($i)->getAntproduccionPlanilla()->getSubGrupo()->getOid()) ){
				$j++;
				$html .= '<tr><td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;">D'.($j).'</td><td style="background-color: #eee;color:#333;" colspan="5"><div align="left"><strong>'.$oAntproduccionsMaximos->getObjectByIndex($i)->getAntproduccionPlanilla()->getSubGrupo()->getDs_subgrupo().'</strong></div></td></tr>';

				$sub=$oAntproduccionsMaximos->getObjectByIndex($i)->getAntproduccionPlanilla()->getSubGrupo()->getOid();
			}
			elseif (!$oAntproduccionsMaximos->getObjectByIndex($i)->getAntproduccionPlanilla()->getSubGrupo()->getOid()) $j++;



			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
			$oCriteria->addFilter('cd_antproduccionmaximo', $oAntproduccionsMaximos->getObjectByIndex($i)->getOid(), '=');
			$managerPuntajeAntproduccion =  ManagerFactory::getPuntajeAntproduccionManager();
			$oPuntajeAntproduccion = $managerPuntajeAntproduccion->getEntity($oCriteria);


			$nu_puntaje = (($oPuntajeAntproduccion)&&($oPuntajeAntproduccion->getNu_puntaje()))?$oPuntajeAntproduccion->getNu_puntaje():'';
			$nu_cant = (($oPuntajeAntproduccion)&&($oPuntajeAntproduccion->getNu_cant()))?$oPuntajeAntproduccion->getNu_cant():'';
		 	$ds_tope = (($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_tope()==0)||($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_tope()==$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_min()))?'':'<strong>Max. '.$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_tope().'pt.</strong>';
		 	//$c_u=($i!=0)?' c/u':' c/10hs.';
		 	$c_u=' c/u';
		 	$hasta = (($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max()))?$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max(). ' pt.':$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max(). $c_u):(($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_min()!=$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max())?'Hasta '.$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max(). ' c/u':'Hasta '.$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_tope());
		 	$fvalidate = (($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max()!=0)&&($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max()))?((($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max()))?'':'isInteger'):'number';
		 	$mValidate = ($fvalidate='isInteger')?CYT_CMP_FORM_MSG_INVALID_NUMBER:CDT_CMP_FORM_MSG_INVALID_NUMBER;


			$oCriteria = new CdtSearchCriteria();

			$tUnidadAprobada = CYTSecureDAOFactory::getUnidadAprobadaDAO()->getTableName();
			$oCriteria->addFilter("$tUnidadAprobada.cd_periodo", $oPerioActual->getOid(), '=');
			$oCriteria->addFilter("$tUnidadAprobada.cd_unidad", $oSolicitud->getLugarTrabajo()->getOid(), '=');

			$checkedUnidad='';
			$oUnidadAprobadaManager =  CYTSecureManagerFactory::getUnidadAprobadaManager();
			$oUnidadAprobada = $oUnidadAprobadaManager->getEntity($oCriteria);
			if ($oUnidadAprobada) {
				$checkedUnidad = ' CHECKED ';
			}
			elseif ($oSolicitud->getLugarTrabajoCarrera()->getOid()) {
				$oCriteria = new CdtSearchCriteria();
				$tUnidadAprobada = CYTSecureDAOFactory::getUnidadAprobadaDAO()->getTableName();
				$oCriteria->addFilter("$tUnidadAprobada.cd_periodo", $oPerioActual->getOid(), '=');
				$oCriteria->addFilter("$tUnidadAprobada.cd_unidad", $oSolicitud->getLugarTrabajoCarrera()->getOid(), '=');
				$oUnidadAprobadaManager =  CYTSecureManagerFactory::getUnidadAprobadaManager();
				$oUnidadAprobada = $oUnidadAprobadaManager->getEntity($oCriteria);
				if ($oUnidadAprobada) {
					$checkedUnidad = ' CHECKED ';
				}
			}
			if (($checkedUnidad != ' CHECKED ')&&($oSolicitud->getLugarTrabajoBeca()->getOid())) {
				$oCriteria = new CdtSearchCriteria();
				$tUnidadAprobada = CYTSecureDAOFactory::getUnidadAprobadaDAO()->getTableName();
				$oCriteria->addFilter("$tUnidadAprobada.cd_periodo", $oPerioActual->getOid(), '=');
				$oCriteria->addFilter("$tUnidadAprobada.cd_unidad", $oSolicitud->getLugarTrabajoBeca()->getOid(), '=');
				$oUnidadAprobadaManager =  CYTSecureManagerFactory::getUnidadAprobadaManager();
				$oUnidadAprobada = $oUnidadAprobadaManager->getEntity($oCriteria);
				if ($oUnidadAprobada) {
					$checkedUnidad = ' CHECKED ';
				}
			}




		 		$html .= ($oAntproduccionsMaximos->getObjectByIndex($i)->getAntproduccionPlanilla()->getSubGrupo()->getOid() )?'<td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;">'.$ds_tope.'</td>':'<td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;">D'.($j).'<br>'.$ds_tope.'</td>';
			 	$input = (($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_tope())&&($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_min()==$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max()))?'<td style="background-color: #fff; border-width: 0px; border-color: #fff"><input type="checkbox" size="5" name="nu_puntajeantproduccionP'.$oAntproduccionsMaximos->getObjectByIndex($i)->getOid().'" id="nu_puntajeantproduccion'.$i.'" value="'.$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max().'"'.$checkedUnidad.$disabled.' DISABLED onclick="sumar_total();" DISABLED></td><td style="background-color: #eee;color:#333; width:80px; text-align: right; vertical-align:middle;"><span id="spanpuntajeantproduccion'.$i.'" ></span><div id="divpuntajeantproduccion'.$i.'" class="fValidator-a"></div><input name="nu_valorantproduccion'.$i.'" id="nu_valorantproduccion'.$i.'" type="hidden" value=""/></td>':(($oAntproduccionsMaximos->getObjectByIndex($i)->getNu_min()!=$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max())?'<td style="background-color: #fff; border-width: 0px; border-color: #fff"><input type="text" size="5" name="nu_cantantproduccionP'.$oAntproduccionsMaximos->getObjectByIndex($i)->getOid().'" id="nu_cantantproduccion'.$i.'" value="'.$nu_cant.'" onblur="sumar_total();" jval="{valid:function (val) { return isInteger(val,\''.CYT_CMP_FORM_MSG_INVALID_NUMBER.'\');}}"'.$disabled.'></td><td style="background-color: #eee;color:#333;" align="right"><input type="text" size="5" name="nu_puntajeantproduccionP'.$oAntproduccionsMaximos->getObjectByIndex($i)->getOid().'" id="nu_puntajeantproduccion'.$i.'" value="'.$nu_puntaje.'" onblur="sumar_total();" jval="{valid:function (val) { return number(val,\''.CDT_CMP_FORM_MSG_INVALID_NUMBER.'\');}}"'.$disabled.'><div id="divpuntajeantproduccion'.$i.'" class="fValidator-a"></div><input name="nu_valorantproduccion'.$i.'" id="nu_valorantproduccion'.$i.'" type="hidden" value=""/></td>':'<td style="background-color: #fff; border-width: 0px; border-color: #fff"><input type="text" size="5" name="nu_puntajeantproduccionP'.$oAntproduccionsMaximos->getObjectByIndex($i)->getOid().'" id="nu_puntajeantproduccion'.$i.'" value="'.$nu_puntaje.'" onblur="sumar_total();" jval="{valid:function (val) { return '.$fvalidate.'(val,\''.$mValidate.'\');}}"'.$disabled.'></td><td style="background-color: #eee;color:#333; width:80px; text-align: right; vertical-align:middle;"><span id="spanpuntajeantproduccion'.$i.'" ></span><div id="divpuntajeantproduccion'.$i.'" class="fValidator-a"></div><input name="nu_valorantproduccion'.$i.'" id="nu_valorantproduccion'.$i.'" type="hidden" value=""/></td>');


				$html .='<td style="width:450px;background-color: #fff; border-width: 0px; border-color: #fff">'.str_replace('#puntaje#', '<B>'.$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max().' puntos</B>',$oAntproduccionsMaximos->getObjectByIndex($i)->getAntproduccionPlanilla()->getDs_antproduccionplanilla()).'</td>  <td style="width:120px;background-color: #fff; border-width: 0px; border-color: #fff"><input type="hidden"  name="nu_maxantproduccion'.$i.'" id="nu_maxantproduccion'.$i.'" value="'.$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_max().'"><input type="hidden"  name="nu_topeantproduccion'.$i.'" id="nu_topeantproduccion'.$i.'" value="'.$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_tope().'"><input type="hidden"  name="nu_minantproduccion'.$i.'" id="nu_minantproduccion'.$i.'" value="'.$oAntproduccionsMaximos->getObjectByIndex($i)->getNu_min().'"><input type="hidden"  name="cd_puntajegrupoantproduccion'.$i.'" id="cd_puntajegrupoantproduccion'.$i.'" value="'.$oAntproduccionsMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getOid().'">'.$hasta.'</td>'.$input;
				$html .= '</tr>';



		}
		if ($oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max()!=0){
	     $html .='<tr style="background-color: #eee;color:#333;""><td colspan="4"><div align="right"><strong>Subtotal (max. '.$oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().')</strong></div><input type="hidden"  name="nu_maxgrupo'.$oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" id="nu_maxgrupo'.$oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" value="'.$oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().'"></td><td style="text-align:right"><strong><span id="spangrupoAntproduccion'.$oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" ></span></strong><div id="divgrupoAntproduccion'.$oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" class="fValidator-a"></div></td></tr>';
		}
		if($oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getCd_grupopadre()){
			$oPuntajeGrupoManager =  ManagerFactory::getPuntajeGrupoManager();
			$oPuntajeGrupo = $oPuntajeGrupoManager->getObjectByCode($oAntproduccionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getCd_grupopadre());
			$html .='<tr style="background-color: #eee;color:#333;""><td colspan="4"><div align="right"><strong>Subtotal D (max. '.$oPuntajeGrupo->getNu_max().')</strong></div><input type="hidden"  name="nu_maxgrupoantproduccion" id="nu_maxgrupoantproduccion" value="'.$oPuntajeGrupo->getNu_max().'"></td><td style="text-align:right"><strong><span id="spangrupoproduccion" ></span></strong><div id="divgrupoproduccion" class="fValidator-a"></div></td></tr>';

		}

		$html .='</tr>
                </table>

                <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666;margin-bottom:5px">
                    <tr style="border-style: solid; border-width: 1px; border-color: #666">';
	    $oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $oModeloPlanilla->getOid(), '=');
		$oCriteria->addOrder('cd_subanteriormaximo');
		$managerSubanteriorMaximo =  ManagerFactory::getSubanteriorMaximoManager();
		$oSubanteriorsMaximos = $managerSubanteriorMaximo->getEntities($oCriteria);



		 $count = $oSubanteriorsMaximos->size();
		$submax=0;
		$max=0;
		$sub=0;
		$j=0;
		$html .='<input type="hidden"  name="nu_cantsubanterior" id="nu_cantsubanterior" value="'.$count.'">';
		 for($i = 0; $i < $count; $i ++) {

			if (($sub!=$oSubanteriorsMaximos->getObjectByIndex($i)->getSubanteriorPlanilla()->getSubGrupo()->getOid())&&($oSubanteriorsMaximos->getObjectByIndex($i)->getSubanteriorPlanilla()->getSubGrupo()->getOid()) ){
				$j++;
				$html .= '<tr><td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;">E'.($j).'</td><td style="background-color: #eee;color:#333;" colspan="4"><div align="left"><strong>'.$oSubanteriorsMaximos->getObjectByIndex($i)->getSubanteriorPlanilla()->getSubGrupo()->getDs_subgrupo().'</strong></div></td></tr>';

				$sub=$oSubanteriorsMaximos->getObjectByIndex($i)->getSubanteriorPlanilla()->getSubGrupo()->getOid();
			}
			elseif (!$oSubanteriorsMaximos->getObjectByIndex($i)->getSubanteriorPlanilla()->getSubGrupo()->getOid()) $j++;

		 	if ($submax!=$oSubanteriorsMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getOid() ){
				$max +=$oSubanteriorsMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getNu_max();
				if ($i!=0 && $oSubanteriorsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max()!=0)
					$html .='<tr style="background-color: #eee;color:#333;""><td></td><td colspan="3"><div align="right"><strong>Subtotal (max. '.$oSubanteriorsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().')</strong></div><input type="hidden"  name="nu_maxgruposubanterior'.$oSubanteriorsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" id="nu_maxgruposubanterior'.$oSubanteriorsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" value="'.$oSubanteriorsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().'"><trong><div id="divgrupoSubanterior'.$oSubanteriorsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" class="fValidator-a"></div></strong></td></tr>';
				$submax=$oSubanteriorsMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getOid();
			}


			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
			$oCriteria->addFilter('cd_subanteriormaximo', $oSubanteriorsMaximos->getObjectByIndex($i)->getOid(), '=');
			$managerPuntajeSubanterior =  ManagerFactory::getPuntajeSubanteriorManager();
			$oPuntajeSubanterior = $managerPuntajeSubanterior->getEntity($oCriteria);

			$nu_puntaje = (($oPuntajeSubanterior)&&($oPuntajeSubanterior->getNu_puntaje()))?$oPuntajeSubanterior->getNu_puntaje():'';
		 	$ds_tope = (($oSubanteriorsMaximos->getObjectByIndex($i)->getNu_tope()==0)||($oSubanteriorsMaximos->getObjectByIndex($i)->getNu_tope()==$oSubanteriorsMaximos->getObjectByIndex($i)->getNu_min()))?'':'<strong>Max. '.$oSubanteriorsMaximos->getObjectByIndex($i)->getNu_tope().'pt.</strong>';
		 	$hasta = (($oSubanteriorsMaximos->getObjectByIndex($i)->getNu_max()!=0)&&($oSubanteriorsMaximos->getObjectByIndex($i)->getNu_min()==$oSubanteriorsMaximos->getObjectByIndex($i)->getNu_max()))?((($oSubanteriorsMaximos->getObjectByIndex($i)->getNu_min()==$oSubanteriorsMaximos->getObjectByIndex($i)->getNu_tope())&&($oSubanteriorsMaximos->getObjectByIndex($i)->getNu_min()==$oSubanteriorsMaximos->getObjectByIndex($i)->getNu_max()))?$oSubanteriorsMaximos->getObjectByIndex($i)->getNu_max(). ' pt.':$oSubanteriorsMaximos->getObjectByIndex($i)->getNu_max(). ' c/u'):'Hasta '.$oSubanteriorsMaximos->getObjectByIndex($i)->getNu_tope();
		 	$fvalidate = (($oSubanteriorsMaximos->getObjectByIndex($i)->getNu_max()!=0)&&($oSubanteriorsMaximos->getObjectByIndex($i)->getNu_min()==$oSubanteriorsMaximos->getObjectByIndex($i)->getNu_max()))?((($oSubanteriorsMaximos->getObjectByIndex($i)->getNu_min()==$oSubanteriorsMaximos->getObjectByIndex($i)->getNu_tope())&&($oSubanteriorsMaximos->getObjectByIndex($i)->getNu_min()==$oSubanteriorsMaximos->getObjectByIndex($i)->getNu_max()))?'':'isInteger'):'number';
		 	$mValidate = ($fvalidate='isInteger')?CYT_CMP_FORM_MSG_INVALID_NUMBER:CDT_CMP_FORM_MSG_INVALID_NUMBER;

		 	//$checked = (($oSubanteriorsMaximos->getObjectByIndex($i)->getNu_min())&&($oPuntajeSubanterior->getOid())&&($oPuntajeSubanterior->getOid()==$oSubanteriorsMaximos->getObjectByIndex($i)->getOid()))?' CHECKED ':'';
		 	$oCriteria = new CdtSearchCriteria();
			$tDocente = CYTSecureDAOFactory::getDocenteDAO()->getTableName();
			$oCriteria->addFilter("$tDocente.cd_docente", $oSolicitud->getDocente()->getOid(), '=');

			$oPeriodoManager = CYTSecureManagerFactory::getPeriodoManager();
			$oPerioActual = $oPeriodoManager->getPeriodoActual(CYT_PERIODO_YEAR);
			$tSolicitudEstado = CYTSecureDAOFactory::getSolicitudEstadoDAO()->getTableName();
			//$oCriteria->addFilter("$tSolicitudEstado.estado_oid", CYT_ESTADO_SOLICITUD_OTORGADA, '=');
			$filter = new CdtSimpleExpression( "(".$tSolicitudEstado.".estado_oid =".CYT_ESTADO_SOLICITUD_OTORGADA." OR ".$tSolicitudEstado.".estado_oid =".CYT_ESTADO_SOLICITUD_RENDIDA." OR ".$tSolicitudEstado.".estado_oid =".CYT_ESTADO_SOLICITUD_RENUNCIADA." OR ".$tSolicitudEstado.".estado_oid =".CYT_ESTADO_SOLICITUD_DEVUELTA.")");
			$oCriteria->setExpresion($filter);
			$oCriteria->addNull('fechaHasta');
			$tSolicitud = DAOFactory::getSolicitudDAO()->getTableName();
             $periodoAnt = $oPerioActual->getOid()-2;//OJO esto es menos 2 solo por pandemia año 2021 (2020 no hubo)
			$oCriteria->addFilter("$tSolicitud.cd_periodo", $periodoAnt, '=');

			$oSolicitudManager =  ManagerFactory::getSolicitudManager();
			$oSolicitudAnterior = $oSolicitudManager->getEntity($oCriteria);


		 	$checked = ($oSolicitudAnterior)?'': ' CHECKED ';



		 		$html .= ($oSubanteriorsMaximos->getObjectByIndex($i)->getSubanteriorPlanilla()->getSubGrupo()->getOid())?'<td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;"></td>':'<td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;">E'.($j).'<br>'.$ds_tope.'</td>';
			 	$input = (($oSubanteriorsMaximos->getObjectByIndex($i)->getNu_min()==$oSubanteriorsMaximos->getObjectByIndex($i)->getNu_tope())&&($oSubanteriorsMaximos->getObjectByIndex($i)->getNu_min()==$oSubanteriorsMaximos->getObjectByIndex($i)->getNu_max()))?'<input type="checkbox" size="5" name="nu_puntajesubanteriorP'.$oSubanteriorsMaximos->getObjectByIndex($i)->getOid().'" id="nu_puntajesubanterior'.$i.'" value="'.$oSubanteriorsMaximos->getObjectByIndex($i)->getNu_max().'"'.$checked.' onclick="sumar_total();" DISABLED>':'<input type="text" size="5" name="nu_puntajesubanteriorP'.$oSubanteriorsMaximos->getObjectByIndex($i)->getOid().'" id="nu_puntajesubanterior'.$i.'" value="'.$nu_puntaje.'" onblur="sumar_total();" jval="{valid:function (val) { return '.$fvalidate.'(val,\''.$mValidate.'\');}}"'.$disabled.'>';


				$html .='<td style="width:450px;background-color: #fff; border-width: 0px; border-color: #fff">'.str_replace('#puntaje#', '<B>'.$oSubanteriorsMaximos->getObjectByIndex($i)->getNu_max().' puntos</B>',$oSubanteriorsMaximos->getObjectByIndex($i)->getSubanteriorPlanilla()->getDs_subanteriorplanilla()).'</td>  <td style="width:120px;background-color: #fff; border-width: 0px; border-color: #fff"><input type="hidden"  name="nu_maxsubanterior'.$i.'" id="nu_maxsubanterior'.$i.'" value="'.$oSubanteriorsMaximos->getObjectByIndex($i)->getNu_max().'"><input type="hidden"  name="nu_topesubanterior'.$i.'" id="nu_topesubanterior'.$i.'" value="'.$oSubanteriorsMaximos->getObjectByIndex($i)->getNu_tope().'"><input type="hidden"  name="nu_minsubanterior'.$i.'" id="nu_minsubanterior'.$i.'" value="'.$oSubanteriorsMaximos->getObjectByIndex($i)->getNu_min().'"><input type="hidden"  name="cd_puntajegruposubanterior'.$i.'" id="cd_puntajegruposubanterior'.$i.'" value="'.$oSubanteriorsMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getOid().'">'.$hasta.'</td><td style="background-color: #fff; border-width: 0px; border-color: #fff">'.$input.'</td><td style="background-color: #eee;color:#333; width:80px; text-align: right; vertical-align:middle;"><span id="spanpuntajesubanterior'.$i.'" ></span><div id="divpuntajesubanterior'.$i.'" class="fValidator-a"></div><input name="nu_valorsubanterior'.$i.'" id="nu_valorsubanterior'.$i.'" type="hidden" value=""/></td>';
				$html .= '</tr>';



		}
		if ($oSubanteriorsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max()!=0){
	     $html .='<tr style="background-color: #eee;color:#333;""><td></td><td colspan="3"><div align="right"><strong>Subtotal (max. '.$oSubanteriorsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().')</strong></div><input type="hidden"  name="nu_maxgruposubanterior'.$oSubanteriorsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" id="nu_maxgruposubanterior'.$oSubanteriorsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" value="'.$oSubanteriorsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().'"></td><td style="text-align:right"><strong><span id="spangrupoSubanterior'.$oSubanteriorsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" ></span></strong><div id="divgrupoSubanterior'.$oSubanteriorsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" class="fValidator-a"></td></tr>';
		}


		$html .='</tr>
                </table>
				<table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666 ;margin-bottom:5px">
				 <tr style="border-style: solid; border-width: 1px; border-color: #666">
                      <Td style="background-color: #eee;color:#333; width:80px">F</td>
                      <td style="background-color: #eee;color:#333;" colspan="5"><div align="center"><strong>JUSTIFICACI&Oacute;N  T&Eacute;CNICA DEL SUBSIDIO SOLICITADO </strong></div></td>
				 </tr>

			  </table>
                <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666;margin-bottom:5px">
                    <tr style="border-style: solid; border-width: 1px; border-color: #666">';
	    $oCriteria = new CdtSearchCriteria();
		$oCriteria->addFilter('cd_modeloplanilla', $oModeloPlanilla->getOid(), '=');
		$oCriteria->addOrder('cd_antjustificacionmaximo');
		$managerAntjustificacionMaximo =  ManagerFactory::getAntjustificacionMaximoManager();
		$oAntjustificacionsMaximos = $managerAntjustificacionMaximo->getEntities($oCriteria);



		 $count = $oAntjustificacionsMaximos->size();
		$submax=0;
		$max=0;
		$sub=0;
		$j=0;
		$html .='<input type="hidden"  name="nu_cantantjustificacion" id="nu_cantantjustificacion" value="'.$count.'">';
		 for($i = 0; $i < $count; $i ++) {

			if (($sub!=$oAntjustificacionsMaximos->getObjectByIndex($i)->getAntjustificacionPlanilla()->getSubGrupo()->getOid())&&($oAntjustificacionsMaximos->getObjectByIndex($i)->getAntjustificacionPlanilla()->getSubGrupo()->getOid()) ){
				$j++;
				$html .= '<tr><td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;">F'.($j).'</td><td style="background-color: #eee;color:#333;" colspan="4"><div align="left">'.$oAntjustificacionsMaximos->getObjectByIndex($i)->getAntjustificacionPlanilla()->getSubGrupo()->getDs_subgrupo().'</div></td></tr>';

				$sub=$oAntjustificacionsMaximos->getObjectByIndex($i)->getAntjustificacionPlanilla()->getSubGrupo()->getOid();
			}
			elseif (!$oAntjustificacionsMaximos->getObjectByIndex($i)->getAntjustificacionPlanilla()->getSubGrupo()->getOid()) $j++;

		 	if ($submax!=$oAntjustificacionsMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getOid() ){
				$max +=$oAntjustificacionsMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getNu_max();
				if ($i!=0 && $oAntjustificacionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max()!=0)
					$html .='<tr style="background-color: #eee;color:#333;""><td></td><td colspan="3"><div align="right"><strong>Subtotal (max. '.$oAntjustificacionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().')</strong></div><input type="hidden"  name="nu_maxgrupoantjustificacion'.$oAntjustificacionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" id="nu_maxgrupoantjustificacion'.$oAntjustificacionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" value="'.$oAntjustificacionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().'"><trong><div id="divgrupoAntjustificacion'.$oAntjustificacionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" class="fValidator-a"></div></strong></td></tr>';
				$submax=$oAntjustificacionsMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getOid();
			}


			$oCriteria = new CdtSearchCriteria();
			$oCriteria->addFilter('cd_evaluacion', $entity->getOid(), '=');
			$oCriteria->addFilter('cd_antjustificacionmaximo', $oAntjustificacionsMaximos->getObjectByIndex($i)->getOid(), '=');
			$managerPuntajeAntjustificacion =  ManagerFactory::getPuntajeAntjustificacionManager();
			$oPuntajeAntjustificacion = $managerPuntajeAntjustificacion->getEntity($oCriteria);

			$nu_puntaje = (($oPuntajeAntjustificacion)&&($oPuntajeAntjustificacion->getNu_puntaje()))?$oPuntajeAntjustificacion->getNu_puntaje():'';
		 	$ds_tope = (($oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_tope()==0)||($oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_tope()==$oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_min()))?'':'<strong>Max. '.$oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_tope().'pt.</strong>';
		 	$hasta = (($oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_max()!=0)&&($oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_min()==$oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_max()))?((($oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_min()==$oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_tope())&&($oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_min()==$oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_max()))?$oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_max(). ' pt.':$oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_max(). ' c/u'):'Hasta '.$oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_tope();
		 	$fvalidate = (($oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_max()!=0)&&($oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_min()==$oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_max()))?((($oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_min()==$oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_tope())&&($oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_min()==$oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_max()))?'':'isInteger'):'number';
		 	$mValidate = ($fvalidate='isInteger')?CYT_CMP_FORM_MSG_INVALID_NUMBER:CDT_CMP_FORM_MSG_INVALID_NUMBER;

		 	$checked = (($oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_min())&&($oPuntajeAntjustificacion->getOid())&&($oPuntajeAntjustificacion->getOid()==$oAntjustificacionsMaximos->getObjectByIndex($i)->getOid()))?' CHECKED ':'';

		 		$html .= ($oAntjustificacionsMaximos->getObjectByIndex($i)->getAntjustificacionPlanilla()->getSubGrupo()->getOid())?'<td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;"></td>':'<td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;">F'.($j).'<br>'.$ds_tope.'</td>';
			 	$input = (($oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_min()==$oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_tope())&&($oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_min()==$oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_max()))?'<input type="checkbox" size="5" name="nu_puntajeantjustificacionP'.$oAntjustificacionsMaximos->getObjectByIndex($i)->getOid().'" id="nu_puntajeantjustificacion'.$i.'" value="'.$oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_max().'"'.$checked.' onclick="sumar_total();"'.$disabled.'>':'<input type="text" size="5" name="nu_puntajeantjustificacionP'.$oAntjustificacionsMaximos->getObjectByIndex($i)->getOid().'" id="nu_puntajeantjustificacion'.$i.'" value="'.$nu_puntaje.'" onblur="sumar_total();" jval="{valid:function (val) { return '.$fvalidate.'(val,\''.$mValidate.'\');}}"'.$disabled.'>';


				$html .='<td style="width:450px;background-color: #fff; border-width: 0px; border-color: #fff">'.str_replace('#puntaje#', '<B>'.$oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_max().' puntos</B>',$oAntjustificacionsMaximos->getObjectByIndex($i)->getAntjustificacionPlanilla()->getDs_antjustificacionplanilla()).'</td>  <td style="width:120px;background-color: #fff; border-width: 0px; border-color: #fff"><input type="hidden"  name="nu_maxantjustificacion'.$i.'" id="nu_maxantjustificacion'.$i.'" value="'.$oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_max().'"><input type="hidden"  name="nu_topeantjustificacion'.$i.'" id="nu_topeantjustificacion'.$i.'" value="'.$oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_tope().'"><input type="hidden"  name="nu_minantjustificacion'.$i.'" id="nu_minantjustificacion'.$i.'" value="'.$oAntjustificacionsMaximos->getObjectByIndex($i)->getNu_min().'"><input type="hidden"  name="cd_puntajegrupoantjustificacion'.$i.'" id="cd_puntajegrupoantjustificacion'.$i.'" value="'.$oAntjustificacionsMaximos->getObjectByIndex($i)->getPuntajeGrupo()->getOid().'">'.$hasta.'</td><td style="background-color: #fff; border-width: 0px; border-color: #fff">'.$input.'</td><td style="background-color: #eee;color:#333; width:80px; text-align: right; vertical-align:middle;"><span id="spanpuntajeantjustificacion'.$i.'" ></span><div id="divpuntajeantjustificacion'.$i.'" class="fValidator-a"></div><input name="nu_valorantjustificacion'.$i.'" id="nu_valorantjustificacion'.$i.'" type="hidden" value=""/></td>';
				$html .= '</tr>';



		}
		if ($oAntjustificacionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max()!=0){
	     $html .='<tr style="background-color: #eee;color:#333;""><td></td><td colspan="3"><div align="right"><strong>Subtotal (max. '.$oAntjustificacionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().')</strong></div><input type="hidden"  name="nu_maxgrupoantjustificacion'.$oAntjustificacionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" id="nu_maxgrupoantjustificacion'.$oAntjustificacionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" value="'.$oAntjustificacionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getNu_max().'"></td><td style="text-align:right"><strong><span id="spangrupoAntjustificacion'.$oAntjustificacionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" ></span></strong><div id="divgrupoAntjustificacion'.$oAntjustificacionsMaximos->getObjectByIndex($i-1)->getPuntajeGrupo()->getOid().'" class="fValidator-a"></td></tr>';
		}
		$html .=' </tr>
                </table>';

		$entity->setDs_contenido($html);


		//CYTSecureUtils::logObject($entity);
		return $entity;
	}

	protected function parseEntity($entity, XTemplate $xtpl) {



		parent::parseEntity($entity, $xtpl);

	}

	protected function getEntityManager(){
		//return ManagerFactory::getEvaluacionManager();
	}

	/**
	 * (non-PHPdoc)
	 * @see classes/com/gestion/action/entities/EditEntityInitAction::getNewFormInstance()
	 */
	public function getNewFormInstance($action){
		$form = new CMPEvaluacionForm($action);
		return $form;
	}

	/**
	 * (non-PHPdoc)
	 * @see classes/com/gestion/action/entities/EditEntityInitAction::getNewEntityInstance()
	 */
	public function getNewEntityInstance(){
		$oEvaluacion = new Evaluacion();
		return $oEvaluacion;
	}


	/**
	 * (non-PHPdoc)
	 * @see CdtEditAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CYT_MSG_SOLICITUD_TITLE_EVALUAR;
	}

	/**
	 * retorna el action para el submit.
	 * @return string
	 */
	protected function getSubmitAction(){
		return "evaluar_solicitud";
	}


}
