<?php
include 'config.php';
session_start();

// Vérifiez que l'ID de l'utilisateur est bien défini dans la session
if (!isset($_SESSION['user_id'])) {
    die('Utilisateur non connecté.'); // L'utilisateur n'est pas connecté
}

$user_id = $_SESSION['user_id'];

// Vérifiez si l'utilisateur existe dans la base de données avec cet ID
$stmt = $conn->prepare("SELECT * FROM utilisateur WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $fetch = $result->fetch_assoc();
} else {
    die('Utilisateur introuvable.');
}

// Traitement de la mise à jour du profil
if (isset($_POST['update_profile'])) {
    $message = [];
    $is_updated = false;

    // Mise à jour du nom, email et numéro de téléphone
    if (!empty($_POST['update_name']) && $_POST['update_name'] !== $fetch['nom']) {
        $update_nom = $_POST['update_name'];
        $stmt = $conn->prepare("UPDATE utilisateur SET nom = ? WHERE id = ?");
        $stmt->bind_param("si", $update_nom, $user_id);
        $stmt->execute();
        $message[] = 'Nom mis à jour avec succès !';
        $is_updated = true;
    }

    if (!empty($_POST['update_email']) && $_POST['update_email'] !== $fetch['email']) {
        $update_email = $_POST['update_email'];
        $stmt = $conn->prepare("UPDATE utilisateur SET email = ? WHERE id = ?");
        $stmt->bind_param("si", $update_email, $user_id);
        $stmt->execute();
        $message[] = 'Email mis à jour avec succès !';
        $is_updated = true;
    }

    if (!empty($_POST['update_num_tel']) && $_POST['update_num_tel'] !== $fetch['numero_tel']) {
        $update_num_tel = $_POST['update_num_tel'];
        $stmt = $conn->prepare("UPDATE utilisateur SET numero_tel = ? WHERE id = ?");
        $stmt->bind_param("si", $update_num_tel, $user_id);
        $stmt->execute();
        $message[] = 'Numéro de téléphone mis à jour avec succès !';
        $is_updated = true;
    }

    // Vérification et mise à jour des champs de mot de passe
    $update_pass = !empty($_POST['update_pass']) ? md5($_POST['update_pass']) : '';
    $new_pass = !empty($_POST['new_pass']) ? md5($_POST['new_pass']) : '';
    $confirm_pass = !empty($_POST['confirm_pass']) ? md5($_POST['confirm_pass']) : '';

    if (!empty($update_pass) || !empty($new_pass) || !empty($confirm_pass)) {
        if (empty($update_pass) || empty($new_pass) || empty($confirm_pass)) {
            $message[] = 'Tous les champs liés au mot de passe doivent être remplis.';
        } elseif ($update_pass !== $fetch['mot_de_passe']) {
            $message[] = 'L\'ancien mot de passe ne correspond pas !';
        } elseif ($new_pass !== $confirm_pass) {
            $message[] = 'Les nouveaux mots de passe ne correspondent pas !';
        } else {
            // Mise à jour du mot de passe
            $stmt = $conn->prepare("UPDATE utilisateur SET mot_de_passe = ? WHERE id = ?");
            $stmt->bind_param("si", $new_pass, $user_id);
            $stmt->execute();
            $message[] = 'Mot de passe mis à jour avec succès !';
            $is_updated = true;
        }
    }

    // Gestion de l'image
    if (!empty($_FILES['update_image']['name'])) {
        $update_image = $_FILES['update_image']['name'];
        $update_image_size = $_FILES['update_image']['size'];
        $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
        $update_image_folder = 'uploaded_img/' . $update_image;

        if ($update_image_size > 2000000) {
            $message[] = 'La taille de l\'image est trop grande !';
        } else {
            $stmt = $conn->prepare("UPDATE utilisateur SET image = ? WHERE id = ?");
            $stmt->bind_param("si", $update_image, $user_id);
            $stmt->execute();

            // Déplacement de l'image téléchargée
            move_uploaded_file($update_image_tmp_name, $update_image_folder);
            $message[] = 'Image mise à jour avec succès !';
            $is_updated = true;
        }
    }

    if (!$is_updated) {
        $message[] = 'Aucune modification détectée.';
    }

    // Affichage des messages
    if (!empty($message)) {
        foreach ($message as $msg) {
            echo '<div class="message">' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</div>';
        }
    }
}
?><!doctypehtml><html lang=fr><meta charset=UTF-8><meta content="IE=edge"http-equiv=X-UA-Compatible><meta content="width=device-width,initial-scale=1"name=viewport><title>Mettre à jour le profil</title><link href=./assets/css/stylel.css rel=stylesheet><div class=update-profile><form action=""enctype=multipart/form-data method=post><?php
        if (empty($fetch['image'])) {
            echo '<img src="images/default-avatar.png">';
        } else {
            echo '<img src="uploaded_img/' . $fetch['image'] . '">';
        }
        ?><div class=flex><div class=inputBox><span>Nom d'utilisateur :</span> <input class=box name=update_name value="<?php echo htmlspecialchars($fetch['nom'], ENT_QUOTES, 'UTF-8'); ?>"required> <span>Votre email :</span> <input class=box name=update_email type=email value="<?php echo htmlspecialchars($fetch['email'], ENT_QUOTES, 'UTF-8'); ?>"required> <span>Numéro de téléphone :</span> <input class=box name=update_num_tel value="<?php echo htmlspecialchars($fetch['numero_tel'], ENT_QUOTES, 'UTF-8'); ?>"required> <span>Mettre à jour votre photo :</span> <input class=box name=update_image type=file accept="image/jpg, image/jpeg, image/png, image/svg+xml"></div><div class=inputBox><span>Ancien mot de passe :</span> <input class=box name=update_pass type=password placeholder="Entrez votre ancien mot de passe"> <span>Nouveau mot de passe :</span> <input class=box name=new_pass type=password placeholder="Entrez un nouveau mot de passe"> <span>Confirmez le mot de passe :</span> <input class=box name=confirm_pass type=password placeholder="Confirmez le nouveau mot de passe"></div></div><input class=btn name=update_profile type=submit value="Mettre à jour le profil"> <a class=delete-btn href=index.php>Retour</a></form></div>