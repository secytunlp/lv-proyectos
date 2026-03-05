<?php
	
	class Provincia {
		private $cd_provincia;
		private $ds_provincia;
		private $oPais;
		
		//Método constructor 
		

		function Provincia() {
			$this->cd_provincia = 0;
			$this->ds_provincia = '';
			$this->oPais = new Pais();
		}
		
		//Métodos Get 
		

		function getCd_provincia() {
			return $this->cd_provincia;
		}
		
		function getDs_provincia() {
			return $this->ds_provincia;
		}
		
		function getCd_pais() {
			return $this->oPais->getCd_pais();
		}
		
		function getDs_pais() {
			return $this->oPais->getDs_pais();
		}
		
		function getPais() {
			return $this->oPais;
		}
		
		//Métodos Set 
		

		function setCd_provincia($value) {
			$this->cd_provincia = $value;
		}
		
		function setDs_provincia($value) {
			$this->ds_provincia = $value;
		}
		
		function setCd_pais($value) {
			$this->oPais->setCd_pais( $value );
		}
		
		function setPais($value) {
			$this->oPais = $value ;
		}
	
	}

