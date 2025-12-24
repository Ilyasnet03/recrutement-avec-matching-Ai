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
$id = $titre = $description = $competence = $responsabilite = $localisation = "";

// Vérifier si l'ID est soumis via GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Récupérer les données de la fiche de poste en fonction de l'ID
    $sql = "SELECT * FROM FicheDePoste WHERE ID = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $titre = $row['Titre'];
        $description = $row['Description'];
        $competence = $row['Compétence'];
        $responsabilite = $row['Responsabilité'];
        $localisation = $row['Localisation'];
    } else {
        // Si aucune fiche n'est trouvée, afficher un message et demander un nouvel ID
        echo "<script>
                alert('Aucune fiche trouvée avec cet ID. Veuillez entrer un autre ID.');
                window.location.href = 'modifier_fiche.php'; // Redirige vers la même page pour essayer un autre ID
              </script>";
    }
}

// Vérifier si le formulaire a été soumis pour mettre à jour les données
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $competence = $_POST['competence'];
    $responsabilite = $_POST['responsabilite'];
    $localisation = $_POST['localisation'];

    // Utiliser une requête préparée pour mettre à jour la fiche de poste
    $stmt = $conn->prepare("UPDATE FicheDePoste SET Titre=?, Description=?, Compétence=?, Responsabilité=?, Localisation=? WHERE ID=?");
    $stmt->bind_param("sssssi", $titre, $description, $competence, $responsabilite, $localisation, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Fiche de Poste mise à jour avec succès'); window.location.href = 'modifier_fiche.php?id=" . $id . "';</script>";
    } else {
        echo "<script>alert('Erreur : " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Fermer la connexion
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une Fiche de Poste</title>
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
            display: flex; /* Utilisation de flexbox pour centrer les éléments */
            justify-content: center; /* Centre les éléments horizontalement */
            align-items: center; /* Centre les éléments verticalement */
            flex-direction: column; /* Empile les éléments (le champ de saisie et le bouton) */
            width: 100%; /* Prend toute la largeur disponible */
            margin-bottom: 20px;
            text-align: center;
            opacity: 0;
            transform: translateY(-50px); /* Positionner initialement vers le haut */
            animation: fadeIn 1s forwards; /* Animation d'apparition */
        }

        /* Animation d'apparition de la barre de recherche */
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(-50px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Centrer l'input dans la barre de recherche */
        .search-container input {
            padding: 15px;
            width: 70%; /* Limite la largeur de l'input */
            margin-bottom: 10px; /* Espace entre l'input et le bouton */
             margin-left: -15px;
            border-radius: 5px;
            border: 2px solid #ff6a00;
            font-size: 16px;
            text-align: center; /* Centre le texte dans le champ */
            transition: width 0.4s ease, border 0.3s ease, transform 0.3s ease; /* Transitions pour la largeur et la bordure */
        }

        /* Animation de l'input au focus */
        .search-container input:focus {
            border: 2px solid #ee0920ff; /* Changer la bordure lors du focus */
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
            margin-top: 10px; /* Ajouter un petit espace au-dessus du bouton */
            opacity: 0;
            transform: translateY(20px); /* Positionner initialement vers le bas */
            animation: fadeInButton 1s 0.5s forwards; /* Animation du bouton */
        }

        /* Animation d'apparition du bouton */
        @keyframes fadeInButton {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Effet de survol du bouton */
        .search-container button:hover {
              transform: scale(1.1); /* Légère augmentation de taille */
    text-shadow: 0 0 10px rgba(25, 23, 24, 0.7), 0 0 20px rgba(88, 10, 49, 0.6); /* Ombre du texte pour un effet lumineux */
    cursor: pointer; /* Changer le curseur pour indiquer que l'élément est interactif */
            background-color: #ee0928ff;
            transform: scale(1.1); /* Effet de zoom sur le bouton au survol */
        }

        /* Animation du focus */
        .search-container input:focus {
            border-color: #ee0909ff;
            transform: scale(1.1);
        }

        /* Formulaire de modification */
        .form-container {
            width: 100%;
            opacity: 0;
            transform: translateX(100%);
            transition: all 1s ease-out;
        }

        .form-container.active {
            opacity: 1;
            transform: translateX(0);
        }

        .input-block {
            margin-bottom: 20px;
            width: 100%;
        }

        .input-label {
            font-size: 16px;
            color: #333;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 2px solid #ddd;
            font-size: 14px;
            margin-top: 6px;
        }
        

        button {
            width: 100%;
            padding: 15px;
            background-color: #d30202ff;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
             transform: scale(1.0.5); /* Légère augmentation de taille */
    text-shadow: 0 0 10px rgba(25, 23, 24, 0.7), 0 0 20px rgba(88, 10, 49, 0.6); /* Ombre du texte pour un effet lumineux */
    cursor: pointer; /* Changer le curseur pour indiquer que l'élément est interactif */
            background-color:rgb(235, 38, 38);
           
            
        }

        .form-container textarea {
            height: 150px;
        }
     /* Style du titre */
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

/* Animation de brillance sur le titre */
@keyframes shineEffect {
    0% {
        background-position: -200%;
    }
    50% {
        background-position: 200%;
    }
    100% {
        background-position: -200%;
    }
}
 h1 {
            text-align: center; /* Centrer le titre */
            font-size: 36px;
            font-weight: 600;
            color: #55311c;
            margin-bottom: 20px;
        }

    </style>
</head>
<body>

    <div class="search-container" id="searchContainer">
        <h2 class="search-title">Rechercher une Fiche de Poste par ID</h2>

        <form method="GET" action="modifier_fiche.php">
            <input type="text" name="id" placeholder="Entrez l'ID de la fiche de poste" required>
            <button type="submit">Chercher</button>
             <button class="back-button" onclick="goBack()">Retour</button>
        </form>
    </div>
    

    <?php if (isset($id) && $id != ""): ?>
    <div class="container">
        <div class="form-container <?php echo (isset($id) && $id != "") ? 'active' : ''; ?>">
            <h1>Modifier une Fiche de Poste</h1>
            <form method="POST" action="">
                <div class="input-block">
                    <label for="titre" class="input-label">Titre</label>
                    <input type="text" id="titre" name="titre" value="<?php echo $titre; ?>" required>
                </div>

                <div class="input-block">
                    <label for="description" class="input-label">Description</label>
                    <textarea id="description" name="description" required><?php echo $description; ?></textarea>
                </div>

                <div class="input-block">
                    <label for="competence" class="input-label">Compétence</label>
                    <input type="text" id="competence" name="competence" value="<?php echo $competence; ?>" required>
                </div>

                <div class="input-block">
                    <label for="responsabilite" class="input-label">Responsabilité</label>
                    <input type="text" id="responsabilite" name="responsabilite" value="<?php echo $responsabilite; ?>" required>
                </div>

                <div class="input-block">
                    <label for="localisation" class="input-label">Localisation</label>
                    <input type="text" id="localisation" name="localisation" value="<?php echo $localisation; ?>" required>
                </div>

                <button type="submit">Mettre à jour la Fiche</button>
                <a href="rh_page.php" class="back-button">Retour</a>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script>
        // Lorsque le formulaire apparaît, on cache la barre de recherche
        if (window.location.search.includes('id=')) {
            document.querySelector('.form-container').classList.add('active');
            document.getElementById('searchContainer').style.display = 'none'; // Masque la barre de recherche
        }
         function goBack() {
            window.location.href = 'rh_page.php';
        }
    </script>

</body>
</html>
