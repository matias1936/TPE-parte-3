//Obtener un registros
    Metodo:GET       URL:/api/registros


//Obtener un registro por ID
    Método: GET      URL: /api/registros/:id
    Ejemplo:GET /api/registros/5


//Crear un nuevo registro
    Método: POST     URL: /api/registros
    Ejemplo:{
            "nombre":"Oroquieta Merlino, Luciano",
            "action":"ENTRADA",
            "fecha":"17/11/2024",
            "hora":"13:00",
            "establecimiento_id":"18"
            }


//Actualizar un registro existente
    Método: PUT       URL: /api/registros/:id
    Ejemplo:{
            "nombre":"Oroquieta Merlino, Luciano",
            "action":"SALIDA",
            "fecha":"17/11/2024",
            "hora":"20:00",
            "establecimiento_id":"18"
            }


//Eliminar un registro
    Método: DELETE     URL: /api/registros/:id
    Ejemplo: /api/registros/5


//Obtener todos los registros con ordenación
    Método: GET        URL:URL: /api/registros?sortField={campo}&sortOrder={asc|desc}
    Parámetros: 
        sortField: Campo para ordenar (ejemplo: fecha, hora).
        sortOrder: Dirección del orden (ASC o DESC, por defecto: ASC).
    Ejemplo: 
        /api/registros?sortField=fecha&sortOrder=DESC


//Obtener registros con filtrado
    Método: GET        URL: /api/registros?filterField={campo}&filterValue={valor}
    Parámetros:
        filterField: Campo por el cual filtrar (ejemplo: action, nombre).
        filterValue: Valor parcial o total para filtrar.
    Ejemplo:
        /api/registros?filterField=action&filterValue=ENTRADA

//Obtener registros con ordenación y filtrado combinados
    Método: GET
    URL: /api/registros?sortField={campo}&sortOrder={asc|desc}&filterField={campo}&filterValue={valor}
    Ejemplo:
         /api/registros?sortField=nombre&sortOrder=ASC&filterField=action&filterValue=SALIDA

