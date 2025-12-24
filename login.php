
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

// Vérifier si les données ont été envoyées via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Préparer la requête SQL pour rechercher l'utilisateur dans la base de données
    $sql = "SELECT * FROM user WHERE username = '$user' AND password = '$pass'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Si un utilisateur est trouvé
        $row = $result->fetch_assoc();
        $role = $row['role'];  // Récupère le rôle de l'utilisateur

        // Redirection en fonction du rôle
        if ($role == 'admin') {
            header("Location: admin_page.php");  // Page pour l'admin
        } elseif ($role == 'rh') {
            header("Location: rh_page.php");  // Page pour le RH
        } else {
            echo "<script>alert('Role not recognized!');</script>";
        }
    } else {
        // Si l'utilisateur n'est pas trouvé
        echo "<script>alert('Invalid username or password.');</script>";
    }
}

// Fermer la connexion
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Modal Example</title>
  <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
  <style>
    /* Inclure ici le CSS */
    * {
      box-sizing: border-box;
    }

    body {
      font-family: "Nunito", sans-serif;
      color: rgba(#000, 0.7);
    }

    .container {
      height: 200vh;
      background-image: url(https://images.unsplash.com/photo-1538137524007-21e48fa42f3f?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=ac9fa0975bd2ebad7afd906c5a3a15ab&auto=format&fit=crop&w=1834&q=80);
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
    }

    .modal {
      position: fixed;
      left: 0;
      bottom: 0;
      width: 100%;
      height: 60px;
      background: rgba(#333, 0.5);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      transition: 0.4s;
      opacity: 0; /* La modale est cachée par défaut */
      pointer-events: none; /* La modale est désactivée */
    }

    .modal-container {
      display: flex;
      max-width: 720px;
      width: 100%;
      border-radius: 10px;
      overflow: hidden;
      position: absolute;
      opacity: 0;
      pointer-events: none;
      transition-duration: 0.3s;
      background: #fff;
      transform: translateY(100px) scale(0.4);
    }

    .modal-title {
      font-size: 26px;
      margin: 0;
      font-weight: 400;
      color: #55311c;
    }

    .modal-desc {
      margin: 6px 0 30px 0;
    }

    .modal-left {
      padding: 60px 30px 20px;
      background: #fff;
      flex: 1.5;
      transition-duration: 0.5s;
      transform: translateY(80px);
      opacity: 0;
    }

    .modal-button {
      color: darken(#8c7569, 5%);
      font-family: "Nunito", sans-serif;
      font-size: 18px;
      cursor: pointer;
      border: 0;
      outline: 0;
      padding: 10px 40px;
      border-radius: 30px;
      background: rgb(255, 255, 255);
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.16);
      transition: 0.3s;
    }

    .modal-right {
      flex: 2;
      font-size: 0;
      transition: 0.3s;
      overflow: hidden;
    }

    .modal-right img {
      width: 100%;
      height: 100%;
      transform: scale(2);
      object-fit: cover;
      transition-duration: 1.2s;
    }

    .modal.is-open {
      height: 100%;
      background: rgba(#333, 0.85);
      opacity: 1; /* La modale devient visible */
      pointer-events: auto; /* La modale devient active */
    }

    .modal-container {
      opacity: 1;
      pointer-events: auto;
      transform: translateY(0) scale(1);
    }

    .modal-right img {
      transform: scale(1);
    }

    .modal-left {
      transform: translateY(0);
      opacity: 1;
      transition-delay: 0.1s;
    }

    .sign-up {
      margin: 60px 0 0;
      font-size: 14px;
      text-align: center;
    }

    .input-button {
      padding: 8px 12px;
      outline: none;
      border: 0;
      color: #fff;
      border-radius: 4px;
      background: #8c7569;
      font-family: "Nunito", sans-serif;
      transition: 0.3s;
      cursor: pointer;
    }

    .input-label {
      font-size: 11px;
      text-transform: uppercase;
      font-family: "Nunito", sans-serif;
      font-weight: 600;
      letter-spacing: 0.7px;
      color: #8c7569;
    }

    .input-block {
      display: flex;
      flex-direction: column;
      padding: 10px 10px 8px;
      border: 1px solid #ddd;
      border-radius: 4px;
      margin-bottom: 20px;
    }

    .icon-button {
      outline: 0;
      position: absolute;
      right: 10px;
      top: 12px;
      width: 32px;
      height: 32px;
      border: 0;
      background: 0;
      padding: 0;
      cursor: pointer;
    }

    .scroll-down {
      position: fixed;
      top: 50%;
      left: 50%;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      color: darken(#8c7569, 5%);
      font-size: 32px;
      font-weight: 800;
      transform: translate(-50%, -50%);
    }

    .scroll-down svg {
      margin-top: 16px;
      width: 52px;
      fill: currentColor;
    }

    @media(max-width: 750px) {
      .modal-container {
        width: 90%;
      }

      .modal-right {
        display: none;
      }
    }
  </style>
</head>

<body>
  
  <div class="scroll-down" id="scrollDown">SCROLL DOWN
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
      <path d="M16 3C8.832031 3 3 8.832031 3 16s5.832031 13 13 13 13-5.832031 13-13S23.167969 3 16 3zm0 2c6.085938 0 11 4.914063 11 11 0 6.085938-4.914062 11-11 11-6.085937 0-11-4.914062-11-11C5 9.914063 9.914063 5 16 5zm-1 4v10.28125l-4-4-1.40625 1.4375L16 23.125l6.40625-6.40625L21 15.28125l-4 4V9z"/>
    </svg>
  </div>

  <div class="container"></div>

  <div class="modal">
    <div class="modal-container">
      <div class="modal-left">
    <h1 class="modal-title">Welcome!</h1>
    <p class="modal-desc">Gestion de Recrutement</p>
    
    <!-- Formulaire de connexion -->
    <form method="POST" action="">
        <div class="input-block">
            <label for="email" class="input-label">username</label>
            <input type="text" name="username" id="email" placeholder="Email" required>
        </div>
        <div class="input-block">
            <label for="password" class="input-label">Password</label>
            <input type="password" name="password" id="password" placeholder="Password" required>
        </div>
        <div class="modal-buttons">
            <button type="submit" class="input-button">Login</button>
        </div>
    </form>
</div>

      <div class="modal-right">
        <img src="https://images.unsplash.com/photo-1512486130939-2c4f79935e4f?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=dfd2ec5a01006fd8c4d7592a381d3776&auto=format&fit=crop&w=1000&q=80" alt="">
      </div>
      <button class="icon-button close-button">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50">
          <path d="M 25 3 C 12.86158 3 3 12.86158 3 25 C 3 37.13842 12.86158 47 25 47 C 37.13842 47 47 37.13842 47 25 C 47 12.86158 37.13842 3 25 3 z M 25 5 C 36.05754 5 45 13.94246 45 25 C 45 36.05754 36.05754 45 25 45 C 13.94246 45 5 36.05754 5 25 C 5 13.94246 13.94246 5 25 5 z M 16.990234 15.990234 A 1.0001 1.0001 0 0 0 16.292969 17.707031 L 23.585938 25 L 16.292969 32.292969 A 1.0001 1.0001 0 1 0 17.707031 33.707031 L 25 26.414062 L 32.292969 33.707031 A 1.0001 1.0001 0 1 0 33.707031 32.292969 L 26.414062 25 L 33.707031 17.707031 A 1.0001 1.0001 0 0 0 32.980469 15.990234 A 1.0001 1.0001 0 0 0 32.292969 16.292969 L 25 23.585938 L 17.707031 16.292969 A 1.0001 1.0001 0 0 0 16.990234 15.990234 z"></path>
        </svg>
      </button>
    </div>
    <button class="modal-button">Click here to login</button>
  </div>

  <script>
    const body = document.querySelector("body");
    const modal = document.querySelector(".modal");
    const modalButton = document.querySelector(".modal-button");
    const closeButton = document.querySelector(".close-button");
    const scrollDown = document.querySelector(".scroll-down");
    let isOpened = false;

    // Ouvre la modale
    const openModal = () => {
      modal.classList.add("is-open");
      body.style.overflow = "hidden";
    };

    // Ferme la modale
    const closeModal = () => {
      modal.classList.remove("is-open");
      body.style.overflow = "initial";
    };

    // Affiche la modale au clic sur le bouton SCROLL DOWN
    scrollDown.addEventListener("click", () => {
      openModal();
      scrollDown.style.display = "none"; // Cache le bouton une fois que la modale est ouverte
    });

    // Ferme la modale en cliquant sur le bouton de fermeture
    closeButton.addEventListener("click", closeModal);

    // Ferme la modale avec la touche ESC
    document.onkeydown = evt => {
      evt = evt || window.event;
      evt.keyCode === 27 ? closeModal() : false;
    };
  </script>
  
</body>

</html>


<?php