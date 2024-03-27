<?php

use MapasCulturais\App;
use CreateGeoDivisions\Plugin;

return [
    'Cria novas geometrias baseado em divisões em uma planilha ' => function () {
        
        /** @var App $app */
        $app = App::i();

        /**
         * Array para armazenar os municípios separados por região
         */
        
        foreach (scandir(Plugin::IMPORT_FILES_PATH) as $file_name) {
            
            if (strtolower(substr($file_name, -4)) != ".csv") {
                continue;
            }
            echo "\n \n ========================================================";
            echo "\n Processando arquivo: $file_name";

            $file_name = Plugin::IMPORT_FILES_PATH . $file_name;
            $target_file = Plugin::IMPORTED_FILES_PATH . md5_file($file_name);
            if (file_exists($target_file)) {
                continue;
            }
    
            copy($file_name, $target_file);
    
            $file = fopen($file_name, 'r');
            $regioes = [];
            $nova_regiao = '';
            $filtro = '';
    
            $count = 0;
            while (($line = fgetcsv($file)) !== false) {  
                
                if ($count == 0) {
                    $filtro = $line[1];
                } else if ($count == 1) {
                    $nova_regiao = $line[1];
                } else {
                    if (!array_key_exists($line[1], $regioes)) {
                        $regioes[$line[1]] = [];
                    }
                    
                    $regioes[$line[1]][] = $line[0];
                }

                ++$count;
            }

            fclose($file);
            
            $conn = $app->em->getConnection();
            $conn->executeQuery("SELECT setval('geo_division_id_seq', (SELECT max(id) FROM geo_division))");
            
            foreach($regioes as $regiao => $municipios) {
                echo "\n Criando $nova_regiao: $regiao - Municípios: " . implode(', ', $municipios);

                $keys = [];
                
                foreach ($municipios as $municipio) {
                    $keys[uniqid()] = $municipio;
                }
                $in_municipios = 'lower(unaccent(:' . implode(')), lower(unaccent(:', array_keys($keys)) . '))';
                $sql = "INSERT INTO geo_division (type, cod, name, geom) VALUES ('$nova_regiao', '$regiao', '$regiao', (SELECT ST_Union(geom) AS poligono_unido FROM geo_division WHERE lower(unaccent(name)) IN($in_municipios) AND cod LIKE '$filtro%'))";
                $conn->executeQuery($sql, $keys);
            }
        }

        return false;
    },
];