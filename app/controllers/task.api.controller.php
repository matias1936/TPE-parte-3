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

}

