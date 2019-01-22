<?php
use App\Model\Nacionalitat;

$app->group('/nacionalitat/', function () {

    $this->get('', function ($req, $res, $args) {
        $obj = new Nacionalitat();   
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $obj->getAll()
            )
        );
    });
               
    $this->post('', function ($req, $res, $args) {
            $atributs=$req->getParsedBody();  //llista atributs del client
            $obj = new Nacionalitat();
            return $res
               ->withHeader('Content-type', 'application/json')
               ->getBody()
               ->write(
                json_encode(
                    $obj->insert($atributs)
                )
            );             
    });

    
    $this->delete('{id}', function ($req, $res, $args) {
        $obj = new Nacionalitat();   
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $obj->delete($args["id"])
            )
        ); 
    });
        
});
