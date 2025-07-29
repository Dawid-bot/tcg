<?php

class Chien {
    private $nom;
    private $race;

    // Constructeur
    public function __construct($nom, $race) {
        $this->nom = $nom;
        $this->race = $race;
    }

    // Getter pour le nom
    public function getNom() {
        return $this->nom;
    }

    // Setter pour le nom
    public function setNom($nouveauNom) {
        $this->nom = $nouveauNom;
    }

    // Getter pour la race
    public function getRace() {
        return $this->race;
    }

    // Méthode aboyer
    public function aboyer() {
        echo "Woof! Je suis " . $this->nom . ".<br>";
    }
}

// Créer un objet $chien1
$chien1 = new Chien("Rex", "Berger Allemand");

// Afficher le nom et la race
echo "Nom du chien : " . $chien1->getNom() . "<br>";
echo "Race du chien : " . $chien1->getRace() . "<br>";

// Changer le nom du chien
$chien1->setNom("Max");

// Faire aboyer le chien
$chien1->aboyer();

?>
