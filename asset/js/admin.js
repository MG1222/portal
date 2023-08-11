export const toggleForms = () => {
    // Select all the toggle buttons
    const buttons = document.querySelectorAll('.toggle-form');

    buttons.forEach(button => {
        button.addEventListener('click', function() {
            // Select the corresponding form row
            console.log(this.parentElement.parentElement.nextElementSibling);
            const formRow = this.parentElement.parentElement.nextElementSibling;
            formRow.classList.toggle('hidden');
        });
    });
};

export const changeFormAction = () => {
console.log('Hello Webpack Encore! Edit me in assets/js/main.js');
    // Select the form
    const form = document.querySelector('form');
    // Select the dropdown
    const dropdown = document.querySelector('#role_id');
    // Select the input
    const input = document.querySelector('#project_name');
    form.addEventListener('change', function() {
        console.log(form.action);

        if (dropdown.value === '2') {
            // Remplacez uniquement la dernière partie de l'URL après "/admin/"
            form.action = form.action.replace(/\/admin\/[^\/]+$/, '/admin/store-user');

            // Supprimez l'entrée du nom du projet
            input.remove();
        } else {
            // Remplacez uniquement la dernière partie de l'URL après "/admin/"
            form.action = form.action.replace(/\/admin\/[^\/]+$/, '/admin/store-admin');

            // Ajoutez l'entrée du nom du projet
            const projectContainer = document.querySelector('#project_container');
            projectContainer.innerHTML = '<input type="text" name="project_name" placeholder="* Project" required>';
        }
    });


}





export const roleOptions = () => {

    let select = document.querySelector('#role_id');
    if (select) {
        select.addEventListener('change', function() {
            // Get the selected user id
            const user_id = select.value;
            console.log(user_id);

            // Fetch the projects for the selected user
            fetch(`./admin/getProjects?user_id=${user_id}`)
                .then(response => response.json())
                .then(projects => {
                    console.log(projects);
                    // Select the project container
                    let projectContainer = document.querySelector('#project_container');
                    // Select the project dropdown
                    if (projects.length > 0) {
                        // Create options for the dropdown
                        let options = '<option value="" hidden="hidden" selected="selected"></option>'; // Selected option
                        projects.forEach(project => {
                            options += `<option value="${project.id}">${project.name}</option>`;
                        });
                        // Create a new dropdown element
                        let dropdown = document.createElement('select');
                        dropdown.setAttribute('name', 'project_id');
                        dropdown.setAttribute('id', 'project_id');
                        dropdown.setAttribute('required', true);
                        // Set the options for the dropdown
                        dropdown.innerHTML = options;
                        // Replace the old dropdown with the new one
                        projectContainer.innerHTML = '';
                        projectContainer.appendChild(dropdown);
                        console.log(dropdown)
                    } else {
                        // Create a new input element
                        let input = document.createElement('input');
                        input.setAttribute('type', 'text');
                        input.setAttribute('name', 'project_name');
                        input.setAttribute('placeholder', '* Project');
                        input.setAttribute('required', true);


                        // Add a hidden empty option for project_id
                        let hiddenOption = document.createElement('input');
                        hiddenOption.setAttribute('type', 'hidden');
                        hiddenOption.setAttribute('name', 'project_id');
                        hiddenOption.setAttribute('value', '');

                        // Replace the dropdown with the input and hidden option
                        projectContainer.innerHTML = '';
                        projectContainer.appendChild(input);
                        projectContainer.appendChild(hiddenOption);
                        console.log(input)
                    }

                })
        });
    }
};


const addOptionsToDropdown = (dropdown, options) => {
    dropdown.innerHTML = '';
    options.forEach(option => {
        const optionElement = document.createElement('option');
        optionElement.value = option.id;
        optionElement.text = option.name;
        dropdown.appendChild(optionElement);
    });
};



export const documentOptions = () => {
    const checkBoxUser = document.querySelector('#checkbox_user');
    const checkBoxAdmin = document.querySelector('#checkbox_admin');
    const userIdDropdown = document.querySelector('#user_id');
    const adminIdDropdown = document.querySelector('#admin_id');
    const projectIdDropdown = document.querySelector('#project_id');

    const handleCheckboxChange = () => {
        if (checkBoxUser.checked) {
            console.log('userIdDropdown')
            // Listen for changes in the user_id dropdown
            userIdDropdown.addEventListener('change', () => {
                const id = userIdDropdown.value;
                const endpointUrl = `./admin/getProjects?user_id=${id}`;

                // Fetch the projects for the selected user
                fetch(endpointUrl)
                    .then(response => response.json())
                    .then(projects => {
                        // Clear the current options from the project_id dropdown if it's the user dropdown
                        if (projectIdDropdown.parentNode === userIdDropdown.parentNode) {
                            projectIdDropdown.innerHTML = '';
                        }

                        if (projects.length > 0) {
                            addOptionsToDropdown(projectIdDropdown, projects);
                        } else {
                            projectIdDropdown.innerHTML = '<option value="" selected="selected" hidden="hidden">Aucun projet disponible</option>';

                        }
                    });
            });
        } else {
            console.log('adminIdDropdown');
            // Listen for changes in the admin_id dropdown
            adminIdDropdown.addEventListener('change', () => {
                const id = adminIdDropdown.value;
                const endpointUrl = `./admin/getProjects?admin_id=${id}`;
                console.log(endpointUrl);

                // Fetch the projects for the selected admin
                fetch(endpointUrl)
                    .then(response => response.json())
                    .then(projects => {
                        // Clear the current options from the project_id dropdown if it's the admin dropdown
                        if (projectIdDropdown.parentNode === adminIdDropdown.parentNode) {
                            projectIdDropdown.innerHTML = '';
                        }

                        if (projects.length > 0) {
                            addOptionsToDropdown(projectIdDropdown, projects);
                        } else {
                            projectIdDropdown.innerHTML = '<option value="" selected="selected" hidden="hidden">Aucun projet disponible</option>';

                        }
                    });
            });
        }
    };

    handleCheckboxChange(); // Call the function to check the initial state of the checkbox

    // Listen for changes in the checkbox
    checkBoxUser.addEventListener('change', handleCheckboxChange);
    checkBoxAdmin.addEventListener('change', handleCheckboxChange);
};


/**
 * dropdown menu admin
 */

export const dropDown = () => {

    const dropdownToggle = document.querySelector('.dropdown-toggle');

    dropdownToggle.addEventListener('click', (event) => {

        console.log('click')
        const dropdownMenu = document.querySelector('.dropdown-menu');
        console.log(dropdownMenu)
        dropdownMenu.classList.toggle('hidden');


    });



}

/**
 * Display the role input if the user selects the "New role" option
 */
export const roles = () => {
    // Select the role dropdown
    const form = document.querySelector('.form');
    const role_new_input = document.querySelector('#role_new_input');
    if (role_new_input) {
        let select = document.querySelector('#role_id');
        const role_new_input = document.querySelector('#role_new_input');
        const type_project = document.querySelector('#type_project');

        role_new_input.style.display = 'none';
        type_project.style.display = 'none';

        select.addEventListener('change', function () {
            // Get the selected role
            let role = select.value;

            if (role === 'role_new_option') {
                role_new_input.style.display = 'block';
                type_project.style.display = 'block';
            } else {
                role_new_input.style.display = 'none';
                type_project.style.display = 'none';
            }
        });
    }
}

/**
 * Confirm the deletion of a user/admin/project
 */
export const alertDelete = () => {

    const btnDelete = document.querySelectorAll('.btn-delete');
    const popupContainers = document.querySelectorAll('.popup-container');

// Fonction pour afficher le popup de confirmation de suppression
    btnDelete.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log(this);
            const userId = this.getAttribute('data-userid');
            const popupContainer = document.querySelector(`#popup-container-${userId}`);
            popupContainer.classList.remove('hidden');
            popupContainer.classList.add('show');
        });
    });

    popupContainers.forEach(function(popupContainer) {
        const btnCancel = popupContainer.querySelector('.btn-cancel');
        btnCancel.addEventListener('click', function(e) {
            popupContainer.classList.add('hidden');
            popupContainer.classList.remove('show');
        });
    });

}

/**
 * Confirm the deletion of a document
 */
export const alertDocDelete = () => {

    const btnDelete = document.querySelectorAll('.btn-delete');
    const popupContainers = document.querySelectorAll('.popup-container');

    // Fonction pour afficher le popup de confirmation de suppression
    btnDelete.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log(this);
            const documentId = this.getAttribute('data-documentid');
            const popupContainer = document.querySelector(`#popup-container-${documentId}`);
            popupContainer.classList.remove('hidden');
            popupContainer.classList.add('show');
        });
    });

    popupContainers.forEach(function(popupContainer) {
        const btnCancel = popupContainer.querySelector('.btn-cancel');
        btnCancel.addEventListener('click', function(e) {
            popupContainer.classList.add('hidden');
            popupContainer.classList.remove('show');
        });
    });

}



/**
 * Display select of user or admin for a new project
 */
export const checkboxes = () => {
    console.log('test')
    const checkboxAdmin = document.querySelector('#checkbox_admin')
    const checkboxUser = document.querySelector('#checkbox_user')


    checkboxAdmin.addEventListener('change', function () {

        checkboxUser.checked = false;
        const selectAdmin = document.querySelector('#admin_id');
        selectAdmin.classList.remove('hidden');
        const selectUser = document.querySelector('#user_id');
        selectUser.classList.add('hidden');
    });

    checkboxUser.addEventListener('click', function () {
        checkboxAdmin.checked = false;
        const selectUser = document.querySelector('#user_id');
        selectUser.classList.remove('hidden');
        const selectAdmin = document.querySelector('#admin_id');
        selectAdmin.classList.add('hidden');

    });


}
