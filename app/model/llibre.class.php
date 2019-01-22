<?php
namespace App\Model;
use App\Lib\Database;
use App\Lib\Resposta;
use PDO;
use Exception;

class Llibre
{
    private $conn; //connexió a la base de dades (PDO)
    private $resposta; // resposta
    public function __CONSTRUCT()
    {
        $this->conn = Database::getInstance()->getConnection();
        $this->resposta = new Resposta();
    }
    public function getAll($orderby = "id_llib") {
        try {
            $result = array();
            $stm = $this->conn->prepare("SELECT * FROM LLIBRES ORDER BY $orderby");
            $stm->execute();
            $tuples = $stm->fetchAll();
            $this->resposta->setDades($tuples); // array de tuples
            $this->resposta->setCorrecta(true, $stm->rowCount()); // La resposta es correcta
            return $this->resposta;
        } catch (Exception $e) { // hi ha un error posam la resposta a fals i tornam missatge d'error
            $this->resposta->setCorrecta(false, $e->getMessage());
            return $this->resposta;
        }
    }
    public function get($id) {
        try{
            $id_llib = $id;
            $sql = "SELECT * FROM LLIBRES where ID_LLIB = :id_llib";
            $stm=$this->conn->prepare($sql);
            $stm->bindValue(":id_llib",$id_llib);
            $stm->execute();
            $row=$stm->fetch();
            $this->resposta->SetDades($row);
            $this->resposta->setCorrecta(true, $stm->rowCount());
            return $this->resposta;
        }catch(Exception $e){
            $this->resposta->setCorrecta(false, "Error get ID: ".$e->getMessage());
            return $this->resposta;
        }
    }
    public function insert($data)
    {
        try {
            $sql = "SELECT max(id_llib) as N from llibres";
            $stm = $this->conn->prepare($sql);
            $stm->execute();
            $row = $stm->fetch();
            $id_llib = $row["N"] + 1;
            $titol = !empty($data['TITOL']) ? $data['TITOL'] : NULL;
            $numEdicio = !empty($data['NUMEDICIO']) ? $data['NUMEDICIO'] : NULL;
            $llocEdicio = !empty($data['LLOCEDICIO']) ? $data['LLOCEDICIO'] : NULL;
            $anyEdicio = !empty($data['ANYEDICIO']) ? $data['ANYEDICIO'] : NULL;
            $descLlibre = !empty($data['DESCRIP_LLIB']) ? $data['DESCRIP_LLIB'] : NULL;
            $isbn = !empty($data['ISBN']) ? $data['ISBN'] : NULL;
            $deplegal = !empty($data['DEPLEGAL']) ? $data['DEPLEGAL'] : NULL;
            $signtop = !empty($data['SIGNTOP']) ? $data['SIGNTOP'] : NULL;
            $dataBaixa = !empty($data['DATBAIXA_LLIB']) ? $data['DATBAIXA_LLIB'] : NULL;
            $motiuBaixa = !empty($data['MOTIUBAIXA']) ? $data['MOTIUBAIXA'] : NULL;
            $fkColleccio = !empty($data['FK_COLLECCIO']) ? $data['FK_COLLECCIO'] : NULL;
            $fkDepart = !empty($data['FK_DEPARTAMENT']) ? $data['FK_DEPARTAMENT'] : NULL;
            $fkIdedit = !empty($data['FK_IDEDIT']) ? $data['FK_IDEDIT'] : NULL;
            $fkLlengua = !empty($data['FK_LLENGUA']) ? $data['FK_LLENGUA'] : NULL;
            $imgLlib = !empty($data['IMG_LLIB']) ? $data['IMG_LLIB'] : NULL;
            $sql = "INSERT INTO llibres
                            (ID_LLIB, TITOL, NUMEDICIO, LLOCEDICIO, ANYEDICIO, DESCRIP_LLIB, ISBN, DEPLEGAL, SIGNTOP, DATBAIXA_LLIB, MOTIUBAIXA, FK_COLLECCIO, FK_DEPARTAMENT, FK_IDEDIT, FK_LLENGUA, IMG_LLIB)
                            VALUES (:id_llib,:titol,:numedicio,:llocedicio,:anyedicio,:descripcio_llib,:isbn,:deplegal,:signtop,:databaixa_llib,:motiubaixa,:fk_colleccio,:fk_departament,:fk_idedit,:fk_llengua,:img_llib)";
            $stm = $this->conn->prepare($sql);
            $stm->bindValue(':id_llib', $id_llib);
            $stm->bindValue(':titol', $titol);
            $stm->bindValue(':numedicio', !empty($numEdicio) ? $numEdicio : NULL, PDO::PARAM_STR);
            $stm->bindValue(':llocedicio', !empty($llocEdicio) ? $llocEdicio : NULL, PDO::PARAM_STR);
            $stm->bindValue(':anyedicio', !empty($anyEdicio) ? $anyEdicio : NULL, PDO::PARAM_STR);
            $stm->bindValue(':descripcio_llib', !empty($descLlibre) ? $descLlibre : NULL, PDO::PARAM_STR);
            $stm->bindValue(':isbn', !empty($isbn) ? $isbn : NULL, PDO::PARAM_STR);
            $stm->bindValue(':deplegal', !empty($deplegal) ? $deplegal : NULL, PDO::PARAM_STR);
            $stm->bindValue(':signtop', !empty($signtop) ? $signtop : NULL, PDO::PARAM_STR);
            $stm->bindValue(':databaixa_llib', !empty($dataBaixa) ? $dataBaixa : NULL, PDO::PARAM_STR);
            $stm->bindValue(':motiubaixa', !empty($motiuBaixa) ? $motiuBaixa : NULL, PDO::PARAM_STR);
            $stm->bindValue(':fk_colleccio', !empty($fkColleccio) ? $fkColleccio : NULL, PDO::PARAM_STR);
            $stm->bindValue(':fk_departament', !empty($fkDepart) ? $fkDepart : NULL, PDO::PARAM_STR);
            $stm->bindValue(':fk_idedit', !empty($fkIdedit) ? $fkIdedit : NULL, PDO::PARAM_STR);
            $stm->bindValue(':fk_llengua', !empty($fkLlengua) ? $fkLlengua : NULL, PDO::PARAM_STR);
            $stm->bindValue(':img_llib', !empty($imgLlib) ? $imgLlib : NULL, PDO::PARAM_STR);
            $stm->execute();
            $this->resposta->setCorrecta(true, $stm->rowCount());
            return $this->resposta;
        } catch (Exception $e) {
            $this->resposta->setCorrecta(false, "Error insertant: " . $e->getMessage());
            return $this->resposta;
        }
    }
    public function modificaLlibre($dades)
    {
        try {
            /*PROBLEMA, he de montar l'sql perque si pos directament a l'sql el nom de les columnes i el vindvalues,
            si li pas per post 3 columnes, borrará les demés*/
            $sql = "UPDATE LLIBRES SET";
            $strAfegir = "";
            $stm = $this->conn->prepare($sql);
            if (isset($dades["titol"])) {
                $strAfegir .= ", TITOL = :titol";
            }
            if (isset($dades["numEdicio"])) {
                $strAfegir .= ", NUMEDICIO = :numEdicio";
            }
            if (isset($dades["llocEdicio"])) {
                $strAfegir .= ", LLOCEDICIO = :llocEdicio";
            }
            if (isset($dades["anyEdicio"])) {
                $strAfegir .= ", ANYEDICIO = :anyEdicio";
            }
            if (isset($dades["descripcio"])) {
                $strAfegir .= ", DESCRIP_LLIB = :descripcio";
            }
            if (isset($dades["isbn"])) {
                $strAfegir .= ", ISBN = :isbn";
            }
            if (isset($dades["deplegal"])) {
                $strAfegir .= ", DEPLEGAL = :deplegal";
            }
            if (isset($dades["signtop"])) {
                $strAfegir .= ", SIGNTOP = :signtop";
            }
            if (isset($dades["dataBaixa"])) {
                $strAfegir .= ", DATBAIXA_LLIB = :dataBaixa";
            }
            if (isset($dades["motiuBaixa"])) {
                $strAfegir .= ", MOTIUBAIXA = :motiuBaixa";
            }
            if (isset($dades["fkCollecio"])) {
                $strAfegir .= ", FK_COLLECCIO = :fkCollecio";
            }
            if (isset($dades["fkDepartament"])) {
                $strAfegir .= ", FK_DEPARTAMENT = :fkDepartament";
            }
            if (isset($dades["fkIdEditor"])) {
                $strAfegir .= ", FK_IDEDIT = :fkIdEditor";
            }
            if (isset($dades["fkLlengua"])) {
                $strAfegir .= ", FK_LLENGUA = :fkLlengua";
            }
            if (isset($dades["imatge"])) {
                $strAfegir .= ", IMG_LLIB = :imatge";
            }
            if (isset($dades["idLlibre"])) {
                $strAfegir .= " WHERE ID_LLIB = :idLlibre";
            }
            $strAfegir = trim($strAfegir, ",");
            $sql .= $strAfegir;
            $stm = $this->conn->prepare($sql);
            if (isset($dades["titol"])) {
                $stm->bindValue(':titol', $dades["titol"], PDO::PARAM_STR);
            }
            if (isset($dades["numEdicio"])) {
                $stm->bindValue(':numEdicio', $dades["numEdicio"], PDO::PARAM_STR);
            }
            if (isset($dades["llocEdicio"])) {
                $stm->bindValue(':llocEdicio', $dades["llocEdicio"], PDO::PARAM_STR);
            }
            if (isset($dades["anyEdicio"])) {
                $stm->bindValue(':anyEdicio', $dades["anyEdicio"], PDO::PARAM_INT);
            }
            if (isset($dades["descripcio"])) {
                $stm->bindValue(':descripcio', $dades["descripcio"], PDO::PARAM_STR);
            }
            if (isset($dades["isbn"])) {
                $stm->bindValue(':isbn', $dades["isbn"], PDO::PARAM_STR);
            }
            if (isset($dades["deplegal"])) {
                $stm->bindValue(':deplegal', $dades["deplegal"], PDO::PARAM_STR);
            }
            if (isset($dades["signtop"])) {
                $stm->bindValue(':signtop', $dades["signtop"], PDO::PARAM_STR);
            }
            if (isset($dades["dataBaixa"])) {
                $stm->bindValue(':dataBaixa', $dades["dataBaixa"], PDO::PARAM_STR);
            }
            if (isset($dades["motiuBaixa"])) {
                $stm->bindValue(':motiuBaixa', $dades["motiuBaixa"], PDO::PARAM_STR);
            }
            if (isset($dades["fkCollecio"])) {
                $stm->bindValue(':fkCollecio', $dades["fkCollecio"], PDO::PARAM_STR);
            }
            if (isset($dades["fkDepartament"])) {
                $stm->bindValue(':fkDepartament', $dades["fkDepartament"], PDO::PARAM_STR);
            }
            if (isset($dades["fkIdEditor"])) {
                $stm->bindValue(':fkIdEditor', $dades["fkIdEditor"], PDO::PARAM_INT);
            }
            if (isset($dades["fkLlengua"])) {
                $stm->bindValue(':fkLlengua', $dades["fkLlengua"], PDO::PARAM_STR);
            }
            if (isset($dades["imatge"])) {
                $stm->bindValue(':imatge', $dades["imatge"], PDO::PARAM_STR);
            }
            if (isset($dades["idLlibre"])) {
                $stm->bindValue(':idLlibre', $dades["idLlibre"], PDO::PARAM_INT);
            }
            $stm->execute();
            $this->resposta->setCorrecta(true, $stm->rowCount());
            return $this->resposta;
        } catch (Exception $e) {
            $this->resposta->setCorrecta(false, "Error modificant: " . $e->getMessage());
            return $this->resposta;
        }
    }
    public function borrarLlibre($dades){
        try{
        $sql = "DELETE FROM LLIBRES WHERE ID_LLIB = :idLlib";
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':idLlib', $dades['id'], PDO::PARAM_INT);
        $stm->execute();
        $this->resposta->setCorrecta(true, $stm->rowCount());
        return $this->resposta;
        } catch(Exception $e){
            $this->resposta->setCorrecta(false, "Error borrant: " . $e->getMessage());
            return $this->resposta;
        }
    }
    public function filtra($where, $orderby) {
        try {
            $buscar = true;
            $limit = false;
            $ntuplas = false;
            $sql = "SELECT * from LLIBRES";
            if (strlen($where) == 0) {
                $buscar = false;
            } else if (is_numeric($where)) {
                $sql = $sql . " WHERE id_llib like :w";
            } else {
                $sql = $sql . " WHERE titol like :w";
            }
            if (strlen($orderby) == 0) {
                
            } else {
                $orderby = filter_var($orderby, FILTER_SANITIZE_STRING);
                $sql = $sql . " ORDER BY $orderby";
            }
            if ($count != "") {
                $limit = true;
                if ($offset != "") {
                    $ntuplas = true;
                    $sql = $sql . " limit :offset, :count";
                } else {
                    $sql = $sql . " limit :count";
                }
            }
            $stm = $this->conn->prepare($sql);
            if ($buscar) {
                $stm->bindValue(':w', '%' . $where . '%');
            }
            $stm->execute();
            $tuples = $stm->fetchAll();
            $this->resposta->setDades($tuples);
            $this->resposta->setCorrecta(true);
            return $this->resposta;
        } catch (Exeption $e) {
            $this->resposta->setCorrecta(false, "Error insertant: " . $e->getMessage());
            return $this->resposta;
        }
    }
}