<?php

namespace App\Model;

use JetBrains\PhpStorm\NoReturn;
use Library\Core\AbstractModel;

class AdminModel extends AbstractModel
{


    public function createAdmin (array $data, int $role_id): ?int
    {

        // Insert the new admin record
        $adminId = $this->db->execute('INSERT INTO `admin` (email, password, firstName, lastName, role_id) VALUES (:email, :password, :firstName, :lastName, :role_id);', [
            'email' => $data['email'],
            'password' => $data['password'],
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName'],
            'role_id' => $role_id
        ]);

        // Check if the admin record was inserted successfully
        if (empty($adminId)) {
            return null;
        }

        return $adminId;
    }


    public function findAdminById (int $id): ?array
    {
        $user = $this->db->getResults('SELECT * FROM `admin` WHERE `id` = :id;', [
            'id' => $id
        ]);
        if (!$user) {
            return null;
        }
        return $user[0];
    }

    public function findAdminByEmail (string $email): ?array
        {
            $user = $this->db->getResults('SELECT * FROM `admin` WHERE `email` = :email;', [
                'email' => $email
            ]);
            if (!$user) {
                return null;
            }
            return $user[0];
        }


    public function getRoles (): array
    {
        return $this->db->getResults('SELECT * FROM `roles` ORDER BY `name` ASC;');
    }
    /**
     * Get all users
     * @return array
     */

    public function getAllUsers(): array
    {
        $users = $this->db->getResults('SELECT id, email, firstName, lastName, role_id FROM users ORDER BY id DESC;');
        if (!$users) {
            return [];
        }
        return $users;
    }


    public function createRole(array $data): ?int
    {
        $role_id = $this->db->execute('INSERT INTO `roles` (name, type_project) VALUES (:name, :type_project);', [
            'name' => $data['name'],
            'type_project' => $data['type_project']
        ]);
        if (!$role_id) {
            return null;
        }
        return $role_id;
    }

    public function updateResetTokenAdmin(mixed $id, string|null $token, string|null $date): void
    {
        $this->db->execute('UPDATE admin SET reset_token = :token, reset_token_expiry = :date WHERE id = :id', [
            'token' => $token,
            'date' => $date,
            'id' => $id
        ]);
    }


    public function updatePasswordAdmin(mixed $id, string $password_hash): void
    {
        $this->db->execute('UPDATE admin SET password = :password WHERE id = :id', [
            'password' => $password_hash,
            'id' => $id
        ]);
    }




    public function findAdminByResetToken(mixed $token)
    {
        $admin = $this->db->getResults(
            sql: 'SELECT * FROM admin WHERE reset_token = :token AND reset_token_expiry >= NOW()',
            parameters: ['token' => $token]
        );
        if (empty($admin)) {
            return null;
        }
        return $admin[0];
    }

    public function getAllAdmins(): array
    {
        $admins = $this->db->getResults('SELECT id, email, firstName, lastName, role_id  FROM `admin` ORDER BY `id` DESC;');
        if (!$admins) {
            return [];
        }
        return $admins;
    }

    public function getRoleById(mixed $role_id)
    {
        $role = $this->db->getResults('SELECT * FROM `roles` WHERE `id` = :id;', [
            'id' => $role_id
        ]);
        if (!$role) {
            return null;
        }
        return $role[0];
    }

    public function getAdminById(mixed $admin_id)
    {
        $admin = $this->db->getResults('SELECT * FROM `admin` WHERE `id` = :id;', [
            'id' => $admin_id
        ]);
        if (!$admin) {
            return null;
        }
        return $admin[0];
    }

    public function getUserById(int $id): ?array
    {
        $user = $this->db->getResults('SELECT * FROM `users` WHERE `id` = :id;', [
            'id' => $id
        ]);
        if (!$user) {
            return null;
        }
        return $user[0];
    }




    public function updateAdmin(mixed $id, array $array): void
    {
       $this->db->execute('UPDATE admin SET email = :email, firstName = :firstName, lastName = :lastName, role_id = :role_id WHERE id = :id', [
            'email' => $array['email'],
            'firstName' => $array['firstName'],
            'lastName' => $array['lastName'],
            'role_id' => $array['role_id'],
            'id' => $id
        ]);


    }

    public function getAllRoles(): array
    {
        $roles = $this->db->getResults('SELECT * FROM `roles` ORDER BY `id` DESC;');
        if (!$roles) {
            return [];
        }
        return $roles;
    }

    public function getAllRolesName(): array
    {
        $roles = $this->db->getResults('SELECT id, type_project FROM roles WHERE type_project NOT IN ("name", "user", "ALL")');

        // Convert the array to an associative array with id as keys and type_project as values
        $rolesName = array_combine(array_column($roles, 'id'), array_column($roles, 'type_project'));

        return $rolesName;
    }

    public function deleteUser(mixed $user_id): void
    {
        $this->db->execute('DELETE FROM `users` WHERE `id` = :id;', [
            'id' => $user_id
        ]);
    }

    public function deleteAdmin(mixed $admin_id): void
    {
        $this->db->execute('DELETE FROM `admin` WHERE `id` = :id;', [
            'id' => $admin_id
        ]);
    }




}
