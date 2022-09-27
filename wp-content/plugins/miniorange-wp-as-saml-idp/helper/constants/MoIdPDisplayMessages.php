<?php

	namespace IDP\Helper\Constants;

	class MoIdPDisplayMessages
	{
		private $message;
		private $type;

		function __construct( $message,$type )
		{
	        $this->_message = $message;
	        $this->_type = $type;
	        add_action( 'admin_notices', array( $this, 'render' ) );
	    }

	    function render()
	    {
	    	switch ($this->_type)
	    	{
	    		case 'CUSTOM_MESSAGE':
	    			echo  esc_html($this->_message);																				break;
	    		case 'NOTICE':
	    			echo '<div  class="is-dismissible notice notice-warning mo-idp-note-endp mo-idp-margin-left"> <p>'.esc_html($this->_message).'</p> </div>';		break;
	    		case 'ERROR':
	    			echo '<div  class="notice notice-error is-dismissible mo-idp-note-error mo-idp-margin-left"> <p>'.esc_html($this->_message).'</p> </div>';		break;
	    		case 'SUCCESS':
	    			echo '<div  class="notice notice-success is-dismissible mo-idp-note-success mo-idp-margin-left"> <p>'.esc_html($this->_message).'</p> </div>';		break;
	    	}
	    }
	}