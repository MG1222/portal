<?php

namespace App\Controller;

use App\Model\AdminModel;
use App\Model\ProjectModel;
use APP\Model\DocumentModel;
use JetBrains\PhpStorm\NoReturn;
use Library\Core\AbstractController;
use App\Model\UserModel;


class UserController extends AbstractController
{
    #[NoReturn] public function index(): void
    {
        if (auth()->isAuthenticated()) {
            $this->redirect('/dashboard');
            }
        $this->display('login');
    }

    public function login(): void
    {
        // Check if user is already authenticated, if yes, redirect to dashboard page.
        if (auth()->isAuthenticated()) {
            $this->redirect('/dashboard');
        }

        // Instantiate UserModel and find user by email
        $modelUser = new UserModel();
        $user = $modelUser->findByEmail($_POST['email']);

        // If a user is not found or password is incorrect, set an error alert.
        if (empty($user) || !password_verify($_POST['password'], $user['password'])) {
            $_SESSION['alert'] = [
                'error' => 'Wrong email or password'];
            $this->redirect('/');
        }
        // If login credentials are correct, log in user and set success alert in session.
        else {
            auth()->login($user['id']);
            if ($user['firstName'] && isset($user['lastName']) !== null) {
                $_SESSION['userFullName'] = $user['firstName'] . ' ' . $user['lastName'];
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];


            $_SESSION['alert'] = [
                'success' => 'Welcome back ' . (auth()->getFullName() ? auth()->getFullName() : auth()->getEmail())
            ];



            $this->redirect('/dashboard');
        }
    }



    public function forgotPassword(): void
    {
        $this->display('forgot-password');
    }

    /**
     * Change user or admin password by sending a reset password link to the provided email address.
     * @throws \Exception
     */
    public function changePassword(): void
    {
        // Initialize user and admin models
        $modelUser = new UserModel();
        $modelAdmin = new AdminModel();

        // Find user by email
        $user = $modelUser->findByEmail($_POST['email']);

        if (empty($user)) {
            // Find admin by email if user not found
            $admin = $modelAdmin->findAdminByEmail($_POST['email']);

            if (empty($admin)) {
                // If neither user nor admin found, show an error message and redirect.
                $_SESSION['alert'] = [
                    'error' => 'Cette adresse email n\'existe pas dans notre base de données.'
                ];
                $this->redirect('/user/forgot-password');
            } else {
                // If admin found, update reset token and send reset password link
                $this->sendResetPasswordLink($admin, $modelAdmin, true);
            }
        } else {
            // If user found, update reset token and send reset password link
            $this->sendResetPasswordLink($user, $modelUser, false);
        }
    }

    /**
     * Send reset password link to the user or admin.
     *
     * @param array $recipient User or admin data
     * @param object $model User or admin model
     * @param bool $isAdmin True if recipient is admin, false if user
     * @throws \Exception
     */
    private function sendResetPasswordLink(array $recipient, object $model, bool $isAdmin): void
    {
        // Create token and expiry date
        $token = $this->createToken();
        $expiryDate = date('Y-m-d H:i:s', strtotime('+24 hour'));

        // Update reset token for user or admin
        if ($isAdmin) {
            $model->updateResetTokenAdmin($recipient['id'], $token, $expiryDate);
        } else {
            $model->updateResetToken($recipient['id'], $token, $expiryDate);
        }

        // Create reset password link
        $resetLink = 'localhost:8888/portal-website/index.php/user/reset-password?token=' . $token;

        // Send reset password link via email
        $mailController = new MailController();
        $mailController->resetPasswordLink($recipient, $resetLink);

        // Show success message and redirect
        $_SESSION['alert'] = [
            'success' => 'Un lien de réinitialisation de mot de passe a été envoyé à votre adresse email.'
        ];
        $this->redirect('/');
    }

    /**
     * Display the reset password form for the user or admin.
     */
    #[NoReturn] public function resetPasswordForm(): void
    {
        $token = $_GET['token'];

        $modelUser = new UserModel();
        $modelAdmin = new AdminModel();

        // Find user or admin by reset token
        $user = $modelUser->findByResetToken($token);
        $admin = $modelAdmin->findAdminByResetToken($token);

        if (empty($user) && empty($admin)) {
            $_SESSION['alert'] = [
                'error' => 'The reset password link is invalid or expired. Please try again.'
            ];
            $this->redirect('/user/forgot-password');
        } else {
            $this->display('reset-password', [
                'token' => $token
            ]);
        }
    }

    /**
     * Update the password for the user or admin.
     */
    public function updatePassword(): void
    {
        $token = $_POST['token'];


        $modelUser = new UserModel();
        $modelAdmin = new AdminModel();

        // Find user or admin by reset token
        $user = $modelUser->findByResetToken($token);
        $admin = $modelAdmin->findAdminByResetToken($token);

        if (empty($user) && empty($admin)) {
            $_SESSION['alert'] = [
                'error' => 'Le lien de réinitialisation du mot de passe est invalide ou a expiré. Veuillez réessayer.'
            ];
            $this->redirect('/user/forgot-password');
        } else {
            if ($_POST['password'] !== $_POST['password_confirm']) {
                $_SESSION['alert'] = [
                    'error' => 'The passwords do not match.'
                ];
                $this->redirect('/user/reset-password?token=' . $token);
            }

            // Update the password and reset token for the user or admin.
            $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
            if (!empty($user)) {
                $modelUser->updatePassword($user['id'], $hashedPassword);
                $modelUser->updateResetToken($user['id'], null, null);
            } else {
                $modelAdmin->updatePasswordAdmin($admin['id'], $hashedPassword);
                $modelAdmin->updateResetTokenAdmin($admin['id'], null, null);
            }

            $_SESSION['alert'] = [
                'success' => 'Votre mot de passe a été mis à jour avec succès. Vous pouvez maintenant vous connecter.'
            ];
            $this->redirect('/');
        }
    }



    /**
     * Function to display the user registration page
     * @var UserModel
     * @var ProjectModel
     * @return void
     */
    public function dashboard(): void
    {
        if (!auth()->isAuthenticated()) {
            $this->redirect('/');
        }
        $user_id = auth()->getUserId();
        $modelUser = new UserModel();
        $user = $modelUser->findById($user_id);

        $projectModel = new ProjectModel();
        $projects = $projectModel->getProjectsByUserId($user_id);


        $document = new DocumentModel();
        $documents = $document->getDocumentsByUserId($user_id);

        $this->display('user/dashboard', [
            'projects' => $projects,
            'documents' => $documents,
            'user_id' => $user_id,
            'user' => $user
        ]);


    }

    public function showProject(): void
    {
        // Check if the user is authenticated, otherwise redirect to home page
        if (!auth()->isAuthenticated()) {
            $this->redirect('/');
        }

        $projectModel = new ProjectModel();

        // Get the project ID from the query string
        $project_id = $_GET['id'];

        // Get the project details by ID
        $project = $projectModel->getProjectById($project_id);

        $modelAdmin = new AdminModel();
        $rolesName = $modelAdmin->getAllRolesName();

        $type_name = $rolesName[$project['type_id']] ?? null;

        // Display the project details page with the project and project type name
        $this->display('user/project', [
            'project' => $project,
            'type_name' => $type_name
        ]);
    }



    public function showAllUserProjects(): void
    {
        if (!auth()->isAuthenticated()) {
            $this->redirect('/');
        }

        // Get user id from url
        $user_id = $_GET['id'];

        $projectModel = new ProjectModel();
        $projects = $projectModel->getProjectsByUserId($user_id);
       if (empty($projects)) {
          $projects = $projectModel->getProjectsByAdminId($user_id);

        }


        $this->display('user/my-projects', [
            'projects' => $projects
        ]);
    }

    /**
     * Display all documents for the user
     * @return void
     */
    public function showAllUserDocuments(): void
    {
        if (!auth()->isAuthenticated()) {
            $this->redirect('/');
        }

        // Get user id from url
        $user_id = $_GET['id'];

        $document = new DocumentModel();
        $documents = $document->getDocumentsByUserId($user_id);
        if (empty($documents)) {
            $documents = $document->getDocumentsByAdminId($user_id);
        }

        $this->display('user/my-documents', [
            'documents' => $documents
        ]);
    }


    /**
     * Method for logout
     * @return void
     */

    public function logout(): void
    {
        auth()->logout();
        $this->redirect('/');
    }

    /**
     * @throws \Exception
     */
    public function createToken(): string
    {
        return bin2hex(random_bytes(22));
    }



    /**
     * Display the user profile for edit
     */

    public function showEditUser(): void
    {
        // if not logged in, redirect to login page
        if (!auth()->isAuthenticated()) {
            $this->redirect('/');
        }
        // Instantiate UserModel
        $modelUser = new UserModel();
        $user = $modelUser->findById($_GET['id']);
        if (auth()->isAdmin()) {
            $modelAdmin = new AdminModel();
            $roles = $modelAdmin->getAllRoles();
        }

        $this->display('user/my-profile',
            [
                'user' => $user,
                'roles' => $roles ?? null,
            ]
        );
    }

    /**
     * Update the user profile.
     */

    public function update(): void
    {
        // If not authenticated, redirect to the home page.
        if (!auth()->isAuthenticated()) {
            $this->redirect('/');
            return;
        }

        // Instantiate UserModel and retrieve the old user data.
        $modelUser = new UserModel();
        $userId = $_POST['id'] ?? null;
        $userOld = $modelUser->findById($userId);

        if (!$userOld) {
            $_SESSION['alert'] = ['error' => 'Utilisateur non trouvé.'];
            $this->redirect('/');
            return;
        }

        // Prepare the updated user data.
        $updatedUserData = [
            'email' => $_POST['email'] ?? $userOld['email'],
            'firstName' => $_POST['firstName'] ?? $userOld['firstName'],
            'lastName' => $_POST['lastName'] ?? $userOld['lastName']
        ];


        // Update the user data in the database.
        $modelUser->updateUser($userId, $updatedUserData);

        // Retrieve the updated user data.
        $userNew = $modelUser->findById($userId);

        // Compare the old and new user data.
        $diff = array_diff_assoc($userOld, $userNew);

        // Update session based on the changes.
        if (count($diff) > 0) {
            $_SESSION['alert'] = ['success' => 'Vous avez modifié votre profil avec succès.'];
        } else {
            $_SESSION['alert'] = ['error' => 'Vous n\'avez pas pu modifié votre profil.'];
        }


        // Redirect the user or admin to the appropriate dashboard.
        if (auth()->isAdmin()) {
            $this->redirect('/admin-dashboard');
        } else {
            $_SESSION['userFullName'] = $userNew['firstName'] . ' ' . $userNew['lastName'];
            $this->display('user/my-profile', ['user' => $userNew]);
        }
    }


    public function editPassword(): void
    {
        // if a user is not admin, redirect to home page
        if (!auth()->isAuthenticated()) {
            $this->redirect('/');
        }

        // Instantiate UserModel
        $modelUser = new UserModel();

        // Get the user with the provided ID and store the result.
        $user = $modelUser->findById($_POST['id']);

       //Check if new password is the same as the old password
        if (password_verify($_POST['password'], $user['password'])) {
            $_SESSION['alert'] = [
                'error' => 'The new password cannot be the same as the old password.'];
            $this->redirect('/my-profile');
        }
        $modelUser->updatePassword($_POST['id'], password_hash($_POST['password'], PASSWORD_ARGON2ID));

        $_SESSION['alert'] = [
            'success' => 'Vous avez modifié votre mot de passe avec succès.'
        ];
        $this->display('user/my-profile',
            [
                'user' => $user,
                'roles' => $roles ?? null,
            ]
        );
    }



}
