<?php
require_once './app/models/task.model.php';
require_once './app/views/json.view.php';

class TaskApiController {
    private $model;
    private $view;

    public function __construct() {
        $this->model = new TaskModel();
        $this->view = new JSONView();
    }

    // /api/tareas
    public function getAll($req, $res) {
        $filtrarFinalizadas = null;
        // obtengo las tareas de la DB
        if(isset($req->query->finalizadas)) {
            $filtrarFinalizadas = $req->query->finalizadas;
        }
        
        $orderBy = false;
        if(isset($req->query->orderBy))
            $orderBy = $req->query->orderBy;

        $tasks = $this->model->getTasks($filtrarFinalizadas, $orderBy);
        
        // mando las tareas a la vista
        return $this->view->response($tasks);
    }

    // /api/tareas/:id
    public function get($req, $res) {
        // obtengo el id de la tarea desde la ruta
        $id = $req->params->id;

        // obtengo la tarea de la DB
        $task = $this->model->getTask($id);

        if(!$task) {
            return $this->view->response("La tarea con el id=$id no existe", 404);
        }

        // mando la tarea a la vista
        return $this->view->response($task);
    }

    // api/tareas/:id (DELETE)
    public function delete($req, $res) {
        $id = $req->params->id;

        $task = $this->model->getTask($id);

        if (!$task) {
            return $this->view->response("La tarea con el id=$id no existe", 404);
        }

        $this->model->eraseTask($id);
        $this->view->response("La tarea con el id=$id se eliminó con éxito");
    }

    // api/tareas (POST)
    public function create($req, $res) {

        // valido los datos
        if (empty($req->body->titulo) || empty($req->body->prioridad)) {
            return $this->view->response('Faltan completar datos', 400);
        }

        // obtengo los datos
        $titulo = $req->body->titulo;       
        $descripcion = $req->body->descripcion;       
        $prioridad = $req->body->prioridad;       

        // inserto los datos
        $id = $this->model->insertTask($titulo, $descripcion, $prioridad);

        if (!$id) {
            return $this->view->response("Error al insertar tarea", 500);
        }

        // buena práctica es devolver el recurso insertado
        $task = $this->model->getTask($id);
        return $this->view->response($task, 201);
    }

    // api/tareas/:id (PUT)
    public function update($req, $res) {
        $id = $req->params->id;

        // verifico que exista
        $task = $this->model->getTask($id);
        if (!$task) {
            return $this->view->response("La tarea con el id=$id no existe", 404);
        }

         // valido los datos
         if (empty($req->body->titulo) || empty($req->body->prioridad) || empty($req->body->finalizada)) {
            return $this->view->response('Faltan completar datos', 400);
        }

        // obtengo los datos
        $titulo = $req->body->titulo;       
        $descripcion = $req->body->descripcion;       
        $prioridad = $req->body->prioridad;
        $finalizada = $req->body->finalizada;

        // actualiza la tarea
        $this->model->updateTask($id, $titulo, $descripcion, $prioridad, $finalizada);

        // obtengo la tarea modificada y la devuelvo en la respuesta
        $task = $this->model->getTask($id);
        $this->view->response($task, 200);
    }

}

