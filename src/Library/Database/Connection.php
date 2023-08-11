<?php

	namespace Library\Database;

	use PDO;

	class Connection
	{
		protected PDO $pdo;

		public function __construct (array $config)
		{
			$this->pdo = new PDO(
				"mysql:host={$config['host']};dbname={$config['dbname']};charset=UTF8",
				$config['user'],
				$config['password'], [
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
				]
			);
		}


		/**
		 * @param string $sql
		 * @param array|null $parameters
		 * @return array
		 */
		public function getResults (string $sql, ?array $parameters = null): array
		{
			$query = $this->pdo->prepare($sql);
			$query->execute($parameters);

			return $query->fetchAll();
		}


		public function execute (string $sql, ?array $parameters = null): string
		{
			$query = $this->pdo->prepare($sql);
			$query->execute($parameters);

			return $this->pdo->lastInsertId();
		}


		public function getPdo (): PDO
		{
			return $this->pdo;
		}


		public function setPdo (PDO $pdo): void
		{
			$this->pdo = $pdo;
		}

        /**
         * @param string $sql
         * @param array|null $parameters
         * @return mixed|null
         */
        public function getValue(string $sql, ?array $parameters = null): mixed
        {
            $query = $this->pdo->prepare($sql);
            $query->execute($parameters);

            $result = $query->fetch(PDO::FETCH_NUM);

            return $result !== false ? $result[0] : null;
        }


    }
