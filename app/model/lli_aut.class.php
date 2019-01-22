<?php
namespace App\Model;
use App\Lib\Database;
use App\Lib\Resposta;
use PDO;
use Exception;

class LliAut
{
    private $conn; //connexió a la base de dades (PDO)
    private $resposta; // resposta
    public function __CONSTRUCT()
    {
        $this->conn = Database::getInstance()->getConnection();
        $this->resposta = new Resposta();
    }
    public function insert($data)
    {
        try {
            $id_llibre = $data["id_llib"];
            $id_autor = $data["id_aut"];
            $sql = "INSERT INTO `LLI_AUT`(`FK_IDLLIB`, `FK_IDAUT`, `FK_ROLAUT`) VALUES (:fk_llib,:fkaut,:rol)";
            $stm = $this->conn->prepare($sql);
            $stm->bindValue(':fk_llib', $id_llibre, PDO::PARAM_INT);
            $stm->bindValue(':fkaut', $id_autor, PDO::PARAM_INT);
            $stm->bindValue(':rol', !empty($data["rol"]) ? $data["rol"] : NULL, PDO::PARAM_STR);
            $stm->execute();
            $this->resposta->setCorrecta(true);
            return $this->resposta;
        } catch (Exception $e) {
            $this->resposta->setCorrecta(false, "Error insertant: " . $e->getMessage());
            return $this->resposta;
        }
    }
    public function delete($idllib,$idaut)
    {
        try {
            $sql = "DELETE FROM `LLI_AUT` WHERE `FK_IDLLIB`= :fk_llib AND `FK_IDAUT`= :fk_aut";
            $stm = $this->conn->prepare($sql);
            $stm->bindValue(':fk_llib', $idllib);
            $stm->bindValue(':fk_aut', $idaut);
            $stm->execute();
            $this->resposta->setCorrecta(true);
            return $this->resposta;
        } catch (Exception $ex) {
            $this->resposta->setCorrecta(false, "Error eliminant: " . $ex->getMessage());
            return $this->resposta;
        }
    }
    public function allAutorLlibre($id)
    {
        try {
            $id_llib = $id;
            //select * from lli_aut inner join llibres on fk_idllib = id_llib inner join autors on fk_idaut = id_aut where id_llib = :id_llib;
            //$sql = "SELECT * FROM AUTORS,LLIBRES,LLI_AUT WHERE ID_AUT = FK_IDAUT AND ID_LLIB = FK_IDLLIB and ID_LLIB = :id_llib";
            $sql = "SELECT * FROM LLI_AUT INNER JOIN LLIBRES ON FK_IDLLIB = ID_LLIB INNER JOIN AUTORS ON FK_IDAUT = ID_AUT WHERE ID_LLIB = :id_llib";
            $stm = $this->conn->prepare($sql);
            $stm->bindValue(":id_llib", $id_llib);
            $stm->execute();
            $row = $stm->fetchAll();
            $this->resposta->SetDades($row);
            $this->resposta->SetCorrecta(true);
            return $this->resposta;
        } catch (Exception $e) {
            $this->resposta->setCorrecta(false, $e->getMessage());
            return $this->resposta;
        }
    }
}