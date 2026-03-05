<?php

/**
 * Acción para exportar a PDF una solicitud.
 *
 * @author Marcos
 * @since 27-09-2023
 */
class ViewFileAction extends CdtAction
{
    /**
     * (non-PHPdoc)
     * @see CdtAction::execute()
     */
    public function execute()
    {

        // Verificar si se proporciona un nombre de archivo en la URL (supongo que se pasa como parámetro GET)
        if (CdtUtils::getParam('archivo')) {
            $archivoSolicitado = CdtUtils::getParam('archivo');
            $dir = APP_PATH.'pdfs/'.CdtUtils::getParam('periodo').'/'.CdtUtils::getParam('documento').'/';
            //$dirREL = WEB_PATH.'pdfs/'.$oSolicitud->getPeriodo()->getDs_periodo().'/'.$oSolicitud->getDocente()->getNu_documento().'/';

            $rutaArchivo = $dir . $archivoSolicitado; // Reemplaza con la ruta real de tus archivos

            // Verificar si el archivo existe y es accesible
            if (file_exists($rutaArchivo)) {
                // Obtener la extensión del archivo
                $extension = pathinfo($archivoSolicitado, PATHINFO_EXTENSION);

                // Definir tipos MIME permitidos (añade o modifica según tus necesidades)
                $tiposMimePermitidos = [
                    'pdf' => 'application/pdf',
                    'doc' => 'application/msword',
                    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'rtf' => 'application/rtf',
                ];

                // Verificar si la extensión del archivo está en la lista de tipos MIME permitidos
                if (array_key_exists($extension, $tiposMimePermitidos)) {
                    // Servir el archivo si el usuario está autenticado, el archivo existe y la extensión es válida
                    header('Content-Type: ' . $tiposMimePermitidos[$extension]);
                    header('Content-Disposition: inline; filename="' . $archivoSolicitado . '"');
                    header('Content-Transfer-Encoding: binary');
                    header('Content-Length: ' . filesize($rutaArchivo));
                    readfile($rutaArchivo);
                    exit();
                } else {
                    // Si la extensión del archivo no es válida, mostrar un mensaje de error
                    echo 'Tipo de archivo no permitido.';
                }
            } else {
                // Si el archivo no existe, mostrar un mensaje de error
                echo 'El archivo solicitado no existe.';
            }
        } else {
            // Si no se proporciona un nombre de archivo en la URL, mostrar un mensaje de error
            echo 'No se especificó un archivo para descargar.';
        }

        // No es necesario redirigir o devolver un "forward" en este caso.
        return null;
    }
}
