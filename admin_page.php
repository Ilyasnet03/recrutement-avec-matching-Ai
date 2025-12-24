<?php
require 'vendor/autoload.php';

try {
    $pdo = new PDO('mysql:host=localhost;dbname=login_system', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$view = $_GET['view'] ?? 'home';

// Suppression utilisateur
if (isset($_GET['delete_user'])) {
    $delete_id = intval($_GET['delete_user']);
    $stmt = $pdo->prepare("DELETE FROM user WHERE id = ?");
    $stmt->execute([$delete_id]);
    header("Location: ?view=user_list&deleted=1");
    exit;
}

// Ajout utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    $stmt = $pdo->prepare("INSERT INTO user (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $role]);
    header("Location: ?view=users&success=1");
    exit;
}

$nb_fiches = $pdo->query("SELECT COUNT(*) FROM fichedeposte")->fetchColumn();
$nb_cvs = $pdo->query("SELECT COUNT(*) FROM fichiers")->fetchColumn();
$nb_matches = $pdo->query("SELECT COUNT(*) FROM resultats_matching")->fetchColumn();
$taux_match = $pdo->query("SELECT ROUND(AVG(score)*100, 1) FROM resultats_matching")->fetchColumn();
$fiches = $pdo->query("SELECT * FROM fichedeposte")->fetchAll(PDO::FETCH_ASSOC);
$utilisateurs = $pdo->query("SELECT * FROM user")->fetchAll(PDO::FETCH_ASSOC);
$matchings = $pdo->query("SELECT * FROM resultats_matching ORDER BY fiche_id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            max-width: 1000px;
            width: 100%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
         h2 {
            text-align: center;
            color: #55311c;
        }
        .card-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 30px;
        }
        .card {
            background: #fdfdfd;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            text-decoration: none;
            color: #333;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            min-width: 200px;
        }
        .card:hover {
            background: #c50000ff;
            color: white;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 400px;
            margin: 0 auto;
            margin-top: 30px;
        }
        input, select, button {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            background: #ff0000ff;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #ee0909ff;
        }
        .fiche, .match-item, .user-item {
            margin-bottom: 20px;
            padding: 15px;
            border-left: 4px solid #ff0000ff;
            background: #f9f9f9;
            border-radius: 8px;
        }
        .user-item a {
            float: right;
            color: red;
            text-decoration: none;
        }
        .return-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            color: #55311c;
            text-decoration: none;
        }
        canvas {
            margin: 30px auto;
            display: block;
            max-width: 600px;
        }
        .admin-title {
             text-align: center;
  margin-left:250px;
  margin-right: 250px;
  text-align: center;
  font-family: 'Nunito', sans-serif;
  font-size: 42px;
  font-weight: 600;
  color: #fff;
  background: linear-gradient(90deg, #ff0400ff, #0918eeff);
  padding: 15px 30px;
  border-radius: 12px;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
  
  animation: fadeInTitle 1.2s ease-out forwards;
  transform: translateY(-10px);
  opacity: 0;
}

@keyframes fadeInTitle {
  0% {
    opacity: 0;
    transform: translateY(-20px) scale(0.95);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

    </style>
</head>
<body>
<div class="container" data-aos="fade-up">
    <?php if ($view === 'home'): ?>
       <h1 class="admin-title">Bienvenue Administrateur</h1>

        <p style="text-align:center">Choisissez une action :</p>
        <div class="card-grid">
            <a href="?view=users" class="card">👤 Ajouter un utilisateur</a>
            <a href="?view=user_list" class="card">📋 Liste des utilisateurs</a>
            <a href="?view=stats" class="card">📊 Vue d'ensemble</a>
            <a href="?view=fiches" class="card">📋 Fiches de poste</a>
            <a href="?view=matchings" class="card">🕓 Historique des matchings</a>
        </div>
        <a href="login.php" class="return-link">⬅ Log out </a>

    <?php elseif ($view === 'users'): ?>
        <h2>Ajouter un utilisateur</h2>
        <?php if (isset($_GET['success'])): ?><p style="color: green; text-align:center;">Utilisateur ajouté avec succès !</p><?php endif; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <select name="role" required>
                <option value="admin">Admin</option>
                <option value="rh">RH</option>
            </select>
            <button type="submit" name="add_user">Ajouter</button>
        </form>
        <a href="?view=home" class="return-link">⬅ Retour</a>

    <?php elseif ($view === 'user_list'): ?>
        <h2>Liste des utilisateurs</h2>
        <?php if (isset($_GET['deleted'])): ?><p style="color: green; text-align:center;">Utilisateur supprimé avec succès.</p><?php endif; ?>
        <?php foreach ($utilisateurs as $user): ?>
            <div class="user-item">
                <strong>Nom :</strong> <?= htmlspecialchars($user['username']) ?><br>
                <strong>Rôle :</strong> <?= htmlspecialchars($user['role']) ?>
                <a href="?view=user_list&delete_user=<?= $user['id'] ?>" onclick="return confirm('Supprimer cet utilisateur ?')">
                    Supprimer
                </a>
            </div>
        <?php endforeach; ?>
        <a href="?view=home" class="return-link">⬅ Retour</a>

    <?php elseif ($view === 'stats'): ?>
        <h2>Statistiques de recrutement</h2>
        <canvas id="barChart"></canvas>
        <canvas id="donutChart"></canvas>
        <a href="?view=home" class="return-link">⬅ Retour</a>
        <script>
            const barCtx = document.getElementById('barChart').getContext('2d');
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: ['Fiches de poste', 'CV ajoutés', 'Matchings'],
                    datasets: [{
                        label: 'Volume',
                        data: [<?= $nb_fiches ?>, <?= $nb_cvs ?>, <?= $nb_matches ?>],
                        backgroundColor: ['#02aebeff', '#5e05f9ff', '#ff00ccff']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
            const donutCtx = document.getElementById('donutChart').getContext('2d');
            new Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Matching %', 'Non-matching %'],
                    datasets: [{
                        data: [<?= $taux_match ?>, <?= 100 - $taux_match ?>],
                        backgroundColor: ['#06d10dff', '#eb0404ff']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        </script>

    <?php elseif ($view === 'fiches'): ?>
        <h2>Fiches de poste</h2>
        <?php foreach ($fiches as $fiche): ?>
            <div class="fiche" data-aos="fade-up">
                <strong><?= htmlspecialchars($fiche['Titre']) ?></strong><br>
                Compétences : <?= htmlspecialchars($fiche['Compétence']) ?><br>
                Responsabilités : <?= htmlspecialchars($fiche['Responsabilité']) ?><br>
                <a href="fiche_candidats.php?fiche_id=<?= $fiche['ID'] ?>">Voir les meilleurs candidats</a>
            </div>
        <?php endforeach; ?>
        <a href="?view=home" class="return-link">⬅ Retour</a>

    <?php elseif ($view === 'matchings'): ?>
        <h2>Historique des matchings</h2>
        <?php foreach ($matchings as $match): ?>
            <div class="match-item">
                <strong>Fiche :</strong> <?= htmlspecialchars($match['fiche_titre']) ?><br>
                <strong>CV :</strong> <?= htmlspecialchars($match['nom_cv']) ?><br>
                <strong>Score :</strong> <?= round($match['score'], 3) ?>
            </div>
        <?php endforeach; ?>
        <a href="?view=home" class="return-link">⬅ Retour</a>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>AOS.init();</script>
</body>
</html>