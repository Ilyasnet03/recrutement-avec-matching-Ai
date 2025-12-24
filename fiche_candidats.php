<?php
require 'vendor/autoload.php';

try {
    $pdo = new PDO('mysql:host=localhost;dbname=login_system', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

$fiche_id = $_GET['fiche_id'] ?? null;

if ($fiche_id) {
    $stmt = $pdo->prepare("SELECT nom_cv, score FROM resultats_matching WHERE fiche_id = ? ORDER BY score DESC");
    $stmt->execute([$fiche_id]);
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("SELECT id, titre FROM fichiers");
    $cvFichiers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $cv_map = [];
    foreach ($cvFichiers as $cv) {
        $cv_map[$cv['titre']] = 'temp_cvs/temp_' . $cv['id'] . '.pdf';
    }
} else {
    die("ID de fiche manquant.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Meilleurs candidats</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(45deg, #ff6a00, #ee0979);
            margin: 0;
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }
        .container {
            background: #fff;
            padding: 40px;
            max-width: 900px;
            width: 100%;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        h1 {
            text-align: center;
            color: #ff6a00;
            font-weight: 800;
            margin-bottom: 30px;
        }
        .item {
            margin-bottom: 25px;
            background: #f9f9f9;
            border-left: 5px solid #ff6a00;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        .item:hover {
            transform: translateY(-3px);
        }
        .iframe-container {
            margin-top: 15px;
            display: none;
        }
        iframe {
            width: 100%;
            height: 500px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .btn {
            margin-top: 10px;
            background-color: #ff6a00;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background-color: #ee0979;
        }
        .back {
            display: block;
            margin-top: 40px;
            text-align: center;
            font-weight: 600;
            color: #ff6a00;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .back:hover {
            color: #ee0979;
        }
    </style>
    <script>
        function toggleIframe(id) {
            const container = document.getElementById('cv_' + id);
            container.style.display = container.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>
<body>
<div class="container" data-aos="fade-up">
    <h1>🧠 Meilleurs Candidats</h1>

    <?php if (!empty($resultats)): ?>
        <?php foreach ($resultats as $index => $cv): ?>
            <div class="item" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                <strong>📄 CV :</strong> <?= htmlspecialchars($cv['nom_cv']) ?><br>
                <strong>🎯 Score :</strong> <?= round($cv['score'], 3) ?> %

                <?php if (isset($cv_map[$cv['nom_cv']]) && file_exists($cv_map[$cv['nom_cv']])): ?>
                    <button class="btn" onclick="toggleIframe(<?= $index ?>)">Afficher le CV</button>
                    <div class="iframe-container" id="cv_<?= $index ?>">
                        <iframe src="<?= htmlspecialchars($cv_map[$cv['nom_cv']]) ?>"></iframe>
                    </div>
                <?php else: ?>
                    <p style="color:red; margin-top: 10px;">⚠️ CV introuvable dans le dossier.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align:center; font-weight:600;">Aucun candidat trouvé pour cette fiche.</p>
    <?php endif; ?>

   <a href="admin_page.php?view=fiches" class="return-link">⬅ Retour aux fiches de poste</a>

</div>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>AOS.init();</script>
</body>
</html>
