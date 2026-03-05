<?php
/**
 * 
 * @author bernardo
 * @since 16-03-2010
 *
 */	
class Localidad {
		private $cd_localidad;
		private $ds_localidad;
		private $oProvincia;
		
		//Método constructor 
		

		function Localidad() {
			
			$this->cd_localidad = 0;
			$this->ds_localidad = '';
			$this->oProvincia = new Provincia();
		}
		
		//Métodos Get 
		

		function getCd_localidad() {
			return $this->cd_localidad;
		}
		
		function getDs_localidad() {
			return $this->ds_localidad;
		}
		
		function getCd_provincia() {
			return $this->oProvincia->getCd_provincia();
		}
		
		function getDs_provincia() {
			return $this->oProvincia->getDs_provincia();
		}
		
		function getProvincia() {
			return $this->oProvincia;
		}
		function getPais() {
			return $this->oProvincia->getPais();
		}
		function getDs_Pais() {
			return $this->oProvincia->getDs_pais();
		}
		
		//Métodos Set 
		

		function setCd_localidad($value) {
			$this->cd_localidad = $value;
		}
		
		function setDs_localidad($value) {
			$this->ds_localidad = $value;
		}
		
		function setCd_provincia($value) {
			$this->oProvincia->setCd_provincia( $value );
		}
		
		function setProvincia($value) {
			$this->oProvincia = $value ;
		}
	
	}

