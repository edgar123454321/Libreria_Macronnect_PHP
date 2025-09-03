<?php 

    class Functions {
        public static function generate_uuid() {
            return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),       // 32 bits
                mt_rand(0, 0xffff),                           // 16 bits
                mt_rand(0, 0x0fff) | 0x4000,                  // versión 4
                mt_rand(0, 0x3fff) | 0x8000,                  // variante
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff) // 48 bits
            );
        }
        
        public static function cleanOldFolders(string $rutaBase, int $diasMaximos): void {
            $tiempoActual = time();
            $segundos = $diasMaximos * 24 * 60 * 60;

            if (!is_dir($rutaBase)) {
                echo "La ruta no es un directorio válido.\n";
                return;
            }

            foreach (scandir($rutaBase) as $carpeta) {
                $rutaCompleta = $rutaBase . DIRECTORY_SEPARATOR . $carpeta;

                if ($carpeta === '.' || $carpeta === '..') {
                    continue;
                }

                if (is_dir($rutaCompleta)) {
                    $tiempoModificacion = filemtime($rutaCompleta);

                    if (($tiempoActual - $tiempoModificacion) > $segundos) {
                        Functions::eliminarDirectorio($rutaCompleta);
                    }
                }
            }
        }

        // Función recursiva para eliminar un directorio y su contenido
        public static function eliminarDirectorio(string $ruta): void {
            foreach (scandir($ruta) as $archivo) {
                if ($archivo === '.' || $archivo === '..') {
                    continue;
                }

                $rutaArchivo = $ruta . DIRECTORY_SEPARATOR . $archivo;

                if (is_dir($rutaArchivo)) {
                    eliminarDirectorio($rutaArchivo); // Recursivo
                } else {
                    unlink($rutaArchivo);
                }
            }
            rmdir($ruta);
        }
                
                
                
    }

?>