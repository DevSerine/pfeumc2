<?php

include 'config.php';
session_start();

if (isset($_POST['submit'])) {

   $email = $_POST['email'];
   $pass = $_POST['password'];

   // Adapter la requête pour utiliser la table utilisateur
   $stmt = $conn->prepare("SELECT * FROM `utilisateur` WHERE email = ?");
   $stmt->bind_param("s", $email);
   $stmt->execute();
   $result = $stmt->get_result();

   if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      if (password_verify($pass, $row['mot_de_passe'])) {
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
   } else {
      $message[] = 'Email ou mot de passe incorrect !';
   }
}

?><!doctypehtml>
   <html lang=fr>
   <meta charset=UTF-8>
   <meta content="IE=edge" http-equiv=X-UA-Compatible>
   <meta content="width=device-width,initial-scale=1" name=viewport>
   <title>Connexion</title>
   <link href=./assets/css/stylel.css rel=stylesheet>
   <div class=form-container>
      <form action enctype=multipart/form-data method=post>
         <h3>Connexion</h3><?php
         if (isset($message)) {
            foreach ($message as $message) {
               echo '<div class="message">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</div>';
            }
         }
         ?><input class=box name=email type=email placeholder="Entrez votre email" required> <input class=box
            name=password type=password placeholder="Entrez votre mot de passe" required> <input class=btn name=submit
            type=submit value="Se connecter">
         <p>Pas encore de compte ? <a href=register.php>Inscrivez-vous maintenant</a>
      </form>
   </div>