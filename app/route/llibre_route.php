<?php
use App\Model\Llibre;
use App\Model\LliAut;

$app->group('/llibre/', function () {
    
    $this->get('', function ($req, $res, $args) {
        $obj = new Llibre();   
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $obj->getAll()
            )
        );
    });
    
    $this->get('{id}', function ($req, $res, $args) {
        $obj = new Llibre();   
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $obj->get($args["id"])
            )
        );         
    });
    
    $this->post('', function ($req, $res, $args) {
        $atributs=$req->getParsedBody();
        $obj = new Llibre();   
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $obj->insert($atributs)
            )
        ); 
    });
    
    $this->put('', function ($req, $res, $args) {
        $atributs=$req->getParsedBody();
        $obj = new Llibre();   
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $obj->modificaLlibre($atributs)
            )
        ); 
    });
    
    $this->delete('', function ($req, $res, $args) {
        $atributs=$req->getParsedBody();
        $obj = new Llibre();   
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $obj->borrarLlibre($atributs)
            )
        ); 
    });

    // AUTORS d'un Llibre
    $this->get('{idllib}/autor/', function ($req, $res, $args) {
        $obj = new LliAut();   
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $obj->allAutorLlibre($args["idllib"])
            )
        );
    });

    $this->post('{idllib}/autor/{idaut}', function ($req, $res, $args) {
        $atributs=$req->getParsedBody();
        $atributs["id_llib"]=$args["idllib"];
        $atributs["id_aut"]=$args["idaut"];
        $obj = new LliAut();   
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $obj->insert($atributs)
            )
        ); 
    });

    $this->delete('{idllib}/autor/{idaut}', function ($req, $res, $args) {
        $atributs=$req->getParsedBody();
        $obj = new LliAut();   
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $obj->delete($args["id_llib"],$args["idaut"])
            )
        ); 
    });

});