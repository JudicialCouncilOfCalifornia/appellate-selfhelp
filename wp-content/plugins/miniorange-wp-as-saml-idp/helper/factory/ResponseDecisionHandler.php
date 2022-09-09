<?php

namespace IDP\Helper\Factory;

use IDP\Helper\Constants\MoIDPConstants;
use IDP\Helper\SAML2\GenerateResponse;
use IDP\Helper\WSFED\GenerateWsFedResponse;

class ResponseDecisionHandler
{

	public static function getResponseHandler($type,$args)
	{
		switch ($type)
		{

			case MoIDPConstants::SAML_RESPONSE:
				return new GenerateResponse($args[0],$args[1],$args[2],
											$args[3],$args[4],$args[5],
											$args[6]);  						break;
			case MoIDPConstants::WS_FED_RESPONSE:
				return new GenerateWsFedResponse($args[0],$args[1],$args[2],
												 $args[3],$args[4],$args[5],
												 $args[6]);						break;
		}
	}
}