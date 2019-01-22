<?php
namespace App\Model;
use App\Lib\Database;
use App\Lib\Resposta;
use PDO;
use Exception;

class Nacionalitat
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
			$stm = $this->conn->prepare("SELECT nacionalitat FROM nacionalitats ORDER BY nacionalitat");
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
    
    public function insert($dades)
    {
		try 
		{
                $nacionalitat=$dades["nacionalitat"];

                $sql = "INSERT INTO nacionalitats
                            (nacionalitat)
                            VALUES (:nacionalitat)";
                
                $stm=$this->conn->prepare($sql);
                $stm->bindValue(':nacionalitat',$nacionalitat);
                $stm->execute();
            
       	        $this->resposta->setCorrecta(true,$stm->rowCount());
                return $this->resposta;
        }
        catch (Exception $e) 
		{
                $this->resposta->setCorrecta(false, "Error insertant: ".$e->getMessage());
                return $this->resposta;
		}
    }   
    
    
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM nacionalitats WHERE nacionalitat=:nacionalitat";
            
            $stm = $this->conn->prepare($sql);
            $stm->bindValue(':nacionalitat', $id);
            $stm->execute();
            $this->resposta->setCorrecta(true,$stm->rowCount());
            return $this->resposta;
        } catch (Exception $ex) {
            $this->resposta->setCorrecta(false, "Error eliminant: " . $e->getMessage());
            return $this->resposta;
        }
    }
          
}
