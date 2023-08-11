<?php

	namespace Library\Session;

	class FlashBag
	{
		/**
		 * @param string $field
		 * @return string|null
		 */
		public function get (string $field): ?string
		{
			if (!isset($_SESSION['alert'][$field])) {
				return null;
			}

			$message = $_SESSION['alert'][$field];
			unset($_SESSION['alert'][$field]);

			return $message;
		}

		public function has (string $field): bool
		{
			return isset($_SESSION['alert'][$field]);
		}




	}