<?php
// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=login_system', 'root', ''); // Modifiez selon vos infos
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Échec de la connexion à la base de données : " . $e->getMessage());
}

// Créer le répertoire 'uploads' s'il n'existe pas déjà
if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);  // Créer le répertoire avec les permissions appropriées
}

// Récupérer tous les fichiers PDF depuis la base de données
$stmt = $pdo->query("SELECT id, fichier_pdf FROM fichiers");  // Assurez-vous que 'fichier_pdf' est un BLOB
$cvs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Parcourir tous les fichiers et les enregistrer dans 'uploads/'
foreach ($cvs as $cv) {
    // Générer un nom de fichier unique pour chaque fichier PDF
    $fileName = 'cv_' . $cv['id'] . '.pdf';
    $filePath = 'uploads/' . $fileName;  // Définir le chemin où enregistrer le fichier

    // Récupérer le contenu du fichier PDF depuis la base de données
    $pdfContent = $cv['fichier_pdf'];

    // Enregistrer le fichier dans le répertoire 'uploads/'
    if (file_put_contents($filePath, $pdfContent) === false) {
        echo "Erreur lors de l'enregistrement du fichier : $fileName<br>";
    } else {
        echo "Le fichier PDF a été extrait et sauvegardé sous : $filePath<br>";
    }
}
?>
