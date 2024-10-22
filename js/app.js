"use strict"

const BASE_URL = "api/"; // url relativa a donde estoy parado (http://localhost/web2/2024/todo-list-rest/api)

// arreglo de tareas
let tasks = [];

// event listener para insertar tarea
let form = document.querySelector("#task-form");
form.addEventListener('submit', insertTask);


async function getAll() {
    try {
        const response = await fetch(BASE_URL + "tareas");
        if (!response.ok) {
            throw new Error('Error al llamar las tareas');
        }

        tasks = await response.json();
        showTasks();
    } catch(error) {
        console.log(error)
    }
}

async function insertTask(e) {
    e.preventDefault();

    let data = new FormData(form);
    let task = {
        titulo: data.get('titulo'),
        descripcion: data.get('descripcion'),
        prioridad: data.get('prioridad'),
    };

    try {
        let response = await fetch(BASE_URL + "tareas", {
            method: "POST",
            headers: { 'Content-Type': 'application/json'},
            body: JSON.stringify(task)
        });
        if (!response.ok) {
            throw new Error('Error del servidor');
        }

        let nTask = await response.json();

        // inserto la tarea nueva
        tasks.push(nTask);
        showTasks();

        form.reset();
    } catch(e) {
        console.log(e);
    }
}

async function deleteTask(e) {
    e.preventDefault();

    try {
        let id = e.target.dataset.task;
        let response = await fetch(BASE_URL + 'tareas/' + id, {method: 'DELETE'});
        if (!response.ok) {
            throw new Error('Recurso no existe');
        }

        // eliminar la tarea del arreglo global
        tasks = tasks.filter(task => task.id != id);
        showTasks();
    } catch(e) {
        console.log(e);
    }
}

async function finalizeTask(e) {
    e.preventDefault();

    try {
        let id = e.target.dataset.task;
        let response = await fetch(BASE_URL + "tareas" + id, {
            method: "PUT",
            headers: { 'Content-Type': 'application/json'},
            body: { finalizada: 1 }
        });

        if (!response.ok) {
            throw new Error('Recurso no existe');
        }

        // busco la tarea y la modifico
        const oldTask = tasks.find(task => task.id === id);
        oldTask.finalizada = 1;

        showTasks();
    } catch(e) {
        console.log(e);
    }
}

/**
 * Renderiza la lista de tareas
 */
function showTasks() {
    let ul = document.querySelector("#task-list");
    ul.innerHTML = "";
    for (const task of tasks) {
        let html = `
            <li class='
                    list-group-item d-flex justify-content-between align-items-center
                    ${ task.finalizada == 1 ? 'finalizada' : ''}
                '>
                <span> <b>${task.titulo}</b> - ${task.descripcion} (prioridad ${task.prioridad}) </span>
                <div class="ml-auto">
                    ${task.finalizada != 1 ? `<a href='#' data-task="${task.id}" type='button' class='btn btn-small btn-success btn-finalize'>Finalizar</a>` : ''}
                    <a href='#' data-task="${task.id}" type='button' class='btn btn-small btn-danger btn-delete'>Borrar</a>
                </div>
            </li>
        `;

        ul.innerHTML += html;
    }

    // actualizo el total
    let count = document.querySelector("#count");
    count.innerHTML = tasks.length;

    // asigno event listener para los botones
    const btnsDelete = document.querySelectorAll('a.btn-delete');
    for (const btnDelete of btnsDelete) {
        btnDelete.addEventListener('click', deleteTask);
    }

    // asigno event listener para los botones
    const btnsFinalizar = document.querySelectorAll('a.btn-finalize');
    for (const btnFinalizar of btnsFinalizar) {
        btnFinalizar.addEventListener('click', finalizeTask);
    }
}


getAll();