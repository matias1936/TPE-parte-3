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

    
    public function getAll($req, $res) {
        $sortField = !empty($req->query->sortField) ? $req->query->sortField : null;
        $sortOrder = !empty($req->query->sortOrder) ? $req->query->sortOrder : null;
        $validFields = ['id', 'nombre', 'action', 'fecha', 'hora', 'id_establecimiento'];
        if ($sortField && !in_array($sortField, $validFields)) {
            return $this->view->response("Campo de ordenación inválido", 400);
        }
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
            return $this->view->response("El registro con el id=$id no existe", 404);
        }
        if (empty($req->body->nombre) || empty($req->body->action) || empty($req->body->fecha) || empty($req->body->hora) || empty($req->body->establecimiento_id)) {
            return $this->view->response('Faltan completar datos', 400);
        }
        $nombre = $req->body->nombre;       
        $action = $req->body->action;       
        $fecha = $req->body->fecha;
        $hora = $req->body->hora;
        $establecimiento_id = $req->body->establecimiento_id;
    
        if (!$this->model->existsEstablecimiento($establecimiento_id)) {
            return $this->view->response("El establecimiento con id=$establecimiento_id no existe", 400);
        }
        $this->model->updateRegistro($id, $nombre, $action, $fecha, $establecimiento_id);
        $registro = $this->model->getRegistro($id);
        $this->view->response($registro, 200);
    }
    

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

