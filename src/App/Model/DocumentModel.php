<?php

namespace App\Model;

use JetBrains\PhpStorm\NoReturn;
use Library\Core\AbstractModel;

class DocumentModel extends AbstractModel
{
    /**
     * Function to get all projects of user from the database
     * @param int $id
     * @param string $idType
     * @return array
     */
    public function getDocumentsById(int $id, string $idType): array
    {
        $column = ($idType === 'user_id') ? 'd.user_id' : 'p.admin_id';
        $query = 'SELECT d.*, p.name AS project_name 
              FROM documents d 
              INNER JOIN projects p ON d.project_id = p.id 
              WHERE '.$column.' = :id';
        $params = ['id' => $id];
        return $this->db->getResults($query, $params);
    }


    public function getDocumentsByUserId(int $userId): array
    {
        $query = 'SELECT d.*, p.name AS project_name 
              FROM documents d 
              INNER JOIN projects p ON d.project_id = p.id 
              WHERE d.user_id = :user_id';
        $params = ['user_id' => $userId];
        return $this->db->getResults($query, $params);
    }

    public function getDocumentsByAdminId(int $adminId): array
    {
        $query = 'SELECT d.*, p.name AS project_name 
              FROM documents d 
              INNER JOIN projects p ON d.project_id = p.id 
              WHERE d.admin_id = :admin_id';
        $params = ['admin_id' => $adminId];
        return $this->db->getResults($query, $params);
    }



    public function addDocument(array $data): bool
    {
        $query = "
        INSERT INTO documents (user_id, admin_id, project_id, name, period, document)
        VALUES (:user_id, :admin_id, :project_id, :name, :period, :pdf)
    ";

        $params = [
            'name' => $data['name'],
            'project_id' => $data['project_id'],
            'period' => $data['period'],
            'user_id' => $data['user_id'] ?? null,
            'admin_id' => $data['admin_id'] ?? null,
            'pdf' => $data['pdf']
        ];

        return $this->db->execute($query, $params);
    }


    public function updateDocument(int $documentId, array $data): void
    {
        $query = 'UPDATE `documents` SET `name` = :name, `project_id` = :project_id, `period` = :period 
                   WHERE `documents`.`id` = :id;';
        $params = [
            'id' => $documentId,
            'name' => $data['name'],
            'project_id' => $data['project_id'],
            'period' => $data['period']
        ];

        $this->db->execute($query, $params);

    }
}
