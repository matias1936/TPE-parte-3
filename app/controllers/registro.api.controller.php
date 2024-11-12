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

    public function createRegistro($req, $res) {

        // Verificar que todos los campos están definidos y no están vacíos
        if (empty($req->body->nombre) || empty($req->body->action) || empty($req->body->fecha) || empty($req->body->hora) || empty($req->body->establecimiento_id)) {
            return $this->view->response('Faltan completar datos', 400);
        }
    
        // Asignar valores después de validarlos
        $nombre = trim($req->body->nombre);
        $action = strtoupper(trim($req->body->action));  // Convertir a mayúsculas
        $fecha = trim($req->body->fecha);
        $hora = trim($req->body->hora);
        $establecimiento_id = $req->body->establecimiento_id;
    
        // Verificar que los campos de nombre y acción no estén vacíos después del trim
        if (empty($nombre) || empty($action)) {
            return $this->view->response('El campo "nombre" o "action" no puede estar vacío', 400);
        }
    
        // Verificar que el campo action solo contenga "ENTRADA" o "SALIDA"
        if ($action !== 'ENTRADA' && $action !== 'SALIDA') {
            return $this->view->response('Indique si es ENTRADA o SALIDA', 400);
        }
    
        // Verificar que la fecha y la hora estén en un formato aceptable
        if (empty($fecha) || empty($hora)) {
            return $this->view->response('La "fecha" o "hora" no puede estar vacía', 400);
        }
    
        // Verificar que el establecimiento_id sea un número entero
        if (!is_numeric($establecimiento_id) || intval($establecimiento_id) <= 0) {
            return $this->view->response('El "establecimiento_id" debe ser un número válido', 400);
        }
    
        // Verificar que el establecimiento existe en la base de datos
        if (!$this->model->existeEstablecimiento($establecimiento_id)) {
            return $this->view->response('El "establecimiento_id" proporcionado no existe', 400);
        }
    
        // Intentar insertar el registro después de pasar todas las validaciones
        $id = $this->model->insertRegistro($nombre, $action, $fecha, $hora, $establecimiento_id);
    
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

