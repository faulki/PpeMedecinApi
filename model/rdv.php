<?php

class rdv {
	// Objet PDO servant à la connexion à la base
	private $pdo;

	// Connexion à la base de données
	public function __construct() {
		$config = parse_ini_file("config.ini");
		
		try {
			$this->pdo = new \PDO("mysql:host=".$config["host"].";dbname=".$config["database"].";charset=utf8", $config["user"], $config["password"]);
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	public function getAll() {
		$sql = "SELECT * FROM rdv";
		
		$req = $this->pdo->prepare($sql);
		$req->execute();
		
		return $req->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function get($id) {
		$sql = "SELECT * FROM rdv WHERE idRdv = :id";
		
		$req = $this->pdo->prepare($sql);
		$req->bindParam(':id', $id, PDO::PARAM_INT);
		$req->execute();
		
		return $req->fetch(\PDO::FETCH_ASSOC);
	}

	public function getByIdPatient($idPatient)
	{
		$sql = "SELECT * FROM rdv WHERE idPatient = :idPatient";

		$req = $this->pdo->prepare($sql);
		$req->bindParam(":idPatient", $idPatient, PDO::PARAM_INT);
		$req->execute();
		
		return $req->fetch(\PDO::FETCH_ASSOC);
	}

	public function getByTokenPatient($token)
	{
		$sql = "SELECT * FROM rdv WHERE idPatient = (SELECT idPatient FROM authentification WHERE token = :token)";

		$req = $this->pdo->prepare($sql);
		$req->bindParam(":token", $token, PDO::PARAM_STR);
		$req->execute();
		
		return $req->fetch(\PDO::FETCH_ASSOC);
	}

	public function exists($id) {
		$sql = "SELECT COUNT(*) AS nb FROM rdv WHERE idRdv = :id";
		
		$req = $this->pdo->prepare($sql);
		$req->bindParam(':id', $id, PDO::PARAM_INT);
		$req->execute();
		
		$nb = $req->fetch(\PDO::FETCH_ASSOC)["nb"];
		if($nb == 1) {
			return true;
		}
		else {
			return false;
		}
	}

    public function insert($dateHeureRdv, $token, $idMedecin)
    {
		$sql = "SELECT idRdv FROM rdv WHERE idMedecin = :idMedecin AND dateHeureRdv = :dateheure";
		$req = $this->pdo->prepare($sql);
		$req->bindParam(':idMedecin', $idMedecin, PDO::PARAM_STR);
		$req->bindParam(':dateheure', $dateHeureRdv, PDO::PARAM_STR);
		$req->execute();
		$ligne = $req->fetch();

		if($ligne != false){
			return "un rendez vous est déjà pris à cette horraire";
		}
		else {

			$ip = $_SERVER["REMOTE_ADDR"];
			$sql = "SELECT idPatient FROM authentification WHERE token = :token AND idAppareil = :ip";
			
			$req = $this->pdo->prepare($sql);
			$req->bindParam(':token', $token, PDO::PARAM_STR);
			$req->bindParam(':ip', $ip, PDO::PARAM_STR);
			$req->execute();
			
			$ligne = $req->fetch();

			if($ligne != false) {
			
				$sql = "INSERT INTO rdv (dateHeureRdv,idPatient,idMedecin) VALUES (:dateheure,:idPatient,:idMedecin)";
				$req = $this->pdo->prepare($sql);

				$req->bindParam(':dateheure', $dateHeureRdv, PDO::PARAM_STR);
				$req->bindParam(':idPatient', $ligne["idPatient"], PDO::PARAM_STR);
				$req->bindParam(':idMedecin', $idMedecin, PDO::PARAM_STR);
				return $req->execute();
			}
			else {
				return false;
			}
		}
    }

	//La variable dateRdv est au format YYYY-MM-DD HH-MI-SS.
	public function update($id, $dateRdv)
	{
		$sql = 'UPDATE rdv SET dateHeureRdv = :dateRdv WHERE idRdv = :id';

		$req = $this->pdo->prepare($sql);
		$req->bindParam(':dateRdv', $dateRdv, PDO::PARAM_STR);
		$req->bindParam(':id', $id, PDO::PARAM_INT);
		return $req->execute();
	}

	public function delete($id)
	{
		$sql = 'DELETE FROM rdv WHERE idRdv = :id';

		$req = $this->pdo->prepare($sql);
		$req->bindParam(':id', $id, PDO::PARAM_STR);
		return $req->execute();
	}
}

?>