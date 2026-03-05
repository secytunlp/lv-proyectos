<?php

/**
 * Objeto contenedor de objetos (colección)
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 10/03/2010
 *
 */
class ItemCollection implements Iterator {

	private $_items = null;

	public function __construct() {
		$this->_items = array();
	}

	public function addItem($value, $index=null){
		if(empty( $index ))
		$this->_items[] = $value;
		else
		$this->_items[$index] = $value;
	}

	public function push($value){
		array_push($this->_items, $value);
	}

	public function removeItem($i){
		unset($this->_items[array_search($i, $this->_items)]);
	}

	public function removeItemByKey($key){
		unset($this->_items[$key]);
	}

	public function rewind():void {
		reset($this->_items);
	}

	public function current(): mixed {

		return current($this->_items);
	}

	public function key():mixed {
		return key($this->_items);
	}

	public function next():void {
		 next($this->_items);
	}

	public function valid():bool {
		return $this->current() !== false;
	}

	public function isEmpty(){
		return empty( $this->_items );
	}

	public function existIndex($index){
		return array_key_exists( $index, $this->_items );
	}

	public function existObject($i){
		return in_array($i, $this->_items );
	}

	public function existObjectComparator($i, $comparator){
		foreach ($this->_items as $item){
			if ($comparator->equals($item,$i)) {
				return true;
			}
		}
		return false;
	}

	public function getObjectByIndex($index){
		return $this->_items[$index];
	}

	public function size(){
		return count($this->_items);
	}


	public function order ( $field, $inverse = false, $case_sensitive = true) {
		$position = array();
		$newRow = array();
		$currents = $this->_items;
		
		foreach ($currents as $key => $item) {
			$methodGet = ReflectionUtils::buildGetter( $field );
			
			if(!$case_sensitive){
				$position[$key ] = strtoupper(  ReflectionUtils::invoke( $item, $methodGet ) );
			}else{
				$position[$key]  = ReflectionUtils::invoke( $item, $methodGet );
			}
			$newRow[ $key ] = $item;
		}
		
		if ($inverse) {
			arsort($position);
		}
		else {
			asort($position);
		}
		
		$this->_items = array();
		foreach ($position as $key => $pos) {
			$this->_items[] = $newRow[$key] ;
		}
		
	}
	
	public function addAll($items){
		
		foreach ($items as $item) {
			$this->addItem( $item );
		}
			
	}
	
	public function fill( $field, $value, $case_sensitive = true) {
		$newRow = array();
		$currents = $this->_items;
		
		foreach ($currents as $key => $item) {
			$methodGet = ReflectionUtils::buildGetter( $field );
			
			$current_value = ReflectionUtils::invoke( $item, $methodGet );
			
			if(!$case_sensitive ){
				$current_value = strtoupper( $current_value );
				$value = strtoupper( $value );
			}
			if( $this->match( $current_value, $value ) )
				$newRow[] = $item;
		}
		
		$this->_items = $newRow;
				
	}	
	
	private function match($value, $pattern){
		   
		$value = str_replace('/', '', $value);
		$pattern = str_replace('/', '', $pattern);
		
 		if(strlen($value)>=strlen($pattern)){
 			
 			$valueSub = substr($value,0,strlen($pattern));
 			
 			return ($valueSub==$pattern);
 			
 		}
		return false;
   
	}
}
