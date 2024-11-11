<?php
require_once './app/models/registro.model.php';
require_once './app/views/json.view.php';

class RegistrosApiController {
    private $model;
    private $view;

    public function __construct() {
        $this->model = new RegistroModel();
        $this->view = new JSONView();
    }
    hola

    
    public function getAll($req, $res) {
        // Verificar si se ha pasado un campo para ordenar y un orden específico
        $sortField = !empty($req->query->sortField) ? $req->query->sortField : null;
        $sortOrder = !empty($req->query->sortOrder) ? $req->query->sortOrder : null;
    
        // Validación del campo de ordenación para evitar inyección SQL
        $validFields = ['id', 'nombre', 'action', 'fecha', 'hora', 'id_establecimiento'];
        if ($sortField && !in_array($sortField, $validFields)) {
            return $this->view->response(["error" => "Campo de ordenación inválido"], 400);
        }
    
        // Llamar al modelo y obtener los registros, con ordenación si se especificó
        $registros = $this->model->getRegistros($sortField, $sortOrder);
    
        return $this->view->response($registros);
    }
    
    


    public function get($req, $res) {

        $id = $req->params->id;

        $registro = $this->model->getRegistro($id);

        if(!$registro) {
            return $this->view->response("el registro con el id=$id no existe", 404);
        }


        return $this->view->response($registro);
    }

    public function delete($req, $res) {
        $id = $req->params->id;

        $registro = $this->model->getRegistro($id);

        if (!$registro) {
            return $this->view->response("el registro con el id=$id no existe", 404);
        }

        $this->model->eraseRegistro($id);
        $this->view->response("el registro con el id=$id se eliminó con éxito");
    }

    public function create($req, $res) {

        if (empty($req->body->titulo) || empty($req->body->prioridad)) {
            return $this->view->response('Faltan completar datos', 400);
        }

        $titulo = $req->body->titulo;       
        $descripcion = $req->body->descripcion;       
        $prioridad = $req->body->prioridad;       

        $id = $this->model->insertRegistro($titulo, $descripcion, $prioridad);

        if (!$id) {
            return $this->view->response("Error al insertar registro", 500);
        }

        $registro = $this->model->getRegistro($id);
        return $this->view->response($registro, 201);
    }

    public function update($req, $res) {
        $id = $req->params->id;

        $registro = $this->model->getRegistro($id);
        if (!$registro) {
            return $this->view->response("el registro con el id=$id no existe", 404);
        }

         if (empty($req->body->titulo) || empty($req->body->prioridad) || empty($req->body->finalizada)) {
            return $this->view->response('Faltan completar datos', 400);
        }

        $titulo = $req->body->titulo;       
        $descripcion = $req->body->descripcion;       
        $prioridad = $req->body->prioridad;
        $finalizada = $req->body->finalizada;

        $this->model->updateRegistro($id, $titulo, $descripcion, $prioridad, $finalizada);

        $registro = $this->model->getRegistro($id);
        $this->view->response($registro, 200);
    }

    
    /**
     * Método para actualizar el subrecurso "finalizada" de tareas.
     * 
     * api/tareas/:id/finalizada (respeta RESTFul)
     * 
     * NOTA: se podria (y es mejor) usar un PATCH a api/tareas/:id
     * ya que es similar al PUT pero solo modifica lo que envias en
     * el body, el resto de los campos los deja igual.
     * (más dificil de implementar) 
     * 
     */
    public function setFinalize($req, $res) {
        $id = $req->params->id;


        $registro = $this->model->getRegistro($id);
        if (!$registro) {
            return $this->view->response("el registro con el id=$id no existe", 404);
        }


        if (!isset($req->body->finalizada)) {
            return $this->view->response('Faltan completar datos', 400);
        }


        if ($req->body->finalizada !== 1 && $req->body->finalizada !== 0) {
            return $this->view->response('Tipo de dato incorrecto', 400);
        }


         $registro = $this->model->getRegistro($id);
         $this->view->response($registro, 200);
    }

}

