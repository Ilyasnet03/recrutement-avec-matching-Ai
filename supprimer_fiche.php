<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_system";

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialisation des variables
$id = "";

// Vérifier si l'ID est soumis via GET ou POST
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Vérifier si l'ID existe dans la base de données
    $sql = "SELECT * FROM FicheDePoste WHERE ID = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // ID trouvé, procéder à la suppression
        $sql_delete = "DELETE FROM FicheDePoste WHERE ID = $id";
        if ($conn->query($sql_delete) === TRUE) {
            echo "<script>alert('Fiche de Poste supprimée avec succès'); window.location.href = 'supprimer_fiche.php';</script>";
        } else {
            echo "<script>alert('Erreur lors de la suppression : " . $conn->error . "');</script>";
        }
    } else {
        // Si l'ID n'est pas trouvé
        echo "<script>alert('Aucune fiche trouvée avec cet ID');</script>";
    }
}

// Fermer la connexion
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer une Fiche de Poste</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <style>
        /* Centrage de la page */
        body {
            font-family: "Nunito", sans-serif;
            background-color: #f0f0f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden; /* Cache les barres de défilement */
             background: linear-gradient(45deg, #1100ffff, #960000ff); /* Dégradé de fond */
        }

        /* Conteneur principal */
        .container {
            display: flex;
            flex-direction: column; /* Pour empiler les éléments */
            align-items: center; /* Centrer horizontalement */
            max-width: 700px; /* Largeur maximale du formulaire */
            background-color: white;
            padding: 30px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            width: 100%;
            height: auto;
            max-height: 600px; /* Limite la hauteur du formulaire */
            overflow-y: auto; /* Permet le défilement vertical */
            scroll-behavior: smooth; /* Animation fluide lors du défilement */
            opacity: 0;
            transform: translateY(50px);
            animation: fadeInUp 1s forwards; /* Animation de défilement du formulaire */
        }

        /* Animation du formulaire qui fait apparaître du bas vers le centre */
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(50px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Centrer la barre de recherche */
        .search-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            width: 100%;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Centrer l'input dans la barre de recherche */
        .search-container input {
            padding: 15px;
            width: 70%; /* Limite la largeur de l'input */
            margin-bottom: 10px;
            border-radius: 5px;
            border: 2px solid #0026ffff;
            font-size: 16px;
            text-align: center; /* Centre le texte dans le champ */
            transition: width 0.4s ease, border 0.3s ease, transform 0.3s ease;
        }

        /* Animation de l'input au focus */
        .search-container input:focus {
            border: 2px solid #ee0979; /* Changer la bordure lors du focus */
            transform: scale(1.05); /* Légère augmentation de taille au focus */
        }

        /* Bouton centré sous l'input */
        .search-container button {
            padding: 15px;
            background-color: #ff0000ff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease; /* Transition fluide pour tous les effets */
            width: 70%; /* Largeur du bouton égale à celle de l'input */
            margin-top: 10px;
        }

        /* Effet de survol du bouton */
        .search-container button:hover {
            background-color: #ee0924ff;
            transform: scale(1.1); /* Effet de zoom sur le bouton au survol */
        }
        .search-title {
    color: black; /* Couleur du texte */
    font-size: 36px; /* Taille du texte */
    font-weight: 700; /* Poids du texte */
    text-align: center; /* Centrer le texte */
    text-transform: uppercase; /* Transformer le texte en majuscules */
    letter-spacing: 2px; /* Espacement entre les lettres */
    position: relative; /* Pour positionner l'ombre et les effets */
    padding: 20px; /* Espacement interne */
    background: linear-gradient(45deg,rgb(31, 19, 11),rgb(63, 43, 53)); /* Dégradé de fond */
    -webkit-background-clip: text; /* Applique le dégradé au texte */
    color: transparent; /* Rendre le texte transparent */
    animation: shineEffect 1.5s infinite; /* Animation de l'effet de brillance */
}

/* Effet de survol avec un léger zoom */
.search-title:hover {
    transform: scale(1.1); /* Légère augmentation de taille */
    text-shadow: 0 0 10px rgba(25, 23, 24, 0.7), 0 0 20px rgba(88, 10, 49, 0.6); /* Ombre du texte pour un effet lumineux */
    cursor: pointer; /* Changer le curseur pour indiquer que l'élément est interactif */
}
    </style>
</head>
<body>

    <div class="container">
       <h2 class="search-title">Supprimer une Fiche de Poste par ID</h2>
        <div class="search-container">
            <form method="POST" action="supprimer_fiche.php">
                <input type="text" name="id" placeholder="Entrez l'ID de la fiche à supprimer" required>
                <button type="submit">Supprimer</button>
                 <button class="back-button" onclick="goBack()">Retour</button>
            </form>
        </div>
    </div>
  <script>
      
         function goBack() {
            window.location.href = 'rh_page.php';
        }
    </script>

</body>
</html>
