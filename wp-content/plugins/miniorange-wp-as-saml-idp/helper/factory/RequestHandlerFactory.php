<?php

namespace IDP\Helper\Factory;

interface RequestHandlerFactory
{
	public function generateRequest();

	public function getRequestType();
}