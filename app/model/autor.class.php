<?php
namespace App\Model;
use App\Lib\Database;
use App\Lib\Resposta;
use PDO;
use Exception;

class Autor
{
    private $conn;       //connexió a la base de dades (PDO)
    private $resposta;   // resposta
    
    public function __CONSTRUCT()
    {
        $objectebd=Database::getInstance();
        $this->conn = $objectebd->getConnection();      
        $this->resposta = new Resposta();
    }
    
    public function getAll()
    {
		try
		{
			$result = array();                        
			$stm = $this->conn->prepare("SELECT id_aut,nom_aut,fk_nacionalitat FROM autors ORDER BY id_aut");
			$stm->execute();
            $tuples=$stm->fetchAll();
            $this->resposta->setDades($tuples);    // array de tuples
			$this->resposta->setCorrecta(true,$stm->rowCount());       // La resposta es correcta        
            return $this->resposta;
		}
        catch(Exception $e)
		{   // hi ha un error posam la resposta a fals i tornam missatge d'error
			$this->resposta->setCorrecta(false, $e->getMessage());
            return $this->resposta;
		}
    }
    
    public function get($id)
    {
        try
		{
			$result = array();                        
            $stm = $this->conn->prepare("SELECT id_aut,nom_aut,fk_nacionalitat FROM autors where id_aut=$id");
            //$stm->bindValue(':id_aut',$id);
            $stm->execute();
            $rc=$stm->rowCount();
            $tupla=$stm->fetch();
            $this->resposta->setDades($tupla);    // array de tuples
			$this->resposta->setCorrecta(true,$rc);       // La resposta es correcta        
            return $this->resposta;
		}
        catch(Exception $e)
		{   // hi ha un error posam la resposta a fals i tornam missatge d'error
			$this->resposta->setCorrecta(false, $e->getMessage());
            return $this->resposta;
		}
    }

    
    public function insert($dades)
    {
		try 
		{
                $sql = "SELECT max(id_aut) as N from autors";
                $stm=$this->conn->prepare($sql);
                $stm->execute();
                $row=$stm->fetch();
                $id_aut=$row["N"]+1;

                $nom_aut=$dades["nom_aut"];
                $fk_nacionalitat=$dades["fk_nacionalitat"];

                $sql = "INSERT INTO autors
                            (id_aut,nom_aut,fk_nacionalitat)
                            VALUES (:id_aut,:nom_aut,:fk_nacionalitat)";
                
                $stm=$this->conn->prepare($sql);
                $stm->bindValue(':id_aut',$id_aut);
                $stm->bindValue(':nom_aut',$nom_aut);
                $stm->bindValue(':fk_nacionalitat',!empty($fk_nacionalitat)?$fk_nacionalitat:NULL,PDO::PARAM_STR);
                $stm->execute();
            
       	        $this->resposta->setCorrecta(true,$stm->rowCount(),"insertat $id_aut");
                return $this->resposta;
        }
        catch (Exception $e) 
		{
                $this->resposta->setCorrecta(false, "Error insertant: ".$e->getMessage());
                return $this->resposta;
		}
    }   
    
    public function update($data) {
        try {
            $id_aut = $data['id'];
            $nom_aut = $data['nom_aut'];
            $fk_nacionalitat = $data['fk_nacionalitat'];

            $sql = "UPDATE AUTORS SET NOM_AUT=:nom_aut, FK_NACIONALITAT=:fk_nacionalitat WHERE ID_AUT=:id_aut";
            $stm = $this->conn->prepare($sql);
            $stm->bindValue(':id_aut', $id_aut,PDO::PARAM_INT);
            $stm->bindValue(':nom_aut', $nom_aut,PDO::PARAM_STR);
            $stm->bindValue(':fk_nacionalitat', !empty($fk_nacionalitat) ? $fk_nacionalitat : NULL, PDO::PARAM_STR);
            $stm->execute();
            $this->resposta->setCorrecta(true,$stm->rowCount(),"$id_aut-$nom_aut-$fk_nacionalitat");
            return $this->resposta;
        } catch (Exception $e) {
            $this->resposta->setCorrecta(false,0, "Error mofificant: " . $e->getMessage().$sql);
            return $this->resposta;
        }
    }

    public function delete($id) {
        try {
            $sql = "DELETE FROM `AUTORS` WHERE ID_AUT=:id_aut";
            
            $stm = $this->conn->prepare($sql);
            $stm->bindValue(':id_aut', $id);
            $stm->execute();
            $this->resposta->setCorrecta(true,$stm->rowCount());
            return $this->resposta;
        } catch (Exception $ex) {
            $this->resposta->setCorrecta(false, "Error eliminant: " . $e->getMessage());
            return $this->resposta;
        }
    }

    public function filtra($where, $orderby, $offset, $count) {
        try {
            $orderby = (!empty($orderby) ? $orderby : "id_aut");
            $offset = (!empty($offset) ? $offset : "0");
            $count = (!empty($count) ? $count : "20");

            $orderby = str_replace("-"," ",$orderby); //atribut-desc -> atribut desc
            
            $wheresql="";
            if ($where!='') {
                $whereset = explode('*',$where);
                foreach ($whereset as $w) {
                  if ($wheresql!='') { $wheresql.=' and ';}
                  $clauvalor=explode("-",$w);
                  $wheresql.=$clauvalor[0]." like '%".$clauvalor[1]."%'";
                }          
                $wheresql="WHERE ".$wheresql;
            } // clau-valor*clau-valor -> clau like '%valor%' and clau like '%valor%'
    

            $sql = "SELECT id_aut FROM AUTORS $wheresql";
            $stm = $this->conn->prepare($sql);
            $stm->execute();
            $numrows=$stm->rowCount();

            
            $sql = "SELECT id_aut,nom_aut,fk_nacionalitat FROM AUTORS $wheresql ORDER BY $orderby LIMIT $offset,$count";
            $stm = $this->conn->prepare($sql);
            $stm->execute();
            $tuples = $stm->fetchAll();
            $this->resposta->setDades($tuples);
            $this->resposta->setCorrecta(true,$numrows);      
            return $this->resposta;
        } catch (Exception $e) {
            $this->resposta->setCorrecta(false, "Error cercant: " . $e->getMessage()."--$sql");
            return $this->resposta;
        }
    }
    
          
}
