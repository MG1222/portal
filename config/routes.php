<?php

	return [
		'/' => [
            'App\Controller\UserController',
			'index'
		],
        '/user/login' => [
            'App\Controller\UserController',
            'login'
        ],
        '/my-profile' => [
            'App\Controller\UserController',
            'ShowEditUser'
        ],
        '/my-profile/edit' => [
            'App\Controller\UserController',
            'showEditUser'
        ],
        '/my-profile/update' => [
            'App\Controller\UserController',
            'update'
        ],
        '/my-profile/edit-password' => [
            'App\Controller\UserController',
            'editPassword'
        ],
        '/user/forgot-password' => [
            'App\Controller\UserController',
            'forgotPassword'
        ],
        '/user/change-password' => [
            'App\Controller\UserController',
            'changePassword'
        ],
        '/user/reset-password' => [
            'App\Controller\UserController',
            'resetPasswordForm'
        ],
        '/user/update-password' => [
            'App\Controller\UserController',
            'updatePassword'
        ],
        '/dashboard' => [
            'App\Controller\UserController',
            'dashboard'
        ],
        '/my-projects' => [
            'App\Controller\UserController',
            'showAllUserProjects'
        ],
        '/my-project' => [
            'App\Controller\UserController',
            'showEditFormProject'
        ],
        '/project' => [
            'App\Controller\UserController',
            'showProject'
        ],
        '/project/update' => [
            'App\Controller\AdminController',
            'updateProject'
        ],
        '/project/delete' => [
            'App\Controller\UserController',
            'deleteProject'
        ],
        '/my-documents' => [
            'App\Controller\UserController',
            'showAllUserDocuments'
        ],
        '/logout' => [
            'App\Controller\UserController',
            'logout'
        ],
        '/admin' => [
            'App\Controller\AdminController',
            'loginAdmin'
        ],
        '/admin/login' => [
            'App\Controller\AdminController',
            'loginAdmin'
        ],
        '/admin-dashboard' => [
            'App\Controller\AdminController',
            'adminDashboard'
        ],
        '/admin-profile' => [
            'App\Controller\AdminController',
            'myAdminProfile'
        ],
        '/admin-create-user' => [
            'App\Controller\AdminController',
            'createUser'
        ],
        '/admin-create-project' => [
            'App\Controller\AdminController',
            'formCreateProject'
        ],
        '/admin-create-document' => [
            'App\Controller\AdminController',
            'formCreateDocument'
        ],
        '/document/update' => [
            'App\Controller\AdminController',
            'updateDocument'
        ],
        '/admin/store-document' => [
            'App\Controller\AdminController',
            'storeDocument'
        ],
        '/admin/store-user' => [
            'App\Controller\AdminController',
            'storeUser'
        ],
        '/delete' => [
            'App\Controller\AdminController',
            'deleteUser'
        ],
        '/admin/store-admin' => [
            'App\Controller\AdminController',
            'storeAdmin'
        ],
        '/admin/store-project' => [
            'App\Controller\AdminController',
            'storeProject'
        ],
        '/admin/getProjects' => [
            'App\Controller\AdminController',
            'getProjects'
        ],
        '/edit-admin' => [
            'App\Controller\AdminController',
            'formEditAdmin'
        ],
        '/admin/update-admin' => [
            'App\Controller\AdminController',
            'updateAdmin'
        ],
        '/delete-admin' => [
            'App\Controller\AdminController',
            'deleteAdmin'
        ],
        '/admin/user-dashboard' => [
            'App\Controller\AdminController',
            'userDashboard'
        ],
        '/admin/admin-dashboard' => [
            'App\Controller\AdminController',
            'Dashboard'
        ],
        '/admin/personal-dashboard' => [
            'App\Controller\AdminController',
            'personalDashboard'
        ],
	];
