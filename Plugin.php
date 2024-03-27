<?php

namespace CreateGeoDivisions;

use MapasCulturais\i;
use MapasCulturais\App;
use MapasCulturais\Controllers\File;

class Plugin extends \MapasCulturais\Plugin
{   
    const IMPORTED_FILES_PATH = PRIVATE_FILES_PATH.'geo_division_imported_files/';
    const IMPORT_FILES_PATH = __DIR__.'/import-files/';

    function __construct()
    {   
        if (!is_dir(self::IMPORTED_FILES_PATH)) {
            mkdir(self::IMPORTED_FILES_PATH);        
        }

        parent::__construct();
    }

    function _init() 
    {
    }

    function register()
    {
    }
}