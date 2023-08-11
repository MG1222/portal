<?php
namespace App\Model;

use JetBrains\PhpStorm\NoReturn;
use Library\Core\AbstractModel;

class UserModel extends AbstractModel
{

    public function createUser($data): ?int
    {
        $userId = $this->db->execute('INSERT INTO users (email, password, role_id, firstName, lastName) VALUES (:email, :password, :role_id, :firstName, :lastName)', [
            'email' => $data['email'],
            'password' => $data['password'],
            'role_id' => $data['role_id'],
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName']
        ]);

        //if no user is created, return null
        if (empty($userId)) {
            return null;
        }
        return $userId;
    }

    /**
     * Find a user by his email
     * @param string $email User email
     **/
    public function findByEmail (string $email): ?array
    {
        $user = $this->db->getResults(
            sql: 'SELECT * FROM users
				WHERE email = :email', parameters: [
            'email' => $email
        ]
        );

        if (empty($user)) {
            return null;
        }

        return $user[0];
    }


    /**
     * Find user by ID
     * @return array|null User id or null
     **/
    public function findById (int $id): ?array
    {
        $user = $this->db->getResults(
            sql: 'SELECT * FROM users WHERE id = :user_id', parameters: [
            'user_id' => $id
        ]);
        if (empty($user)) {
            return null;
        }
        return $user[0];
    }





    public function updateResetToken(mixed $id, string|null $token, string|null $date): void
    {
        $this->db->execute('UPDATE users SET reset_token = :token, reset_token_expiry = :date WHERE id = :id', [
            'token' => $token,
            'date' => $date,
            'id' => $id
        ]);
    }

    public function findByResetToken(string $token): ?array
    {
        $user = $this->db->getResults(
            sql: 'SELECT * FROM users WHERE reset_token = :token AND reset_token_expiry >= NOW()',
            parameters: ['token' => $token]
        );
        if (empty($user)) {
            return null;
        }
        return $user[0];
    }

    public function updatePassword(mixed $id, string $password_hash): void
    {
        $this->db->execute('UPDATE users SET password = :password WHERE id = :id', [
            'password' => $password_hash,
            'id' => $id
        ]);
    }

    public function updateUser(mixed $id, array $array): void
    {
       $this->db->execute('UPDATE users SET email = :email, firstName = :firstName, lastName = :lastName WHERE id = :id', [
            'email' => $array['email'],
            'firstName' => $array['firstName'],
            'lastName' => $array['lastName'],
            'id' => $id
        ]);

    }
}
