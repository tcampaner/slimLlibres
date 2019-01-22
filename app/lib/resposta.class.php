<?php
namespace App\Lib;

class Resposta
{
	public $dades    = null;     //Dades generades per l'acció.
	public $correcta    = false;    //Operació correcta?
	public $missatge    = '';
	public $rowcount ='';
	
	public function SetCorrecta($correcta, $rc=0, $m = '')
	{
		$this->correcta = $correcta;
		$this->missatge = $m;
		$this->rowcount = $rc;

		if(!$correcta && $m = '') {
            $this->missatge = 'Hi ha hagut un error inesperat';
        }    
    }
    
    public function SetDades($dades)
    {
        $this->dades=$dades;
    }
}