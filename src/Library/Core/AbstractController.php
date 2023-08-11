<?php

	namespace Library\Core;

	use JetBrains\PhpStorm\NoReturn;

	class AbstractController
	{
        public function display(string $template, array $data = []): void
        {
            // Calculate the base path
            $base_path = dirname($_SERVER['SCRIPT_NAME']);

            // Extract data array to variables (optional, if you want to pass additional data to the view)
            extract($data);

            require "src/App/View/layout.phtml";
        }



        public function redirect (string $path): void
		{
			header('Location: '.url($path));
			exit();
		}
	}
