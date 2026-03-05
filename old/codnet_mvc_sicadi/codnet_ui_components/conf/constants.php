<?php
/**
 * se definen constantes para
 * el m�dulo de componentes.
 * 
 * @author bernardo
 * @since 19-12-2011
 * 
 */


define( 'CDT_CMP_DEFAULT_GRID_RENDERER',  'RichGridRenderer' );
define( 'CDT_CMP_DEFAULT_POPUP_GRID_RENDERER',  'FindPopupRichGridRenderer' );
define(	'CDT_CMP_DEFAULT_POPUP_MULTIPLE_GRID_RENDERER',  'FindPopupMultipleRichGridRenderer' );
define( 'CDT_CMP_DEFAULT_GRID_EXPORT_RENDERER',  'GridExportRenderer' );
define( 'CDT_CMP_DEFAULT_GRID_MODEL',  'GridModel' );

define( 'CDT_CMP_GRID_FILTER_TYPE_DATE', "date");
define( 'CDT_CMP_GRID_FILTER_TYPE_STRING', "string");
define( 'CDT_CMP_GRID_FILTER_TYPE_NUMBER', "number");
define( 'CDT_CMP_GRID_FILTER_TYPE_COMBOBOX', "combobox");
define( 'CDT_CMP_GRID_FILTER_TYPE_CHECKBOK', "checkbox");
define( 'CDT_CMP_GRID_FILTER_TYPE_RADIO', "radio");
define( 'CDT_CMP_GRID_FILTER_TYPE_DATETIME', "datetime");
define( 'CDT_CMP_GRID_FILTER_TYPE_TIME', "time");

define( 'CDT_CMP_GRID_TEXTALIGN_LEFT', "1");
define( 'CDT_CMP_GRID_TEXTALIGN_RIGHT', "2");
define( 'CDT_CMP_GRID_TEXTALIGN_CENTER', "3");

define( 'CDT_CMP_GRID_STYLE_CSS', WEB_PATH . "css/grid/grid.css");
//define( 'CDT_CMP_GRID_RICH_STYLE_CSS', WEB_PATH . "css/grid/rich_grid.css");
define( 'CDT_CMP_GRID_RICH_POPUP_STYLE_CSS', WEB_PATH . "css/grid/rich_grid_popup.css");
?>