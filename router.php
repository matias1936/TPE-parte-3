<?php
    
    require_once 'libs/router.php';

    require_once 'app/controllers/registro.api.controller.php';

    $router = new Router();

    #                      endpoint                            verbo                  controller                          metodo
    $router->addRoute('registros'      ,            'GET',     'RegistrosApiController',   'getAll');
    $router->addRoute('registros/:id'  ,            'GET',     'RegistrosApiController',   'get'   );
    $router->addRoute('registros/:id'  ,            'DELETE',  'RegistrosApiController',   'delete');
    $router->addRoute('registros'      ,            'POST',    'RegistrosApiController',   'createRegistro');
    $router->addRoute('registros/:id'  ,            'PUT',     'RegistrosApiController',   'updateRegistro');


    $router->route($_GET['resource'], $_SERVER['REQUEST_METHOD']);