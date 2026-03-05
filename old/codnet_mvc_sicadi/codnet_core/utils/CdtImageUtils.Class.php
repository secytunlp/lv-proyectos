<?php

/**
 * Utilidades para el tratamiento de imágenes.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 10-03-2010
 */
class CdtImageUtils{
	
		
	public static function uploadImage($nombre_archivo_origen, $nombre_archivo_destino, $path_img_servidor,  $max_width=null){
			
		$destino = $path_img_servidor.$nombre_archivo_destino;
		
		$result=@move_uploaded_file($nombre_archivo_origen, $destino);
					
		//si se define un ancho máximo, se redefine el tamaño de la imagen.
		if($result && $max_width!=null){
			if ($max_width) {
				CdtImageUtils::resizeImage($destino,$max_width);
			}
			else
				CdtImageUtils::resizeImage($destino);
		}
			
		return $result;
	
	}
	
	public static function resizeImage($url_img, $new_width=110){
		$image = imagecreatefromjpeg($url_img);
			
		// Obtengo ancho y alto orginal
		$width = imagesx($image);
		$height = imagesy($image);

		if($width > $max_width){
		   	//$new_width = 110;
		    $new_height = $height * ($new_width/$width);
		
		    // Redimensiono
		    $image_resized = imagecreatetruecolor($new_width, $new_height);
		    imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		    imageJPEG($image_resized,"$url_img");
		}			
	}

	public static function thumbnail($url_origen, $url_destino, $width=100 ){
	
		$thumb=new Thumbnail( $url_origen ) ;
		
		if(!$thumb)
			return false;
		
		$thumb->size_width( $width);
		//$thumb->show();
		return $thumb->save( $url_destino );       // save my  thumbnail to file "huhu.jpg" in directory "/www/thumb 		
	}
}
