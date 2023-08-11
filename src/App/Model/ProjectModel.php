<?php

namespace App\Model;

use JetBrains\PhpStorm\NoReturn;
use Library\Core\AbstractModel;

class ProjectModel extends AbstractModel
{
    /**
     * Function to get all projects of user from the database
     * @param mixed $id
     * @return array|null
     */
    public function getProjectsByUserId(mixed $user_id): ?array
    {
        $projects = $this->db->getResults('SELECT p.id, p.name, p.period, p.comment, p.type_id, p.link_snapshot, p.link_dashboard, p.user_id, r.type_project
            FROM projects p
            JOIN roles r ON p.type_id = r.id
            WHERE p.user_id = :user_id;', [
            'user_id' => $user_id
        ]);
        if (empty($projects)) {
            return [];
        }
        return $projects;
    }

    public function getProjectsByAdminId(mixed $admin_id): ?array
    {
        $projects = $this->db->getResults('SELECT p.id, p.name, p.period, p.comment, p.type_id, p.link_snapshot, p.link_dashboard, p.admin_id, r.type_project
            FROM projects p
            JOIN roles r ON p.type_id = r.id
            WHERE p.admin_id = :admin_id;', [
            'admin_id' => $admin_id
        ]);
        if (empty($projects)) {
            return [];
        }
        return $projects;
    }


    public function addProject(array $data): ?int
    {
        $user_id = null;
        $admin_id = null;
        if (isset($data['user_id'])) {
            $user_id = $data['user_id'];
        } elseif (isset($data['admin_id'])) {
            $admin_id = $data['admin_id'];
        }

        $projectId = $this->db->execute('INSERT INTO `projects` ( `name`, `period`, `comment`, `type_id`, `link_snapshot`, `link_dashboard`, `user_id`, `admin_id`) VALUES ( :name, :period, :comment, :type_id, :link_snapshot, :link_dashboard, :user_id, :admin_id);', [
            'name' => $data['name'],
            'period' => $data['period'],
            'comment' => $data['comment'],
            'type_id' => $data['type_id'],
            'link_snapshot' => $data['link_snapshot'],
            'link_dashboard' => $data['link_dashboard'],
            'user_id' => $user_id,
            'admin_id' => $admin_id
        ]);

        if (!$projectId) {
            return null;
        }

        return $projectId;
    }




    public function updateProject(int $id, array $data): void
    {
        $this->db->execute('UPDATE `projects` SET `name` = :name, `comment` = :comment, `period` = :period, `link_snapshot` = :link_snapshot, `link_dashboard` = :link_dashboard WHERE `id` = :id;', [
            'name' => $data['name'],
            'comment' => $data['comment'],
            'period' => $data['period'],
            'link_snapshot' => $data['link_snapshot'],
            'link_dashboard' => $data['link_dashboard'],
            'id' => $data['id']
        ]);
    }







    public function deleteProject(int $id): void
    {
        $this->db->execute('DELETE FROM `projects` WHERE `id` = :id;', [
            'id' => $id
        ]);
    }

    public function getProjectById(mixed $project_id): ?array
    {
        $project = $this->db->getResults('SELECT * FROM `projects` WHERE `id` = :project_id;', [
            'project_id' => $project_id
        ]);

        if (empty($project)) {
            return [];
        }

        return $project[0];
    }


    public function getAllProjects(): ?array
    {
        $projects = $this->db->getResults('SELECT * FROM `projects` ORDER BY `name` ASC;');
        if (empty($projects)) {
            return [];
        }
        return $projects;
    }

}
