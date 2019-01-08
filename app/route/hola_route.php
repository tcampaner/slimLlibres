<?php
$app->group('/hola/', function () {   

    $this->get('catala', function ($req, $res, $args) {
        return $res->getBody()
                   ->write('Hola mon!!');
    });   

    $this->get('angles', function ($req, $res, $args) {
        return $res->getBody()
                   ->write('Hello world!!');
    });

    $this->get('{nom}', function ($req, $res, $args) {
        $nom=$args["nom"];
        return $res->getBody()
                   ->write("Hola $nom!!");
    });

});