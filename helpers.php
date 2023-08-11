<?php

	use Library\Session\FlashBag;
	use Library\Auth\Authenticate;

	/**
	 * @param string $path
	 * @return string
	 */
	// looking for the path in the url
	function url (string $path): string
	{
		return $_SERVER['SCRIPT_NAME'].$path;
	}

	function auth (): Authenticate
	{
		return new Authenticate();
	}

	function flashBag (): FlashBag
	{
		return new FlashBag();
	}

