<?php

namespace IDP\Helper\Utilities;

use IDP\Helper\Traits\Instance;

final class RewriteRules
{
    use Instance;

    private function __construct()
	{
		add_filter('mod_rewrite_rules', array($this,'output_htaccess'));
	}

	function output_htaccess( $rules ) {
        $dirName = MSI_NAME;
        $new_rules = "IndexIgnore {$dirName}* actions controllers exception helper includes schedulers views *.php"."\n".
		             '<FilesMatch "\.(key)$">'."\n".
						'Order allow,deny'."\n".
						'Deny from all'."\n".
					 '</FilesMatch>';
		return $rules . $new_rules;
	}
}