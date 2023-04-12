<?php

class controleur {

    public function erreur404() {
        http_response_code(404);
        (new vue)->erreur404;
    }

    public function verifierAttributsJson($objetJson, $listeDesAttributs) {
        $verifier = true;
        foreach($listeDesAttributs as $unAttribut) {
            if(!isset($objetJson->$unAttribut)) {
                $verifier = false;
            }
        }
        return $verifier;
    }

    public function getPatients() {
        $donnees = null;

        if(isset($_GET["id"])) {
            if((new patient)->exists($_GET["id"])) {
                http_response_code(200);
                $donnees = (new patient)->getById($_GET["id"]);
            }
            else {
				http_response_code(404);
				$donnees = array("message" => "Patient introuvable");
			}
		}
		else {
			http_response_code(200);
			$donnees = (new patient)->getAll();
		}
		
		(new vue)->transformerJson($donnees);
	}

    public function ajouterRdv() {
		$donnees = json_decode(file_get_contents("php://input"));
		$renvoi = null;
		if($donnees === null) {
			http_response_code(400);
			$renvoi = array("message" => "JSON envoyé incorrect");
		}
		else {
			$attributsRequis = array("date", "token", "idMedecin");
			if($this->verifierAttributsJson($donnees, $attributsRequis)) {
				if((new Patient)->exist($donnees->token)) {
					$resultat = (new rdv)->insert($donnees->date, $donnees->token, $donnees->idMedecin);
					
					var_dump($resultat);
					if($resultat != false) {
						http_response_code(201);
						$renvoi = array("message" => "Ajout effectué avec succès", "idRdv" => $resultat);
					}
					else {
						http_response_code(500);
						$renvoi = array("message" => "Une erreur interne est survenue");
					}
				}
				else {
					http_response_code(400);
					$renvoi = array("message" => "La patient n'existe pas");
				}
			}
			else {
				http_response_code(400);
				$renvoi = array("message" => "Données manquantes");
			}
		}

		(new vue)->transformerJson($renvoi);
	}

    public function modifierRdv() {
		$donnees = json_decode(file_get_contents("php://input"));
		$renvoi = null;
		if($donnees === null) {
			http_response_code(400);
			$renvoi = array("message" => "JSON envoyé incorrect");
		}
		else {
			$attributsRequis = array("id", "date");
			if($this->verifierAttributsJson($donnees, $attributsRequis)) {
				if((new rdv)->exists($donnees->id)) {
					$resultat = (new rdv)->update($donnees->id, $donnees->date);
					
					if($resultat != false) {
						http_response_code(200);
						$renvoi = array("message" => "Modification effectuée avec succès");
					}
					else {
						http_response_code(500);
						$renvoi = array("message" => "Une erreur interne est survenue");
					}
				}
				else {
					http_response_code(400);
					$renvoi = array("message" => "Le rdv spécifié n'existe pas");
				}
			}
			else {
				http_response_code(400);
				$renvoi = array("message" => "Données manquantes");
			}
		}

		(new vue)->transformerJson($renvoi);
	}

    public function annulerRdv() {
		$donnees = json_decode(file_get_contents("php://input"));
		$renvoi = null;
		if($donnees === null) {
			http_response_code(400);
			$renvoi = array("message" => "JSON envoyé incorrect");
		}
		else {
			$attributsRequis = array("id");
			if($this->verifierAttributsJson($donnees, $attributsRequis)) {
				if((new rdv)->exists($donnees->id)) {
					$resultat = (new rdv)->delete($donnees->id);
					
					if($resultat != false) {
						http_response_code(200);
						$renvoi = array("message" => "Suppression effectuée avec succès");
					}
					else {
						http_response_code(500);
						$renvoi = array("message" => "Une erreur interne est survenue");
					}
				}
				else {
					http_response_code(400);
					$renvoi = array("message" => "Le rdv spécifiée n'existe pas");
				}
			}
			else {
				http_response_code(400);
				$renvoi = array("message" => "Données manquantes");
			}
		}

		(new vue)->transformerJson($renvoi);
	}

    public function getRdv() {
        $donnees = null;

        if(isset($_GET["id"])) {
            if((new rdv)->exists($_GET["id"])) {
                http_response_code(200);
                $donnees = (new rdv)->get($_GET["id"]);
            }
            else {
                http_response_code(404);
                $donnees = array("message" => "Rdv introuvable");
            }
        }
		elseif(isset($_POST["token"]))
		{
                http_response_code(200);
                $donnees = (new rdv)->getBytokenPatient($_POST["token"]);
		}
        else {
            http_response_code(200);
            $donnees = (new rdv)->getAll();
        }

        (new vue)->transformerJson($donnees);
    }

	public function inscriptionPatient() {
		$donnees = json_decode(file_get_contents("php://input"));
	    $renvoi = null;

		if($donnees === null) {
			http_response_code(400);
			$renvoi = array("message" => "JSON envoyé incorrect");
		}
		else {
			$attributsRequis = array("nom", "prenom", "rue", "cp", "ville", "tel", "login", "mdp");
			if($this->verifierAttributsJson($donnees, $attributsRequis)) {
					$resultat = (new patient)->insert($donnees->nom, $donnees->prenom, $donnees->rue, $donnees->cp, $donnees->ville, $donnees->tel, $donnees->login, $donnees->mdp);
					if($resultat != false) {
						http_response_code(201);
						$renvoi = array("message" => "Patient inscrit avec succès");
					}
					else {
						http_response_code(500);
						$renvoi = array("message" => "Une erreur interne est survenue");
					}
			}
			else {
				http_response_code(400);
				$renvoi = array("message" => "Données manquantes");
			}
		}

		(new vue)->transformerJson($renvoi);

	}

	public function connexion() {
		$donnees = json_decode(file_get_contents("php://input"));
	    $renvoi = null;

		if($donnees === null) {
			http_response_code(400);
			$renvoi = array("message" => "JSON envoyé incorrect");
		}
		else {
			$attributsRequis = array("login", "mdp");
			if($this->verifierAttributsJson($donnees, $attributsRequis)) {

				$resultat = null;

					$resultat = (new authentification)->connexion($donnees->login, $donnees->mdp);

					if($resultat != null) {
						http_response_code(201);
						$renvoi = array("message" => "Vous êtes correctement connecté", "token" => $resultat);
					}
					else {
						http_response_code(500);
						$renvoi = array("message" => "Une erreur interne est survenue");
					}
			}
			else {
				http_response_code(400);
				$renvoi = array("message" => "Données manquantes");
			}
		}

		(new vue)->transformerJson($renvoi);
	}
}

?>