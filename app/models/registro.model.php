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
    
        // Retornar los datos como un arreglo de objetos
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    
    
    
 
    public function getRegistro($id) {    
        $query = $this->db->prepare('SELECT * FROM registros WHERE id = ?');
        $query->execute([$id]);   
    
        $registro = $query->fetch(PDO::FETCH_OBJ);
    
        return $registro;
    }
 
    public function insertRegistro($title, $description, $priority, $finished = false) { 
        $query = $this->db->prepare('INSERT INTO registros(titulo, descripcion, prioridad, finalizada) VALUES (?, ?, ?, ?)');
        $query->execute([$title, $description, $priority, $finished]);
    
        $id = $this->db->lastInsertId();
    
        return $id;
    }
 
    public function eraseRegistro($id) {
        $query = $this->db->prepare('DELETE FROM registros WHERE id = ?');
        $query->execute([$id]);
    }



    function updateRegistro($id, $titulo, $descripcion, $prioridad, $finalizada) {    
        $query = $this->db->prepare('UPDATE registros SET titulo = ?, descripcion = ?, prioridad = ?, finalizada = ? WHERE id = ?');
        $query->execute([$titulo, $descripcion, $prioridad, $finalizada, $id]);
    }
}