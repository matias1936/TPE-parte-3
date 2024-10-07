<?php

class TaskModel {
    private $db;

    public function __construct() {
       $this->db = new PDO('mysql:host=localhost;dbname=db_tareas;charset=utf8', 'root', '');
    }
 
    public function getTasks($filtrarFinalizadas = null, $orderBy = false) {
        $sql = 'SELECT * FROM tareas';

        if($filtrarFinalizadas != null) {
            if($filtrarFinalizadas == 'true')
                $sql .= ' WHERE finalizada = 1';
            else
                $sql .= ' WHERE finalizada = 0';
        }

        if($orderBy) {
            switch($orderBy) {
                case 'titulo':
                    $sql .= ' ORDER BY titulo';
                    break;
                case 'prioridad':
                    $sql .= ' ORDER BY prioridad';
                    break;
            }
        }

        // 2. Ejecuto la consulta
        $query = $this->db->prepare($sql);
        $query->execute();
    
        // 3. Obtengo los datos en un arreglo de objetos
        $tasks = $query->fetchAll(PDO::FETCH_OBJ); 
    
        return $tasks;
    }
 
    public function getTask($id) {    
        $query = $this->db->prepare('SELECT * FROM tareas WHERE id = ?');
        $query->execute([$id]);   
    
        $task = $query->fetch(PDO::FETCH_OBJ);
    
        return $task;
    }
 
    public function insertTask($title, $description, $priority, $finished = false) { 
        $query = $this->db->prepare('INSERT INTO tareas(titulo, descripcion, prioridad, finalizada) VALUES (?, ?, ?, ?)');
        $query->execute([$title, $description, $priority, $finished]);
    
        $id = $this->db->lastInsertId();
    
        return $id;
    }
 
    public function eraseTask($id) {
        $query = $this->db->prepare('DELETE FROM tareas WHERE id = ?');
        $query->execute([$id]);
    }

    public function updateTask($id) {        
        $query = $this->db->prepare('UPDATE tareas SET finalizada = 1 WHERE id = ?');
        $query->execute([$id]);
    }
}