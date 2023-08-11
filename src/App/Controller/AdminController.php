<?php

namespace App\Controller;

use App\Model\DocumentModel;
use App\Model\ProjectModel;
use JetBrains\PhpStorm\NoReturn;
use Library\Core\AbstractController;
use App\Model\UserModel;
use App\Model\AdminModel;
use Library\Auth\Authenticate;

class AdminController extends AbstractController
{
    /**
     * Method for login admin
     * @return void
     */
    public function loginAdmin (): void
    {
        // if a user is already logged in, redirect to home page.
        if (auth()->isAuthenticated()) {
            $this->redirect('/admin-dashboard');
        }

        $this->display('admin/admin-login');

        $modelAdmin = new AdminModel();
        $admin = $modelAdmin->findAdminByEmail($_POST['email']);
        //if the user does not exist or the password is incorrect, redirect to login page with an error message.
        if (empty($admin) || !password_verify($_POST['password'], $admin['password'])) {
            $_SESSION['alert'] = [
                'error' => 'Wrong email or password'];
            $this->redirect('/admin');
        } else {
            auth()->login($admin['id']);
            if ($admin['firstName'] && isset($admin['lastName']) !== null) {
                $_SESSION['userFullName'] = $admin['firstName'] . ' ' . $admin['lastName'];
            }
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['user_email'] = $admin['email'];
            $_SESSION['admin'] = true;
            if ($admin['role_id'] === 1) {
                $_SESSION['superAdmin'] = true;
            }
            $roles = $modelAdmin->getRoles();
            foreach ($roles as $role) {
                if ($role['id'] === $admin['role_id']) {
                    $_SESSION['type_project'] = $role['id'];
                }
            }



            $_SESSION['alert'] = [
                'success' => "Succès ! Vous vous êtes connecté avec succès " . (auth()->getFullName() ? auth()->getFullName() : auth()->getEmail())
            ];


            $this->redirect('/admin-dashboard');
        }



    }

    /**
     * Method for show admin dashboard
     * @return void
     */
    public function adminDashboard (): void
    {
        // if a user is not admin, redirect to home page
        if (!auth()->isAdmin()) {
            $this->redirect('/');
        }
        $modelAdmin = new AdminModel();
        $roles = $modelAdmin->getRoles();
        $adminId = auth()->getUserId();

        $users = $modelAdmin->getAllUsers();
        $admins = $modelAdmin->getAllAdmins();


        $this->display('admin/admin-dashboard',
            [
                'roles' => $roles,
                'user_id' => $adminId,
                'users' => $users,
                'admins' => $admins
            ]);

    }

    /**
     * Method for display admin page create user
     * @return void
     */

    public function createUser (): void
    {   $modelAdmin = new AdminModel();
        $roles = $modelAdmin->getRoles();
        $this->display('admin/admin-create-user',
            [
                'roles' => $roles
            ]);
    }

    /**
     * Method to store a new user in the database.
     * @return void
     */
    #[NoReturn] public function storeUser (): void
    {

        // if a user is not admin, redirect to home page
        if (!auth()->isAdmin()) {
            $this->redirect('/');
        }

        // Instantiate UserModel
        $modelUser = new UserModel();

        // Create a new user with the provided data and store the result.
        $user = $modelUser->createUser([
            'email' => $_POST['email'],
            'password' => password_hash($_POST['password'], PASSWORD_ARGON2ID),
            'role_id' => $_POST['role_id'],
            'firstName' => ($_POST['firstName'] ?? 'Anonymous'),
            'lastName' => ($_POST['lastName'] ?? 'Anonymous'),
        ]);


        // If a user is created, redirect to admin page with success or error message.
        if ($user) {
            $_SESSION['alert'] = [
                'success' => 'Utilisateur créé avec succès'];
        } else {
            $_SESSION['alert'] = [
                'error' => 'La création de l\'utilisateur a échoué'];
        }

        $this->redirect('/admin');

    }


    /**
     * Method to store a new admin in the database.
     * @return void
     */
    #[NoReturn] public function storeAdmin (): void
    {
        if (!auth()->isAdmin()) {
            $this->redirect('/');
        }
        $modelAdmin = new AdminModel();
        if (!empty($_POST['role_new']) || !empty($_POST['role_id'])) {
            if (!empty($_POST['role_new'])) {
                $role_id = $modelAdmin->createRole(
                    [
                        'name' => $_POST['role_new'],
                        'type_project' => $_POST['type_project'] ?? 'all',
                    ]
                );
            } else {
                $role_id = $_POST['role_id'];
            }

            $admin_id = $modelAdmin->createAdmin([
                'email' => $_POST['email'],
                'password' => password_hash($_POST['password'], PASSWORD_ARGON2ID),
                'firstName' => $_POST['firstName'] ?? 'Anonymous',
                'lastName' => $_POST['lastName'] ?? 'Anonymous',

            ], $role_id);

            if ($admin_id) {
                $_SESSION['alert'] = [
                    'success' => 'Admin créé avec succès'];
            } else {
                $_SESSION['alert'] = [
                    'error' => 'La création d\'admin a échoué'];
            }

            $this->redirect('/admin');

        } else if ($_POST['role_id'] !== null) {
            $admin_id = $modelAdmin->createAdmin([
                'email' => $_POST['email'],
                'password' => password_hash($_POST['password'], PASSWORD_ARGON2ID),
                'firstName' => $_POST['firstName'] ?? 'Anonymous'
                ,
                'lastName' => $_POST['lastName'] ?? 'Anonymous'
            ], $_POST['role_id']);

            if ($admin_id) {
                $_SESSION['alert'] = [
                    'success' => 'Admin créé avec succès'];
            } else {
                $_SESSION['alert'] = [
                    'error' => 'La création d\'admin a échoué'];
            }

            $this->redirect('/admin');
        }

        $roles = $modelAdmin->getRoles();
        $this->display('admin/dashboard-admin',
            [
                'roles' => $roles,
            ]
        );
    }


    /**
     * Method to display page to create a new project.
     * @return void
     */
    public function formCreateProject (): void
    {
        $modelAdmin = new AdminModel();
        $users = $modelAdmin->getAllUsers();
        $modelAdmin = new AdminModel();
        $admins = $modelAdmin->getAllAdmins();

        $types = $modelAdmin->getAllRolesName();

        unset($types[1], $types[2]);




        $this->display('admin/admin-create-project',
            [
                'users' => $users,
                'admins' => $admins,
                'types' => $types
            ]);
    }

    /**
     * Method to store a new project in the database.
     * @return void
     */
    public function storeProject(): void
    {
        $modelProject = new ProjectModel();
        $project_data = [
            'name' => $_POST['name'],
            'comment' => $_POST['comment'],
            'period' => $_POST['period'],
            'type_id' => $_POST['type_id'],
            'link_snapshot' => $_POST['link_snapshot'],
            'link_dashboard' => $_POST['link_dashboard']
        ];
        if (isset($_POST['checkbox_user'])) {
            $project_data['user_id'] = $_POST['user_id'];
        } elseif (isset($_POST['checkbox_admin'])) {
            $project_data['admin_id'] = $_POST['admin_id'];
        }
        $project = $modelProject->addProject($project_data);



        if ($project) {
            $_SESSION['alert'] = [
                'success' => 'Création du projet réussie'];
        } else {
            $_SESSION['alert'] = [
                'error' => 'Création du projet échouée'];
        }
        $this->redirect('/admin-dashboard');

    }



    /**
     * Method to Update a project of user in the database.
     * @return void
     */

    public function updateProject(): void
    {
        $projectId = $_POST['id'];
        $project = (new ProjectModel())->getProjectById($projectId);
        $data = [
            'name' => $_POST['name'],
            'comment' => $_POST['comment'],
            'period' => $_POST['periode'],
            'type_id' => $project['type_id'], // can't be changed
            'link_snapshot' => $_POST['link_snapshot'],
            'link_dashboard' => $_POST['link_dashboard'],
            'id' => $projectId
        ];

        // Mise à jour du projet
        $modelProject = new ProjectModel();
        $modelProject->updateProject($projectId, $data);

        $_SESSION['alert'] = [
            'success' => 'Projet mis à jour avec succès'
        ];
        $this->redirect('/admin-dashboard');
        if ($project) {
            $_SESSION['alert'] = [
                'success' => 'Mise à jour du projet réussie'];
        } else {
            $_SESSION['alert'] = [
                'error' => 'Mise à jour du projet échouée'];
        }
        $this->redirect('/admin-dashboard');
    }




    /**
     * Method to get projects by user ID – for javascript
     * @return void
     */
    public function getProjects(): void
    {
        if (isset($_GET['user_id'])) {
            $user_id = $_GET['user_id'];
            $modelProject = new ProjectModel();
            $projects = $modelProject->getProjectsByUserId($user_id);
            echo json_encode($projects);
        } elseif (isset($_GET['admin_id'])) {
            $admin_id = $_GET['admin_id'];
            $modelProject = new ProjectModel();
            $projects = $modelProject->getProjectsByAdminId($admin_id);

            echo json_encode($projects);
        }
        else {
            echo "Aucun ID d'utilisateur n'a été transmis.";
        }
    }

    /**
     * Method to display the admin profile page
     * @return void
     */
    public function myAdminProfile(): void
    {
        if (!auth()->isAdmin()) {
            $this->redirect('/');
        }
        $modelAdmin = new AdminModel();
        $id = $_GET['id'];
        $admin = $modelAdmin->findAdminById($id);
        $roles = $modelAdmin->getRoles();
        $this->display('admin/admin-profile',
            [
                'admin' => $admin,
                'roles' => $roles
            ]
        );
    }

    /**
     * Method to display the edit page for an admin
     * @return void
     */
    #[NoReturn] public function formEditAdmin(): void
    {

        // if a user is not admin, redirect to home page
        if (!auth()->isAdmin()) {
            $this->redirect('/');
        }

        $modelAdmin = new AdminModel();

        // Get the admin with the provided ID and store the result.
        $admin = $modelAdmin->findAdminById(auth()->getUserId());

        // If admin is found, display the edit page with the admin data.
        if ($admin) {
            $roles = $modelAdmin->getRoles();

            $this->display('admin/admin-edit',
                [
                    'admin' => $admin,
                    'roles' => $roles
                ]
            );
        } else {
            $_SESSION['alert'] = [
                'error' => "Admin n'a pas été trouvé"];
            $this->redirect('/admin-dashboard');
        }


    }

/**
     * Method to update admin info.
     * @return void
     */
    public function updateAdmin(): void
    {
        // Ensure only admins can access this method.
        if (!auth()->isAdmin()) {
            $this->redirect('/');
            return;
        }

        $modelAdmin = new AdminModel();

        // Get the current admin details.
        $adminOld = $modelAdmin->findAdminById(auth()->getUserId());

        // Prepare the updated admin data directly.
        $updatedAdminData = [
            'email' => $_POST['email'] ?? $adminOld['email'],
            'firstName' => $_POST['firstName'] ?? $adminOld['firstName'],
            'lastName' => $_POST['lastName'] ?? $adminOld['lastName'],
            'role_id' => $_POST['role_id'] ?? $adminOld['role_id']
        ];


        // Update the admin details.
        $modelAdmin->updateAdmin($_POST['id'], $updatedAdminData);

        // Fetch the updated details.
        $adminNew = $modelAdmin->findAdminById($_POST['id']);

        // Check if any details were updated.
        $diff = array_diff_assoc($adminOld, $adminNew);

        // Set the appropriate alert message.
        if (count($diff) > 0) {
            $_SESSION['alert'] = ['success' => 'Vous avez modifié les données de l\'administrateur.'];

            // Update the session name if the current admin updated their own details.
            if (intval($_POST['id']) === $_SESSION['user_id']) {
                $_SESSION['userFullName'] = $updatedAdminData['firstName'] . ' ' . $updatedAdminData['lastName'];
            }
        } else {
            $_SESSION['alert'] = ['error' => 'Vous n\'avez pas modifié les données de l\'administrateur.'];
        }

        // Redirect to the admin dashboard.
        $this->redirect('/admin-dashboard');
    }




    /**
     * Method to delete Admin from the database.
     * Only super admin can delete an admin.
     * And admin with the same role as a user can delete a user.
     * @return void
     */
    #[NoReturn] public function deleteUser(): void
    {
        if (!auth()->isAdmin()) {
            $this->redirect('/');
        }

        if (isset($_GET['user_id'])) {
            $modelAdmin = new AdminModel();
            $user_id = $_GET['user_id'];
            $user = $modelAdmin->findAdminById($user_id);
            $modelAdmin->deleteUser($user_id);
            $_SESSION['alert'] = [
                'success' => 'Utilisateur supprimé avec succès'];
            // same for admin
        }elseif (isset($_GET['admin_id'])) {
            $modelAdmin = new AdminModel();
            $admin_id = $_GET['admin_id'];
            $admin = $modelAdmin->findAdminById($admin_id);
            $modelAdmin->deleteAdmin($admin_id);
            $_SESSION['alert'] = [
                'success' => 'Administrateur supprimé avec succès'];
        }



        $this->redirect('/admin-dashboard');
    }


    /**
     * Method to display the admin create document page
     * @return void
     */
    public function formCreateDocument(): void
    {
        if (!auth()->isAdmin()) {
            $this->redirect('/');
        }

        $modelAdmin = new AdminModel();
        $roles = $modelAdmin->getRoles();
        $users = $modelAdmin->getAllUsers();
        $admins = $modelAdmin->getAllAdmins();



        $this->display('admin/admin-create-document', [
            'users' => $users,
            'admins' => $admins,
        ]);

    }

    /**
     * Method to store a document in the database.
     * Only super admin can store a document for any admin and user.
     * and admin with the same role as a user can store a document for a user.
     * @return void
     */

    public function storeDocument(): void
    {
        $userId = null;
        $adminId = null;

        if(isset($_POST['checkbox_user']) && $_POST['checkbox_user'] == 'on') {
            $userId = $_POST['user_id'];
        } elseif(isset($_POST['checkbox_admin']) && $_POST['checkbox_admin'] == 'on') {
            $adminId = $_POST['admin_id'];
        }

            $file_size = $_FILES['document']['size'];
            if ($file_size > 1000000) {
                $_SESSION['alert'] = [
                    'error' => 'Le fichier est trop volumineux.'];
                $this->redirect('/admin-create-document');
            }

            // Check file type
            $file_type = $_FILES['document']['type'];
            if ($file_type !== 'application/pdf') {
                $_SESSION['alert'] = [
                    'error' => 'Le fichier doit être au format PDF.'];
                $this->redirect('/admin-create-document');
            }


            $file_name = $_FILES['document']['name'];


            $new_file_name = md5(uniqid()) . '.' . pathinfo($file_name, PATHINFO_EXTENSION);


            $destination_folder = './asset/documents/';
            if (move_uploaded_file($_FILES['document']['tmp_name'], $destination_folder . $new_file_name)) {

                $modelDocument = new DocumentModel();
                $document = $modelDocument->addDocument([
                    'user_id' => $userId,
                    'admin_id' => $adminId,
                    'project_id' => $_POST['project_id'],
                    'name' => $_POST['name'],
                    'period' => $_POST['period'],
                    'comment' => $_POST['comment'],
                    'pdf' => $new_file_name
                ]);
                if($document){
                    $_SESSION['alert'] = [
                        'success' => 'Document ajouté avec succès'];
                }else{
                    $_SESSION['alert'] = [
                        'error' => 'Erreur lors de l\'ajout du document'];
                }
                $this->redirect('/admin-dashboard');
            }
        }

    /**
     * Method to update document page
     * @return void
     */
    public function updateDocument(): void
        {
            $modelDocument = new DocumentModel();
            $id = $_POST['id'];
            $modelDocument->updateDocument($id, [
                'name' => $_POST['name'],
                'project_id' => $_POST['project_id'],
                'period' => $_POST['period']
            ]);
            $_SESSION['alert'] = [
                    'success' => 'Document modifié avec succès'];
            $this->redirect('/admin-dashboard');




        }

    /**
     * Method to show all user/admin info with his projects and documents
     * @return void
     */
    public function userDashboard(): void
    {
        if (!auth()->isAdmin()) {
            $this->redirect('/');
        }


        if (isset($_GET['user_id'])) {
            $id = $_GET['user_id'];
            $modelAdmin = new AdminModel();
            $user = $modelAdmin->getUserById($id);
            $modelProject = new ProjectModel();
            $projects = $modelProject->getProjectsByUserId($id);
            $modelDocument = new DocumentModel();
            $documents = $modelDocument->getDocumentsByUserId($id);


            $this->display('admin/user-info-dashboard', [
                'projects' => $projects,
                'documents' => $documents,
                'user' => $user

            ]);


        } elseif (isset($_GET['admin_id'])) {

            $id = $_GET['admin_id'];
            $modelAdmin = new AdminModel();
            $admin = $modelAdmin->getAdminById($id);
            $modelProject = new ProjectModel();
            $projects = $modelProject->getProjectsByAdminId($id);

            $modelDocument = new DocumentModel();
            $documents = $modelDocument->getDocumentsByAdminId($id);


            $this->display('admin/user-info-dashboard', [
                'projects' => $projects,
                'documents' => $documents,
                'admin' => $admin
            ]);
        } else {
            $_SESSION['alert'] = [
                'error' => 'Utilisateur non trouvé'];
            $this->redirect('/admin-dashboard');
        }
    }

    public function personalDashboard (): void
    {
        if (!auth()->isAdmin()) {
            $this->redirect('/');
        }
        $id = auth()->getUserId();

        $modelAdmin = new AdminModel();
        $admin = $modelAdmin->getAdminById($id);
        $modelProject = new ProjectModel();
        $projects = $modelProject->getProjectsByAdminId($id);
        $modelDocument = new DocumentModel();
        $documents = $modelDocument->getDocumentsByAdminId($id);



        $this->display('admin/personal-dashboard', [
            'projects' => $projects,
            'documents' => $documents,
            'admin' => $admin
        ]);
    }




















}
