"use strict"

const BASE_URL = "api/"; // url relativa a donde estoy parado (http://localhost/web2/2024/todo-list-rest/api)

let tasks = [];

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

        tasks = tasks.filter(task => task.id != id);
        showTasks();
    } catch(e) {
        console.log(e);
    }
}


function showTasks() {
    let ul = document.querySelector("#task-list");
    ul.innerHTML = "";
    for (const task of tasks) {
        let html = `
            <li class='
                    list-group-item d-flex justify-content-between align-items-center'>
                <span> <b>${task.titulo}</b> - ${task.descripcion} (prioridad ${task.prioridad}) </span>
                <div class="ml-auto">
                    <a href='#' data-task="${task.id}" type='button' class='btn btn-small btn-danger btn-delete'>Borrar</a>
                </div>
            </li>
        `;

        ul.innerHTML += html;
    }

    let count = document.querySelector("#count");
    count.innerHTML = tasks.length;

    const btnsDelete = document.querySelectorAll('a.btn-delete');
    for (const btnDelete of btnsDelete) {
        btnDelete.addEventListener('click', deleteTask);
    }

}


getAll();