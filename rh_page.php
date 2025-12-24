<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Page RH</title>
  <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
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
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
     
      background-size: cover;
      background-position: center;
      filter: blur(8px);
      z-index: -1;
    }

    .content {
      background: rgba(255, 255, 255, 0.9);
      padding: 40px 60px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
      text-align: center;
      max-width: 700px;
      width: 100%;
      z-index: 1;
    }

    .page-title {
      font-size: 36px;
      font-weight: 600;
      color: #55311c;
      margin-bottom: 20px;
    }

    .description {
      font-size: 18px;
      margin-bottom: 30px;
      color: #666;
    }
     /* 🎨 Boutons secondaires bleus */
    .choice-btn-secondary {
      display: inline-block;
      padding: 15px 30px;
      font-size: 18px;
      background-color: #020270ff;
      color: white;
      text-decoration: none;
      border-radius: 30px;
      margin: 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      min-width: 220px;
      text-align: center;
    }

    .choice-btn-secondary:hover {
      background-color: #073763;
      transform: translateY(-2px);
    }


    .choice-btn {
      display: inline-block;
      padding: 15px 30px;
      font-size: 18px;
      background-color: #b81b1bff;
      color: white;
      text-decoration: none;
      border-radius: 30px;
      margin: 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }

    .choice-btn:hover {
      background-color: #ab4444ff;
      transform: translateY(-2px);
    }

    .choice-btn:focus {
      outline: none;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }

   .additional-choices {
  display: flex;
  flex-direction: row; /* ✅ Horizontal */
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  gap: 15px; /* ✅ Espace entre les boutons */
  margin-top: 20px;
  opacity: 0;
  position: absolute;
  left: 50%;
  transform: translateX(-50%) translateY(-10px);
  transition: transform 0.5s ease, opacity 0.5s ease;
}

.additional-choices.fiches,
.additional-choices.cv {
  transform: translateX(-50%) translateY(-10px);
}

    .additional-choices.show {
      opacity: 1;
      transform: translateX(-50%) translateY(0);
      position: relative;
    }

 .row {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-wrap: nowrap; /* ❗ empêche les retours à la ligne */
  gap: 20px; /* espace horizontal entre les boutons */
  margin-bottom: 30px;
}



    @media (max-width: 750px) {
      .content {
        width: 90%;
        padding: 30px 40px;
      }

      .page-title {
        font-size: 28px;
      }

      .choice-btn {
        font-size: 16px;
        padding: 12px 25px;
      }
    }
  </style>
</head>
<body>

  <div class="container"></div>

  <div class="content">
    <h1 class="page-title">Page du Responsable RH</h1>
    <p class="description">Bienvenue sur la page du Responsable RH. Choisissez l'une des options ci-dessous pour commencer.</p>

    <!-- Ligne unique : tous les boutons côte à côte -->
<div class="row">
  <a href="javascript:void(0)" class="choice-btn" id="gestionFichesBtn">Gestion des Fiches de Poste</a>
  <a href="javascript:void(0)" class="choice-btn" id="gestionCvBtn">Gestion des CV</a>
  <a href="index.php" class="choice-btn">Matching FP/CV</a>
  
</div>
<a href="login.php" class="return-link">⬅ Log out </a>

 <div id="gestionFichesChoices" class="additional-choices fiches">
      <a href="ajouter_fiche.php" class="choice-btn-secondary">Ajouter une Fiche de Poste</a>
      <a href="modifier_fiche.php" class="choice-btn-secondary">Modifier une Fiche de Poste</a>
      <a href="supprimer_fiche.php" class="choice-btn-secondary">Supprimer une Fiche de Poste</a>
      <a href="consulteFP.php" class="choice-btn-secondary">Consulter les Fiches de Poste</a>
    </div>

    <!-- Sous-options CV -->
    <div id="gestionCvChoices" class="additional-choices cv">
      <a href="ajouterpdf.php" class="choice-btn-secondary">Ajouter un CV</a>
      <a href="consultercv.php" class="choice-btn-secondary">Consulter un CV</a>
    </div>
  </div>
  <script>
    // Toggle fiches
    document.getElementById('gestionFichesBtn').addEventListener('click', function () {
      const fichesChoices = document.getElementById('gestionFichesChoices');
      fichesChoices.classList.toggle('show');
      document.getElementById('gestionCvChoices').classList.remove('show');
    });

    // Toggle CV
    document.getElementById('gestionCvBtn').addEventListener('click', function () {
      const cvChoices = document.getElementById('gestionCvChoices');
      cvChoices.classList.toggle('show');
      document.getElementById('gestionFichesChoices').classList.remove('show');
    });
  </script>

</body>
</html>
