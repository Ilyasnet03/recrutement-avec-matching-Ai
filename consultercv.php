<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_system";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Si un fichier PDF est demandé
if (isset($_GET['id'])) {
    $fileId = $_GET['id'];
    $sql = "SELECT fichier_pdf FROM fichiers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $fileId);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($fichierPdf);
    $stmt->fetch();

    if ($fichierPdf) {
        header("Content-Type: application/pdf");
        echo $fichierPdf;
    } else {
        echo "Fichier introuvable.";
    }
    $conn->close();
    exit;
}

// Supprimer un fichier
if (isset($_GET['delete_id'])) {
    $fileId = $_GET['delete_id'];
    $deleteSql = "DELETE FROM fichiers WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param("i", $fileId);
    $deleteStmt->execute();
    $deleteStmt->close();
    header("Location: " . $_SERVER['PHP_SELF']); // Rediriger après suppression
    exit;
}

// Récupérer la liste des fichiers
$sql = "SELECT id, titre FROM fichiers";
$result = $conn->query($sql);

$files = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $files[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Fichiers</title>
    <style>
        body {
            font-family: "Nunito", sans-serif;
            margin: 0;
            padding: 0;
           background: linear-gradient(45deg, #1100ffff, #960000ff); /* Dégradé de fond */
            display: flex;
            justify-content: flex-start;
            align-items: center;
            flex-direction: column; /* Structure verticale */
            height: 100vh;
            overflow: hidden;
            padding-top: 20px;
        }

        .container {
            display: flex;
            flex-direction: column; /* Les éléments seront disposés verticalement */
            align-items: center; /* Centrage horizontal */
            gap: 20px;
            max-width: 1200px;
            width: 100%;
            margin-top: 20px;
            padding: 20px;
        }

        h1 {
            color: black;
            font-size: 36px;
            text-align: center; /* Centre le titre */
            margin-bottom: 20px;
            animation: fadeInTitle 1s ease-in-out;
        }

        /* Animation de fade-in pour le titre */
        @keyframes fadeInTitle {
            0% {
                opacity: 0;
                transform: translateY(-50px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        table {
            width: 80%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            border-radius: 10px;
            opacity: 0;
            animation: fadeInTable 1s forwards; /* Animation de table */
        }

        @keyframes fadeInTable {
            0% {
                opacity: 0;
                transform: translateY(50px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        table, th, td {
            border: 1px solid #be0202ff;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #d20303ff;
            color: white;
        }

        button {
            padding: 5px 10px;
            background-color: #053bdbff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #3b1fb7ff;
        }

        /* Effet de survol pour chaque ligne */
        tr:hover {
            background-color: #f0f0f5;
            cursor: pointer;
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

        /* Barre de recherche */
        .search-bar {
            margin-bottom: 20px;
            padding: 10px;
            width: 80%;
            max-width: 400px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        /* Message de confirmation */
        .message {
            padding: 10px;
            background-color: #ff0000ff;
            color: white;
            text-align: center;
            margin-bottom: 20px;
            display: none; /* Caché par défaut */
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
            display: block;
            margin: 20px auto 0;
            margin-left: 10px;
        }

        .back-button:hover {
            background-color: #e50000ff;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Message de confirmation -->
        <div id="message" class="message">Fichier consulté avec succès !</div>

        <h1>Liste des CV </h1>

        <!-- Barre de recherche -->
        <input class="search-bar" type="text" id="searchInput" placeholder="Rechercher un fichier..." onkeyup="filterFiles()">

        <table id="filesTable">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Consulter</th>
                    <th>Supprimer</th>
                </tr>
            </thead>
            <tbody id="filesList">
                <?php foreach ($files as $file): ?>
                    <tr>
                        <td><?php echo $file['titre']; ?></td>
                        <td><button onclick="viewFile(<?php echo $file['id']; ?>)">Consulter le CV</button></td>
                        <td><button onclick="deleteFile(<?php echo $file['id']; ?>)">Supprimer</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Bouton Retour -->
        <button class="back-button" onclick="goBack()">Retour</button>
    </div>

    <script>
        // Fonction pour afficher le fichier PDF
        function viewFile(fileId) {
            window.open('?id=' + fileId, '_blank');
            showMessage('Fichier consulté avec succès !');
        }

        // Fonction pour supprimer un fichier
        function deleteFile(fileId) {
            if (confirm("Êtes-vous sûr de vouloir supprimer ce fichier ?")) {
                window.location.href = "?delete_id=" + fileId;
            }
        }

        // Afficher un message de confirmation
        function showMessage(message) {
            const messageElement = document.getElementById('message');
            messageElement.textContent = message;
            messageElement.style.display = 'block';
            setTimeout(() => {
                messageElement.style.display = 'none';
            }, 3000);
        }

        // Fonction de filtrage des fichiers
        function filterFiles() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const rows = document.getElementById('filesList').getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                if (cells) {
                    const title = cells[0].textContent || cells[0].innerText;
                    if (title.toLowerCase().indexOf(filter) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }
        }

        // Fonction pour revenir à la page précédente
        function goBack() {
            window.location.href = 'rh_page.php';
        }
    </script>
</body>
</html>
