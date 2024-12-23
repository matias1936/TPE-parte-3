<?php

class RegistroModel {
    private $db;

    public function __construct() {
       $this->db = new PDO('mysql:host=localhost;dbname=db_registros;charset=utf8', 'root', '');
    }
 
    public function getRegistros($sortField = null, $sortOrder = 'ASC', $filterField = null, $filterValue = null) {
        $query = "SELECT * FROM registros";
    
        $params = [];
        if ($filterField && $filterValue) {
            $query .= " WHERE $filterField LIKE ?";
            $params[] = "%$filterValue%";
        }
    
        if ($sortField) {
            $query .= " ORDER BY $sortField " . (strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC');
        }
    
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    
 
    public function getRegistro($id) {    
        $query = $this->db->prepare('SELECT * FROM registros WHERE id = ?');
        $query->execute([$id]);   
        $registro = $query->fetch(PDO::FETCH_OBJ);
        return $registro;
    }

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

        $query = $this->db->prepare("SELECT COUNT(*) FROM establecimientos WHERE id = ?");
        $query->execute([$establecimiento_id]);

        $count = $query->fetchColumn();
        
        return $count > 0;
    }
    
 
    public function eraseRegistro($id) {
        $query = $this->db->prepare('DELETE FROM registros WHERE id = ?');
        $query->execute([$id]);
    }



    function updateRegistro($id, $nombre, $action, $fecha, $hora, $establecimiento_id) {    
        $query = $this->db->prepare('UPDATE registros SET nombre= ?, action = ?, fecha = ?, hora = ?, establecimiento_id = ? WHERE id = ?');
        $query->execute([$nombre, $action, $fecha,$hora, $establecimiento_id, $id]);
    } 
    
  
    
}