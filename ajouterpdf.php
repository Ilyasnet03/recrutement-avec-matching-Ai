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
$titre = $file_path = "";
$successMessage = "";

// Vérifier si le formulaire a été soumis pour télécharger un fichier
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si un fichier a été téléchargé
    if (isset($_FILES['pdf_file'])) {
        $titre = $_POST['titre'];  // Récupérer le titre du fichier
        $file_tmp_name = $_FILES['pdf_file']['tmp_name'];  // Récupérer le fichier temporaire
        $file_name = $_FILES['pdf_file']['name'];  // Récupérer le nom du fichier

        // Option 1: Stocker le fichier sur le serveur et enregistrer le chemin dans la base de données
        if ($_POST['storage_method'] == 'server') {
            // Définir le répertoire où le fichier sera stocké sur le serveur
            $file_path = "uploads/" . basename($file_name);
            // Déplacer le fichier téléchargé vers le répertoire "uploads"
            if (move_uploaded_file($file_tmp_name, $file_path)) {
                // Insérer le titre et le chemin du fichier dans la base de données
                $stmt = $conn->prepare("INSERT INTO Fichiers (titre, chemin_pdf) VALUES (?, ?)");
                $stmt->bind_param("ss", $titre, $file_path);
                $stmt->execute();
                $successMessage = "Le fichier a été stocké sur le serveur avec succès!";
            } else {
                $successMessage = "Erreur lors du téléchargement du fichier!";
            }
        }

        // Option 2: Stocker le fichier PDF sous forme de BLOB dans la base de données
        elseif ($_POST['storage_method'] == 'database') {
            // Lire le contenu du fichier PDF
            $pdf_content = file_get_contents($file_tmp_name);
            // Insérer le titre et le fichier PDF en tant que BLOB dans la base de données
            $stmt = $conn->prepare("INSERT INTO Fichiers (titre, fichier_pdf) VALUES (?, ?)");
            $stmt->bind_param("sb", $titre, $null);
            $stmt->send_long_data(1, $pdf_content);  // Envoi du fichier binaire
            $stmt->execute();
            $successMessage = "Le fichier a été stocké dans la base de données avec succès!";
        }
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
    <title>Télécharger un Fichier PDF</title>
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
            overflow: hidden;
           background: linear-gradient(45deg, #1100ffff, #960000ff); /* Dégradé de fond */
        }

        /* Conteneur principal */
        .container {
            background: rgba(255, 255, 255, 0.9);
            padding: 40px 60px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        .page-title {
            font-size: 36px;
            font-weight: 600;
            color: #55311c;
            margin-bottom: 20px;
        }

        .input-block {
            margin-bottom: 20px;
        }

        .input-label {
            font-size: 16px;
            color: #333;
        }

        input[type="text"], input[type="file"] {
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
            background-color: #da0808ff;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background-color: #ee0979;
            transform: scale(1.05);
        }

        /* Bouton de retour */
        .back-button {
            margin-top: 40px;
            background-color: rgb(167, 11, 11);
            color: white;
            padding: 15px 30px;
            border-radius: 30px;
            text-decoration: none;
            display: inline-block;
        }

        .back-button:hover {
            background-color: rgb(188, 34, 34);
        }

        /* Message de succès */
        .success-message {
            margin-top: 10px;
            background-color: black;
            color: white;
            padding: 15px;
            border-radius: 5px;
            font-size: 16px;
            display: none; /* Masqué au départ */
            animation: showMessage 1s ease-out forwards;
        }

        /* Animation du message de succès */
        @keyframes showMessage {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

    </style>
</head>
<body>

<div class="container">
    <!-- Message de succès dynamique (affiché en haut du titre) -->
    <?php if (isset($successMessage)) : ?>
        <div class="success-message" id="successMessage">
            <?php echo $successMessage; ?>
        </div>
        <script>
            // Affichage du message de succès avec animation
            document.getElementById('successMessage').style.display = 'block';
            setTimeout(function () {
                document.getElementById('successMessage').style.display = 'none';
            }, 3000); // Masquer le message après 3 secondes
        </script>
    <?php endif; ?>

    <h1 class="page-title">Télécharger un Fichier PDF</h1>

    <!-- Formulaire de téléchargement de fichier -->
    <form method="POST" enctype="multipart/form-data">
        <div class="input-block">
            <label for="titre" class="input-label">Titre du fichier</label>
            <input type="text" name="titre" id="titre" placeholder="Entrez le titre du fichier" required>
        </div>

        <div class="input-block">
            <label for="pdf_file" class="input-label">Choisissez un fichier PDF</label>
            <input type="file" name="pdf_file" id="pdf_file" accept=".pdf" required>
        </div>

        <div class="input-block">
            <label for="storage_method" class="input-label">Choisissez le mode de stockage</label>
            <select name="storage_method" id="storage_method" required>
                <option value="database">Stocker dans la base de données</option>
                <option value="server">Stocker sur le serveur</option>
                
            </select>
        </div>

        <button type="submit">Télécharger</button>
    </form>

    <!-- Bouton de retour -->
    <a href="rh_page.php" class="back-button">Retour</a>
</div>

</body>
</html>
