<?php 
return [
    'fix geoLocations' => function() {
        $app = \MapasCulturais\App::i();
        DB_UPDATE::enqueue('Agent', 'status > 0', function (MapasCulturais\Entities\Agent $entity) use ($app) {
            
            $entity->save();
            echo $entity . "\n";
        });

        DB_UPDATE::enqueue('Space', 'status > 0', function (MapasCulturais\Entities\Space $entity) use ($app) {
            
            $entity->save();
            echo $entity . "\n";
        });
    },
];