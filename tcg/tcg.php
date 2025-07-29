<?php
session_start(); // Démarre la session pour stocker les données entre les requêtes

// ===== CLASSES DE COMBAT =====

// Classe de base pour tous les personnages
abstract class Personnage {
    public $nom;
    public $vie;
    public $force;

    public function __construct($nom, $vie, $force) {
        $this->nom = $nom;
        $this->vie = $vie;
        $this->force = $force;
    }

    // Méthode d'attaque : inflige des dégâts à l'adversaire
    public function attaquer(Personnage $adversaire) {
        $degats = $this->force;
        $log = "{$this->nom} attaque {$adversaire->nom} et inflige $degats dégâts.<br>";
        $log .= $adversaire->recevoirDegats($degats);
        return $log;
    }

    // Réduction des points de vie suite à une attaque
    public function recevoirDegats(int $degats) {
        $this->vie -= $degats;
        return "{$this->nom} a maintenant {$this->vie} points de vie.<br><br>";
    }

    // Vérifie si le personnage est encore en vie
    public function estVivant() {
        return $this->vie > 0;
    }
}

// ===== CLASSES SPÉCIFIQUES =====

class Guerrier extends Personnage {
    public function __construct($nom) {
        parent::__construct($nom, 120, 15);
    }
}

class Voleur extends Personnage {
    public function __construct($nom) {
        parent::__construct($nom, 100, 12);
    }

    // A 30% de chance d'esquiver totalement une attaque
    public function recevoirDegats(int $degats) {
        if (rand(1, 100) <= 30) {
            return "{$this->nom} esquive l'attaque !<br><br>";
        } else {
            return parent::recevoirDegats($degats);
        }
    }
}

class Magicien extends Personnage {
    public function __construct($nom) {
        parent::__construct($nom, 90, 8);
    }

    // 50% de chance de doubler les dégâts (sort spécial)
    public function attaquer(Personnage $adversaire) {
        $degats = $this->force;
        if (rand(1, 100) <= 50) {
            $degats *= 2;
            $log = "{$this->nom} lance un sort spécial ! Dégâts doublés !<br>";
        } else {
            $log = "";
        }
        $log .= "{$this->nom} attaque {$adversaire->nom} et inflige $degats dégâts.<br>";
        $log .= $adversaire->recevoirDegats($degats);
        return $log;
    }
}

// ===== FONCTION POUR CRÉER UN PERSONNAGE =====
function creerPersonnage($type, $nom) {
    return match($type) {
        'Guerrier' => new Guerrier($nom),
        'Voleur' => new Voleur($nom),
        'Magicien' => new Magicien($nom),
        default => new Guerrier($nom),
    };
}

// ===== INITIALISATION DU COMBAT OU REDEMARRAGE =====
if (!isset($_SESSION['joueur']) || isset($_POST['reset'])) {
    // On récupère la classe choisie ou "Guerrier" par défaut
    $type = $_POST['classe'] ?? 'Guerrier';

    // Création du personnage joueur
    $joueur = creerPersonnage($type, "Vous le $type");

    // Création d’un adversaire aléatoire différent du joueur
    $classes = ['Guerrier', 'Voleur', 'Magicien'];
    do {
        $typeAdv = $classes[array_rand($classes)];
    } while ($typeAdv === $type);

    $adversaire = creerPersonnage($typeAdv, "Adversaire le $typeAdv");

    // Sauvegarde en session
    $_SESSION['joueur'] = serialize($joueur);
    $_SESSION['adversaire'] = serialize($adversaire);
    $_SESSION['log'] = ["Combat lancé entre {$joueur->nom} et {$adversaire->nom}."];
} else {
    // Récupération des objets en session
    $joueur = unserialize($_SESSION['joueur']);
    $adversaire = unserialize($_SESSION['adversaire']);

    // Tour de combat si on a cliqué sur "Attaquer"
    if (isset($_POST['attaquer']) && $joueur->estVivant() && $adversaire->estVivant()) {
        $log = [];
        $log[] = $joueur->attaquer($adversaire);

        // Si l’adversaire est encore vivant, il répond
        if ($adversaire->estVivant()) {
            $log[] = $adversaire->attaquer($joueur);
        }

        // Mise à jour des logs et états en session
        $_SESSION['log'] = array_merge($_SESSION['log'], $log);
        $_SESSION['joueur'] = serialize($joueur);
        $_SESSION['adversaire'] = serialize($adversaire);
    }
}
?>

<!-- ===== INTERFACE HTML ===== -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Combat Tour par Tour</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>⚔️ Combat Tour par Tour</h2>

    <?php if (!isset($_SESSION['joueur']) || isset($_POST['reset'])): ?>
        <!-- Formulaire de choix de classe -->
        <form method="POST">
            <label>Choisissez votre classe :</label><br>
            <select name="classe">
                <option value="Guerrier">Guerrier</option>
                <option value="Voleur">Voleur</option>
                <option value="Magicien">Magicien</option>
            </select><br>
            <button type="submit">Commencer le combat</button>
        </form>
    <?php else: ?>
        <!-- Affichage des états -->
        <div class="etat">
            <p><strong><?= $joueur->nom ?></strong> : <?= $joueur->vie ?> PV</p>
            <p><strong><?= $adversaire->nom ?></strong> : <?= $adversaire->vie ?> PV</p>
        </div>

        <!-- Actions du joueur -->
        <div class="actions">
            <?php if ($joueur->estVivant() && $adversaire->estVivant()): ?>
                <form method="POST">
                    <button name="attaquer" type="submit">🔪 Attaquer</button>
                    <button name="reset" type="submit">🔄 Recommencer</button>
                </form>
            <?php else: ?>
                <h3>
                    <?= $joueur->estVivant()
                        ? "🏆 Vous avez gagné !"
                        : "💀 Vous avez perdu..." ?>
                </h3>
                <form method="POST">
                    <button name="reset" type="submit">🔁 Rejouer</button>
                </form>
            <?php endif; ?>
        </div>

        <!-- Affichage du journal de combat -->
        <div class="log">
            <h4>Journal du combat</h4>
            <?php foreach ($_SESSION['log'] as $ligne): ?>
                <p><?= $ligne ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>
</html>
