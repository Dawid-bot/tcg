<?php


class Humain {
    // Propriété protégée : accessible dans cette classe et les classes qui en héritent
    protected $nom;

    // Constructeur : appelé automatiquement quand on crée un nouvel objet
    public function __construct($nom) {
        $this->nom = $nom; // On stocke le nom dans la propriété $nom
    }

    // Méthode de présentation de base
    public function sePresenter() {
        echo "Bonjour, je m'appelle " . $this->nom . ".<br>";
    }
}

// Classe Homme qui hérite de Humain
class Homme extends Humain {
    // On redéfinit (override) la méthode sePresenter pour l’adapter à un homme
    public function sePresenter() {
        echo "Je suis un homme et je m'appelle " . $this->nom . ".<br>";
    }
}

// Classe Femme qui hérite de Humain
class Femme extends Humain {
    // On redéfinit (override) la méthode sePresenter pour l’adapter à une femme
    public function sePresenter() {
        echo "Je suis une femme et je m'appelle " . $this->nom . ".<br>";
    }
}

// Création d’un objet de la classe Homme avec le nom "Pierre"
$homme = new Homme("Pierre");

// Création d’un objet de la classe Femme avec le nom "Claire"
$femme = new Femme("Claire");

// Appel de la méthode sePresenter pour chaque objet
$homme->sePresenter();   
$femme->sePresenter();   
