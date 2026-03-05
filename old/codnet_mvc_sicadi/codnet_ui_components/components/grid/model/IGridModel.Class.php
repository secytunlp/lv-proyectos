<?php

/**
 * Modelo para la grilla 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 14-12-2011
 *
 */
interface IGridModel {

		/**
		 * @return cantidad de columnas de la grilla
		 */
		public function getColumnCount( );
	
		/**
		 * retorna el columnmodel de una columna dado el índice.
		 * @param int $index indice de la columna
		 * @return GridColumnModel 
		 */
		public function getColumnModel( $index  );
		
		/**
		 * retorna la colección de columns model
		 * @return ItemCollection[GridColumnModel] 
		 */
		public function getColumnsModel();
		
		/**
		 * retorna el valor de la celda dado el índice de fila y columna
		 * @param $rowIndex índice de la fila
		 * @param $columnIndex índice de la columna
		 * @return anObject.
		 */
		public function getValueAt($rowIndex, $columnIndex);
		
		/**
		 * retorna el valor de la entitdad dado el índice de columna
		 * @param oEntity $oEntity entitdad
		 * @param int $columnIndex índice de la columna
		 * @return anObject.
		 */
		public function getValue($oEntity, $columnIndex);
		
		/**
		 * retorna el manager de las entities a mostrar.
		 * @return anObject
		 */
		public function getEntityManager();
		
		
		/**
		 * entities de a mostrar en la grilla.
		 * @return ItemCollection
		 */
		public function getEntities();
		
		/**
		 * cantidad total de entities.
		 * @return int.
		 */
		public function getTotalRows();

		/**
		 * cantidad total de filas.
		 * @return int.
		 */
		public function getRowsCount();
		
		/**
		 * retorna el id de la entidad
		 * @param anObject $oEntity
		 * @return int.
		 */
		public function getEntityId( $oEntity );
		
		/**
		 * retorna el campo default por el cual ordenar la grilla
		 * @return string
		 */
		public function getDefaultOrderField();
		
		/**
		 * retorna el campo default por el cual filtrar la grilla
		 * @return string
		 */
		public function getDefaultFilterField();

		
		/**
		 * @return cantidad de filtros de la grilla
		 */
		public function getFiltersCount( );
	
		/**
		 * retorna el filter model de un filtro dado el índice.
		 * @param int $index indice del filtro
		 * @return GridFilterModel 
		 */
		public function getFilterModel( $index  );
		
		/**
		 * retorna la colección de columns model
		 * @return ItemCollection[GridFilterModel] 
		 */
		public function getFiltersModel();
		
		/**
		 * @return cantidad de actions de la grilla
		 */
		public function getActionsCount( );
	
		/**
		 * retorna el action model de un action dado el índice.
		 * @param int $index indice del action
		 * @return GridActionModel 
		 */
		public function getActionModel( $index  );
		
		/**
		 * retorna la colección de actions model
		 * @return ItemCollection[GridActionModel] 
		 */
		public function getActionsModel();
		
		/**
		 * @return cantidad de actions para una fila de la grilla
		 */
		public function getRowActionsCount( );
	
		/**
		 * retorna el action model de un action de una fila dado el índice.
		 * @param int $index indice del action
		 * @return GridActionModel 
		 */
		public function getRowActionModel( $index  );
		
		/**
		 * retorna la colección de actions model para las filas
		 * @return ItemCollection[GridActionModel] 
		 */
		public function getRowActionsModel( $item );
		
		/**
		 * título de la grilla
		 * @return string
		 */
		public function getTitle();
		
		/**
		 * para mostrar filtros adicionales, particulares de cada grilla..
		 * @return string (html)
		 */
		public function getCustomFilters();
		
		/**
		 * resetea las actions definidas
		 */
		public function resetActions();
		
		/**
		 * agrega filtros al criterio de búsqueda.
		 * @param CdtSearchCriteria $oCriteria
		 */
		public function enhanceCriteria( CdtSearchCriteria $oCriteria );

		/**
		 * retorna la clase estilo css de la fila dado el item
		 * @return  
		 */
		public function getRowStyleClass( $item );
		
		/**
		 * retorna el contenido a mostrar en el encabezado
		 * de la grilla
		 */
		public function getHeaderContent();
		
		/**
		 * retorna el contenido a mostrar en el pie
		 * de la grilla
		 */
		public function getFooterContent();
}