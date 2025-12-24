<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";  // Utilisateur par défaut dans XAMPP
$password = "";      // Mot de passe par défaut dans XAMPP
$dbname = "login_system"; // Nom de ta base de données

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $competence = $_POST['competence'];
    $responsabilite = $_POST['responsabilite'];
    $localisation = $_POST['localisation'];

    // Utiliser une requête préparée pour éviter les injections SQL
    $stmt = $conn->prepare("INSERT INTO FicheDePoste (Titre, Description, Compétence, Responsabilité, Localisation)
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $titre, $description, $competence, $responsabilite, $localisation);

    if ($stmt->execute()) {
        echo "<script>alert('Fiche de Poste ajoutée avec succès'); window.location.href = '#';</script>";
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
    <title>Ajouter une Fiche de Poste</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <style>
        body {
            font-family: "Nunito", sans-serif;
            
            padding: 20px;
             background: linear-gradient(45deg, #1100ffff, #960000ff); /* Dégradé de fond */
        }

        .container {

            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .input-block {
            margin-bottom: 15px;
        }

        .input-label {
            font-size: 14px;
            color: #555;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        button {
              padding: 15px;
            background-color: #f10d0dff;
            color: white;
            border: none;
            margin-left: 180px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease; /* Transition fluide pour tous les effets */
            width: 30%; /* Largeur du bouton égale à celle de l'input */
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #191919;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Ajouter une Fiche de Poste</h1>
        <form method="POST" action="">
            <div class="input-block">
                <label for="titre" class="input-label">Titre</label>
                <input type="text" id="titre" name="titre" required>
            </div>

            <div class="input-block">
                <label for="description" class="input-label">Description</label>
                <textarea id="description" name="description" required></textarea>
            </div>

            <div class="input-block">
                <label for="competence" class="input-label">Compétence</label>
                <input type="text" id="competence" name="competence" required>
            </div>

            <div class="input-block">
                <label for="responsabilite" class="input-label">Responsabilité</label>
                <input type="text" id="responsabilite" name="responsabilite" required>
            </div>

            <div class="input-block">
                <label for="localisation" class="input-label">Localisation</label>
                <input type="text" id="localisation" name="localisation" required>
            </div>

            <button type="submit">Ajouter la Fiche</button>
        </form>
         <button class="back-button" onclick="goBack()">Retour</button>
    </div>
<script>
       
         function goBack() {
            window.location.href = 'rh_page.php';
        }
    </script>

</body>
</html>
