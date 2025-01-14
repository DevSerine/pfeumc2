<?php

include 'config.php';
session_start();

if (isset($_POST['submit'])) {

   $email = $_POST['email'];
   $pass = md5($_POST['password']);

   // Préparer la requête pour utiliser la table utilisateur
   $stmt = mysqli_prepare($conn, "SELECT * FROM `utilisateur` WHERE email = ? AND mot_de_passe = ?");
   mysqli_stmt_bind_param($stmt, "ss", $email, $pass);
   mysqli_stmt_execute($stmt);
   $result = mysqli_stmt_get_result($stmt);

   if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
      $_SESSION['user_id'] = $row['id'];
      $_SESSION['user_email'] = $row['email'];  // Enregistrez l'email de l'utilisateur dans la session

      // Vérifiez si l'email est celui de l'admin
      if ($row['email'] == 'admin@gmail.com') {
         // Si l'email est celui de l'admin, redirigez-le vers la page admin
         header('location:admin.php');
      } else {
         // Sinon, redirigez vers la page d'accueil
         header('location:index.php');
      }
      exit;
   } else {
      $message[] = 'Email ou mot de passe incorrect !';
   }
}

?><!doctypehtml><html lang=fr><meta charset=UTF-8><meta content="IE=edge"http-equiv=X-UA-Compatible><meta content="width=device-width,initial-scale=1"name=viewport><title>Connexion</title><link href=./assets/css/stylel.css rel=stylesheet><div class=form-container><form action enctype=multipart/form-data method=post><h3>Connexion</h3><?php
      if (isset($message)) {
         foreach ($message as $message) {
            echo '<div class="message">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</div>';
         }
      }
      ?><input class=box name=email type=email placeholder="Entrez votre email"required> <input class=box name=password type=password placeholder="Entrez votre mot de passe"required> <input class=btn name=submit type=submit value="Se connecter"><p>Pas encore de compte ? <a href=register.php>Inscrivez-vous maintenant</a></form></div>
