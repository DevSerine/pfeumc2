<?php
include 'config.php';

if (isset($_POST['submit'])) {

   $nom = $_POST['name'];
   $email = $_POST['email'];
   $pass = md5($_POST['password']);
   $cpass = md5($_POST['cpassword']);
   $numero_tel = $_POST['telephone'];
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/' . $image;
   $role = $_POST['role'];
   $formation_id = isset($_POST['formation']) ? intval($_POST['formation']) : NULL;
   $cv = isset($_FILES['cv']['name']) ? $_FILES['cv']['name'] : '';
   $cv_tmp_name = isset($_FILES['cv']['tmp_name']) ? $_FILES['cv']['tmp_name'] : '';
   $cv_folder = 'uploaded_img/' . $cv;

   // Vérifie si l'utilisateur existe déjà
   $stmt = $conn->prepare("SELECT * FROM `utilisateur` WHERE email = ?");
   $stmt->bind_param("s", $email);
   $stmt->execute();
   $result = $stmt->get_result();

   if ($result->num_rows > 0) {
      $message[] = 'Cet utilisateur existe déjà !';
   } else {
      if ($pass != $cpass) {
         $message[] = 'Les mots de passe ne correspondent pas !';
      } elseif ($image_size > 2000000) {
         $message[] = 'La taille de l\'image est trop grande !';
      } else {
         if ($email == 'admin@gmail.com') {
            $role = 'admin';
         }

         // Insertion des données dans la table `utilisateur`
         $stmt = $conn->prepare("INSERT INTO `utilisateur`(nom, email, mot_de_passe, numero_tel, image, type_utilisateur, formation_id, cv) VALUES(?, ?, ?, ?, ?, ?, ?, ?)");
         $stmt->bind_param("ssssssis", $nom, $email, $pass, $numero_tel, $image, $role, $formation_id, $cv);
         $insert = $stmt->execute();

         if ($insert) {
            move_uploaded_file($image_tmp_name, $image_folder);
            if ($role === 'formateur') {
               move_uploaded_file($cv_tmp_name, $cv_folder);
            }
            $message[] = 'Bienvenue à UMC2, vous allez recevoir une réponse dans les délais les plus courts...';
            foreach ($message as $msg) {
               echo '<div style=" text-align: center;
         font-size: 30px;
         color: green;
         margin-top: 20px;
         position: fixed;
         top: 50%;
         left: 50%;
         transform: translate(-50%, -50%);
         background-color: #f0f0f0;
         padding: 20px;
         border-radius: 10px;
         box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);"  class="messages"><span class="close-btn" onclick="this.parentElement.style.display=\'none\';">&times;</span>' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</div>';
            }
            header("Refresh:5; url=login.php");
            exit;
         } else {
            $message[] = 'Échec de l\'inscription !';
         }
      }
   }
}
?><!doctypehtml>
   <html lang=fr>
   <meta charset=UTF-8>
   <meta content="IE=edge" http-equiv=X-UA-Compatible>
   <meta content="width=device-width,initial-scale=1" name=viewport>
   <title>Inscription</title>
   <link href=./assets/css/stylel.css rel=stylesheet>
   <div class=form-container>
      <form action="" enctype=multipart/form-data id=registerForm method=post>
         <h3>Inscrivez-vous maintenant</h3><?php
         // Affichage des messages de succès ou d'erreur
         if (isset($message)) {
            foreach ($message as $msg) {
               echo '<div class="message"><span class="close-btn" onclick="this.parentElement.style.display=\'none\';">&times;</span>' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</div>';
            }
         }
         ?><input class=box name=name required placeholder="Entrez votre nom"> <input class=box name=email required
            placeholder="Entrez votre email" type=email> <input class=box name=password required
            placeholder="Entrez votre mot de passe" type=password> <input class=box name=cpassword required
            placeholder="Confirmez votre mot de passe" type=password> <input class=box name=telephone required
            placeholder="Entrez votre numéro de téléphone"> <label for=image>Télécharger votre photo :</label> <input
            class=box name=image required type=file accept="image/jpg, image/jpeg, image/png, image/svg+xml"> <select
            class=box id=roleSelect name=role onchange=handleRoleChange() required>
            <option value="">Sélectionner un rôle
            <option value=entreprise>Entreprise
            <option value=etudiant>Étudiant
            <option value=formateur>Formateur
            <option value=interne>Interne
         </select>
         <div class=hidden id=formationSelect><select class=box id=formation name=formation>
               <option value="">Sélectionner une formation</option><?php
               $stmt = $conn->prepare("SELECT * FROM formation");
               $stmt->execute();
               $result = $stmt->get_result();
               while ($formation = $result->fetch_assoc()) {
                  echo '<option value="' . htmlspecialchars($formation['id']) . '">' . htmlspecialchars($formation['nom']) . '</option>';
               }
               ?>
            </select></div>
         <div class=hidden id=cvInput><label for=cv>Télécharger votre CV :</label> <input class=box name=cv type=file
               accept=application/pdf id=cv></div><input class=btn name=submit type=submit value="S'inscrire">
         <p>Vous avez déjà un compte ? <a href=login.php>Connectez-vous</a>
      </form>
   </div>
   <script>
      function handleRoleChange() {
         var role = document.getElementById("roleSelect").value;
         var formationSelect = document.getElementById("formationSelect");
         var cvInput = document.getElementById("cvInput");
         var formation = document.getElementById("formation");
         var cv = document.getElementById("cv");

         formationSelect.classList.add("hidden");
         cvInput.classList.add("hidden");
         formation.classList.add("hidden");
         cv.classList.add("hidden");

         formation.removeAttribute("required");
         cv.removeAttribute("required");

         if (role === "etudiant") {
            formationSelect.classList.remove("hidden");
            formation.classList.remove("hidden");
            formation.setAttribute("required", "required");
         } else if (role === "formateur") {
            cvInput.classList.remove("hidden");
            cv.classList.remove("hidden");
            cv.setAttribute("required", "required");
         } else if (role === "entreprise") {
            formationSelect.classList.remove("hidden");
            formation.classList.remove("hidden");
            formation.setAttribute("required", "required");
         }
      }

      document.addEventListener("DOMContentLoaded", function () {
         handleRoleChange();
      });
   </script>
   <style>
      .hidden {
         display: none
      }

      .message {
         text-align: center;
         font-size: 24px;
         color: green;
         margin-top: 20px;
         position: fixed;
         top: 50%;
         left: 50%;
         transform: translate(-50%, -50%);
         background-color: #f0f0f0;
         padding: 20px;
         border-radius: 10px;
         box-shadow: 0 0 10px rgba(0, 0, 0, .1)
      }

      .close-btn {
         position: absolute;
         top: 10px;
         right: 10px;
         font-size: 20px;
         cursor: pointer
      }
   </style>