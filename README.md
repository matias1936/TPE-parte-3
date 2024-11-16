1. Listar con ordenación por cualquier campo:

Método: GET
URL: /api/registros?sortField={campo}&sortOrder={asc|desc}
Parámetros:
sortField: Campo por el que se ordenará (ej. fecha, hora).
sortOrder: ASC o DESC (por defecto: ASC).
Ejemplo de Request en Postman: GET /api/registros?sortField=fecha&sortOrder=DESC


2. Listar con filtrado por un campo:
Método: GET

URL: /api/registros?filterField={campo}&filterValue={valor}
Parámetros:
filterField: Campo por el que se filtrará (ej. action, nombre).
filterValue: Valor parcial o total del campo.
Ejemplo de Request en Postman: GET /api/registros?filterField=action&filterValue=ENTRADA


3. Listar con ordenación y filtrado combinados:
Método: GET

URL: /api/registros?sortField={campo}&sortOrder={asc|desc}&filterField={campo}&filterValue={valor}
Ejemplo de Request en Postman: GET /api/registros?sortField=nombre&sortOrder=ASC&filterField=action&filterValue=SALIDA

