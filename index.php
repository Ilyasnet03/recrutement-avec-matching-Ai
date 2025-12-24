<?php
set_time_limit(1000);
require 'vendor/autoload.php';

try {
    $pdo = new PDO('mysql:host=localhost;dbname=login_system', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connexion échouée : " . $e->getMessage());
}

$top_cvs = [];
$fiche_info = '';

function extractPdfText($filePath) {
    if (!file_exists($filePath)) return '';
    $parser = new \Smalot\PdfParser\Parser();
    $pdf = $parser->parseFile($filePath);
    return $pdf->getText();
}

function queryHuggingFaceSimilarity($text1, $text2) {
    $temp1 = tempnam(sys_get_temp_dir(), 'fiche_') . '.txt';
    $temp2 = tempnam(sys_get_temp_dir(), 'cv_') . '.txt';
    file_put_contents($temp1, $text1);
    file_put_contents($temp2, $text2);

    $command = "C:\\xampp\\htdocs\\recrutement\\venv\\Scripts\\python.exe embed.py " . escapeshellarg($temp1) . " " . escapeshellarg($temp2);
    $output = shell_exec($command . " 2>&1");

    unlink($temp1);
    unlink($temp2);

    $result = json_decode($output, true);
    return isset($result['score']) ? $result['score'] : 0;
}

$stmt = $pdo->query("SELECT id, titre, description, Compétence, Responsabilité, Localisation FROM fichedeposte");
$fiches = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['fiche_id'])) {
    $fiche_id = intval($_POST['fiche_id']);
    $stmt = $pdo->prepare("SELECT * FROM fichedeposte WHERE id = ?");
    $stmt->execute([$fiche_id]);
    $fiche = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($fiche) {
        $fiche_info = implode(' ', array_values($fiche));

        $stmt = $pdo->query("SELECT id, titre, fichier_pdf FROM fichiers");
        $cvs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($cvs as $cv) {
            $filename = "temp_" . $cv['id'] . ".pdf";
            $filepath = __DIR__ . "/temp_cvs/" . $filename;

            if (!is_dir(__DIR__ . "/temp_cvs")) {
                mkdir(__DIR__ . "/temp_cvs", 0777, true);
            }

            file_put_contents($filepath, $cv['fichier_pdf']);
            $cv_text = extractPdfText($filepath);
            if (empty(trim($cv_text))) continue;

            $score = queryHuggingFaceSimilarity($fiche_info, $cv_text);

            $top_cvs[] = [
                'id' => $cv['id'],
                'titre' => $cv['titre'],
                'filepath' => $filepath,
                'score' => $score
            ];
        }

        usort($top_cvs, fn($a, $b) => $b['score'] <=> $a['score']);
        $top_cvs = array_slice($top_cvs, 0, 3);

        // ✅ Enregistrer uniquement le meilleur si non déjà présent
        $best_cv = $top_cvs[0] ?? null;
        if ($best_cv) {
            $fiche_titre = $fiche['Titre'] ?? 'Titre inconnu';
            $check = $pdo->prepare("SELECT COUNT(*) FROM resultats_matching WHERE fiche_id = ? AND nom_cv = ?");
            $check->execute([$fiche_id, $best_cv['titre']]);
            $exists = $check->fetchColumn();

            if (!$exists) {
                $insert = $pdo->prepare("INSERT INTO resultats_matching (fiche_id, fiche_titre, nom_cv, score) VALUES (?, ?, ?, ?)");
                $insert->execute([$fiche_id, $fiche_titre, $best_cv['titre'], $best_cv['score']]);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Matching Fiche / CV</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
             body {
  font-family: "Nunito", sans-serif;
  margin: 0;
  padding: 40px;
  min-height: 100vh;
  background-image: url('image/fond.png'); /* chemin vers ton image */
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  background-attachment: fixed;
  display: flex;
  justify-content: center;
  align-items: flex-start;
}
        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px 60px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 800px;
        }
        h1 {
            font-size: 32px;
            font-weight: 700;
            color: #55311c;
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: 600;
            margin-top: 20px;
        }
        select, button {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            border-radius: 6px;
            border: 2px solid #ccc;
            margin-top: 8px;
        }
        button {
            background-color: #ff0000ff;
            color: white;
            border: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        button:hover {
            background-color: #ee0909ff;
            transform: scale(1.05);
        }
        .result-container {
            margin-top: 30px;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .cv-item {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ccc;
        }
        .iframe-container {
            display: none;
            margin-top: 10px;
        }
        iframe {
            width: 100%;
            height: 500px;
            border: none;
        }
        #loader {
            display: none;
            margin-top: 20px;
            text-align: center;
        }
        .spinner {
            border: 6px solid #f3f3f3;
            border-top: 6px solid #ff0000ff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        #loading-text {
            color: #ff0000ff;
            margin-top: 10px;
            font-weight: bold;
        }
        .back-button {
            display: inline-block;
            margin-top: 40px;
            background-color: rgb(167, 11, 11);
            color: white;
            padding: 15px 30px;
            border-radius: 30px;
            text-decoration: none;
            text-align: center;
        }
        .back-button:hover {
            background-color: rgb(188, 34, 34);
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Comparer une Fiche de Poste avec les CV</h1>

    <form method="post">
        <label for="fiche_id">Choisissez une fiche de poste :</label>
        <select name="fiche_id" id="fiche_id" required>
            <?php foreach ($fiches as $fiche_item): ?>
    <option value="<?= htmlspecialchars($fiche_item['id']) ?>" <?= (isset($_POST['fiche_id']) && $_POST['fiche_id'] == $fiche_item['id']) ? 'selected' : '' ?>>
        <?= htmlspecialchars($fiche_item['id']) ?> - <?= htmlspecialchars($fiche_item['titre'] ?? 'Sans titre') ?>
    </option>
<?php endforeach; ?>
        </select>

        <button type="submit">Lancer la comparaison</button>

        <div id="loader">
            <div class="spinner"></div>
            <div id="loading-text">Traitement en cours... Veuillez patienter</div>
        </div>
    </form>

    <?php if (isset($_POST['fiche_id']) && $fiche): ?>
        <div class="result-container">
            <h2>Fiche sélectionnée :</h2>
            <ul>
                <?php foreach ($fiche as $key => $value): ?>
                    <li><strong><?= htmlspecialchars($key) ?> :</strong> <?= htmlspecialchars($value) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($top_cvs)): ?>
        <div class="result-container">
            <h2>Top 3 des CV correspondants :</h2>
            <?php foreach ($top_cvs as $i => $cv): ?>
                <div class="cv-item">
                    <p><strong>CV #<?= $i + 1 ?> :</strong> <?= htmlspecialchars($cv['titre']) ?></p>
                    <p><strong>Score :</strong> <?= round($cv['score'], 3) ?></p>
                    <button onclick="document.getElementById('cvIframe<?= $i ?>').style.display='block';">Afficher ce CV</button>
                    <div id="cvIframe<?= $i ?>" class="iframe-container">
                        <iframe src="<?= htmlspecialchars('temp_cvs/' . basename($cv['filepath'])) ?>"></iframe>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- 🔙 Bouton retour -->
    <div style="text-align: center;">
        <a href="rh_page.php" class="back-button">Retour</a>
    </div>
</div>

<script>
    document.querySelector("form").addEventListener("submit", () => {
        document.getElementById("loader").style.display = "block";
    });
</script>

</body>
</html>
