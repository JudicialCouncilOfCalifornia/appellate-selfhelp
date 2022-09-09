<?php

namespace IDP\Actions;

use IDP\Helper\Traits\Instance;

abstract class BasePostAction
{
    use Instance;

    protected $_nonce;

	public function __construct()
	{
		add_action( 'admin_init',  array( $this, 'handle_post_data' ),1 );
	}

	abstract function handle_post_data();

	abstract function route_post_data($option);
}