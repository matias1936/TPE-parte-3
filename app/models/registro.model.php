<?php

class RegistroModel {
    private $db;

    public function __construct() {
       $this->db = new PDO('mysql:host=localhost;dbname=db_registros;charset=utf8', 'root', '');
    }
 
    public function getRegistros($sortField = null, $sortOrder = null) {
        $sql = 'SELECT * FROM registros';
    
        // Añadir la ordenación solo si se especifican ambos parámetros
        if ($sortField && $sortOrder) {
            $sql .= ' ORDER BY ' . $sortField;
            $sql .= ($sortOrder === 'desc') ? ' DESC' : ' ASC';
        }
    
        // Ejecutar la consulta
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    
    
    
 
    public function getRegistro($id) {    
        $query = $this->db->prepare('SELECT * FROM registros WHERE id = ?');
        $query->execute([$id]);   
        $registro = $query->fetch(PDO::FETCH_OBJ);
        return $registro;
    }
    /* 
        $nombre = $req->body->nombre;       
        $action = $req->body->action;       
        $fecha = $req->body->fecha;
        $hora = $req->body->hora;
        $establecimiento_id = $req->body->establecimiento_id;       
    */  
    public function insertRegistro($nombre, $action, $fecha, $hora, $establecimiento_id) {
        try {
            $query = $this->db->prepare('INSERT INTO registros(nombre, action, fecha, hora, establecimiento_id) VALUES (?, ?, ?, ?, ?)');
            $query->execute([$nombre, $action, $fecha, $hora, $establecimiento_id]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error de inserción en registros: " . $e->getMessage());
            return null;
        }
    }
    
    public function existeEstablecimiento($establecimiento_id) {
        // Preparar la consulta SQL para verificar si el establecimiento existe
        $query = $this->db->prepare("SELECT COUNT(*) FROM establecimientos WHERE id = ?");
        $query->execute([$establecimiento_id]);
    
        // Obtener el resultado y verificar si hay al menos un registro
        $count = $query->fetchColumn();
        
        return $count > 0;
    }
    
 
    public function eraseRegistro($id) {
        $query = $this->db->prepare('DELETE FROM registros WHERE id = ?');
        $query->execute([$id]);
    }



    function updateRegistro($id, $nombre, $action, $fecha, $establecimiento_id) {    
        $query = $this->db->prepare('UPDATE registros SET nombre= ?, action = ?, fecha = ?, establecimiento_id = ? WHERE id = ?');
        $query->execute([$nombre, $action, $fecha, $establecimiento_id, $id]);
    }
    
    public function existsEstablecimiento($establecimiento_id) {
        $query = $this->db->prepare('SELECT id FROM establecimientos WHERE id = ?');
        $query->execute([$establecimiento_id]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result !== false; // Retorna true si encontró un registro, false si no
    }
    
}