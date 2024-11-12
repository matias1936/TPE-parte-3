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

    public function createRegistro($req, $res) {

        if (empty($req->body->nombre) || empty($req->body->action) || empty($req->body->fecha) || empty($req->body->hora) || empty($req->body->establecimiento_id)) {
            return $this->view->response('Faltan completar datos', 400);
        }

        $nombre = trim($req->body->nombre);
        $action = strtoupper(trim($req->body->action));  // Convertir a mayúsculas
        $fecha = trim($req->body->fecha);
        $hora = trim($req->body->hora);
        $establecimiento_id = $req->body->establecimiento_id;

        if (empty($nombre) || empty($action)) {
            return $this->view->response('El campo "nombre" o "action" no puede estar vacío', 400);
        }
    
        if ($action !== 'ENTRADA' && $action !== 'SALIDA') {
            return $this->view->response('Indique si es ENTRADA o SALIDA', 400);
        }
    
        if (empty($fecha) || empty($hora)) {
            return $this->view->response('La "fecha" o "hora" no puede estar vacía', 400);
        }
    
        if (!is_numeric($establecimiento_id) || intval($establecimiento_id) <= 0) {
            return $this->view->response('El "establecimiento_id" debe ser un número válido', 400);
        }

        if (!$this->model->existeEstablecimiento($establecimiento_id)) {
            return $this->view->response('El "establecimiento_id" proporcionado no existe', 400);
        }

        $id = $this->model->insertRegistro($nombre, $action, $fecha, $hora, $establecimiento_id);
    
        if (!$id) {
            return $this->view->response("Error al insertar registro", 500);
        }
    
        $registro = $this->model->getRegistro($id);
        return $this->view->response($registro, 201);
    }
    
    
    


    public function updateRegistro($req, $res) {
        $id = $req->params->id;

        $registro = $this->model->getRegistro($id);
        if (!$registro) {
            return $this->view->response("El registro con el id=$id no existe", 404);
        }

        $body = json_decode(file_get_contents("php://input"), true);
        if (empty($body['nombre']) || empty($body['action']) || empty($body['fecha']) || empty($body['hora']) || empty($body['establecimiento_id'])) {
            return $this->view->response('Faltan completar datos', 400);
        }

        $nombre = trim($body['nombre']);
        $action = strtoupper(trim($body['action']));
        $fecha = trim($body['fecha']);
        $hora = trim($body['hora']);
        $establecimiento_id = $body['establecimiento_id'];

        if (empty($nombre) || empty($action)) {
            return $this->view->response('El campo "nombre" o "action" no puede estar vacio', 400);
        }

        if ($action !== 'ENTRADA' && $action !== 'SALIDA') {
            return $this->view->response('Indique si es ENTRADA o SALIDA', 400);
        }
    
        if (empty($fecha) || empty($hora)) {
            return $this->view->response('La "fecha" o "hora" no puede estar vacia', 400);
        }

        if (!is_numeric($establecimiento_id) || intval($establecimiento_id) <= 0) {
            return $this->view->response('El "establecimiento_id" debe ser un número valido', 400);
        }

        if (!$this->model->existeEstablecimiento($establecimiento_id)) {
            return $this->view->response("El establecimiento con id=$establecimiento_id no existe", 400);
        }

        $this->model->updateRegistro($id, $nombre, $action, $fecha, $hora, $establecimiento_id);

        $registro = $this->model->getRegistro($id);
        return $this->view->response($registro, 200);
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

