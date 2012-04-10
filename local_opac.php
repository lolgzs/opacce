<?php
set_include_path('/var/www/html/vhosts/opac2/www/php/library:/var/www/html/vhosts/opac2/www/php/afi/library:.:' . 
                 get_include_path()) ;
define("CKBASEPATH",  '/var/www/html/vhosts/opac2/www/htdocs/ckeditor/') ;

// Creation des paths
define("MODULEDIRECTORY","../../php/afi/application/modules");
define("LANG_DIR", "../../php/afi/library/translation/");
define("SQLDOSSIER", "../../php/afi/library/define/");
define("PATH_TEMP",  "./temp/") ;
define("CKBASEURL",  '/ckeditor/') ;
define("URL_ADMIN_HTML", "/../php/afi/application/modules/admin/views/scripts/");
define('URL_FLASH', BASE_URL . '/afi/public/opacpriv/flash/');
define('PATH_FLASH', './afi/public/opacpriv/flash/');
?>