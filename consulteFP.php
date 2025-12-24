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

// Récupérer toutes les fiches de poste
$sql = "SELECT * FROM FicheDePoste";
$result = $conn->query($sql);

// Fermer la connexion
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toutes les Fiches de Poste</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <style>
        /* Centrage de la page */
        body {
            font-family: "Nunito", sans-serif;
            background-color: #f0f0f5;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column; /* Structure verticale */
            height: 100vh;
            margin: 0;
            overflow: hidden;
              background: linear-gradient(45deg, #1100ffff, #960000ff); /* Dégradé de fond */
        }

        /* Conteneur principal */
        .container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            max-width: 1200px;
            padding: 20px;
            width: 100%;
            margin-top: 30px;
            max-height: 80vh; /* Limite la hauteur du conteneur */
            overflow-y: auto; /* Permet le défilement vertical */
            scroll-behavior: smooth; /* Animation fluide lors du défilement */
        }

        /* Carte pour chaque fiche */
        .fiche-card {
            background-color: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .fiche-card:hover {
            transform: translateY(-10px);
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.15);
        }

        .fiche-card h3 {
            color: #ff0000ff;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .fiche-card p {
            color: #555;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .fiche-card .compétence {
            font-weight: bold;
        }

        .fiche-card .localisation {
            color: #b20000ff;
            font-weight: bold;
        }

        /* Animation de l'apparition */
        .container {
            opacity: 0;
            transform: translateY(50px);
            animation: fadeIn 1s forwards;
        }
        
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(50px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Style du bouton retour */
        .back-button {
            background-color: #ff0000ff;
            padding: 10px 20px;
            border: none;
            color: white;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
            position: fixed; /* Fixer en bas */
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%); /* Centrer le bouton */
            z-index: 1000; /* Assurer qu'il reste au-dessus du contenu */
        }

        .back-button:hover {
            background-color: #b40a0aff;
        }
    </style>
</head>
<body>

    <h1 style="color: white; font-size: 36px; font-weight: 700; margin-top: 50px;">Liste des Fiches de Poste</h1>

    <div class="container">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="fiche-card">
                    <h3><?php echo $row['Titre']; ?></h3>
                    <p><strong>Description :</strong> <?php echo $row['Description']; ?></p>
                    <p><strong class="compétence">Compétence :</strong> <?php echo $row['Compétence']; ?></p>
                    <p><strong>Responsabilité :</strong> <?php echo $row['Responsabilité']; ?></p>
                    <p><strong class="localisation">Localisation :</strong> <?php echo $row['Localisation']; ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color: white;">Aucune fiche de poste disponible.</p>
        <?php endif; ?>
    </div>

    <!-- Bouton Retour centré en bas -->
    <button class="back-button" onclick="goBack()">Retour</button>

    <script>
        function goBack() {
            window.location.href = 'rh_page.php';  // Lien vers la page RH
        }
    </script>

</body>
</html>
