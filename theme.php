<?php
@include 'config.php';
session_start();

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['user_email']) || $_SESSION['user_email'] != 'admin@gmail.com') {
    header('location:login.php');
    exit;
}

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = [];

// Ajouter un thème
if (isset($_POST['add_theme'])) {
    $theme_name = $_POST['theme_name'];
    $stmt = $conn->prepare("INSERT INTO theme (nom) VALUES (?)");
    $stmt->bind_param("s", $theme_name);
    if ($stmt->execute()) {
        $message[] = 'Thème ajouté avec succès.';
    } else {
        $message[] = 'Impossible d\'ajouter le thème.';
    }
}

// Modifier un thème
if (isset($_POST['update_theme'])) {
    $theme_id = intval($_POST['theme_id']);
    $new_theme_name = $_POST['new_theme_name'];

    // Mettre à jour le nom du thème
    $stmt = $conn->prepare("UPDATE theme SET nom = ? WHERE id = ?");
    $stmt->bind_param("si", $new_theme_name, $theme_id);
    if ($stmt->execute()) {
        $message[] = 'Thème mis à jour avec succès.';
    } else {
        $message[] = 'Impossible de mettre à jour le thème.';
    }
}

// Supprimer un thème
if (isset($_GET['delete'])) {
    $theme_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM theme WHERE id = ?");
    $stmt->bind_param("i", $theme_id);
    if ($stmt->execute()) {
        $message[] = 'Thème supprimé avec succès.';
    } else {
        $message[] = 'Impossible de supprimer le thème.';
    }
}
?><!doctypehtml><html lang=en><meta charset=UTF-8><meta content="IE=edge"http-equiv=X-UA-Compatible><meta content="width=device-width,initial-scale=1"name=viewport><title>Gestion des Thèmes</title><link href=https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css rel=stylesheet><link href=./assets/css/stylec.css rel=stylesheet><?php if (isset($message)): ?><?php foreach ($message as $msg): ?><div class=message><span><?= $msg; ?></span><i class="fa-times fas"onclick='this.parentElement.style.display="none"'></i></div><?php endforeach; ?><?php endif; ?><?php include 'header.php'; ?><div class=container><section><form action=""class=add-theme-form method=post><h3>Ajouter un nouveau thème</h3><input class=box name=theme_name placeholder="Nom du thème"required> <input class=btn name=add_theme type=submit value="Ajouter le thème"></form></section><section><form action=""class=update-theme-form method=post><h3>Modifier un thème</h3><select class=box name=theme_id required><option value="">Sélectionner un thème</option><?php
                $stmt = $conn->prepare("SELECT * FROM theme");
                $stmt->execute();
                $result = $stmt->get_result();
                while ($theme = $result->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($theme['id']) . "'>" . htmlspecialchars($theme['nom']) . "</option>";
                }
                ?></select> <input class=box name=new_theme_name placeholder="Nouveau nom du thème"required> <input class=btn name=update_theme type=submit value="Mettre à jour le thème"></form></section><section><h3>Liste des thèmes</h3><div class=table-responsive><table><thead><tr><th>ID<th>Nom<th>Actions<tbody><?php
                $stmt = $conn->prepare("SELECT * FROM theme");
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>" . htmlspecialchars($row['id']) . "</td>
                            <td>" . htmlspecialchars($row['nom']) . "</td>
                            <td>
                                <a href='theme.php?delete=" . $row['id'] . "' class='delete-btn' onclick=\"return confirm('Voulez-vous vraiment supprimer ce thème ?');\"><i class='fas fa-trash'></i> Supprimer</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' style='text-align: center;'>Aucun thème trouvé.</td></tr>";
                }
                ?></table></div><style>table{width:100%;border-collapse:collapse;margin-bottom:20px}table td,table th{padding:10px;text-align:left;border:1px solid #ddd}table th{background:#f4f4f4;font-weight:700}.table-responsive{overflow-x:auto;-webkit-overflow-scrolling:touch;margin-bottom:20px}.table-responsive table{min-width:600px}</style></section></div><script src=./assets/js/scriptc.js></script>