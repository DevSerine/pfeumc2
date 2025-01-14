<?php
@include 'config.php';

session_start();
session_regenerate_id(true); // Régénérer l'ID de session pour éviter les attaques de fixation

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['user_email']) || $_SESSION['user_email'] != 'admin@gmail.com') {
    header('location:login.php');
    exit;
}

// Fonction pour valider les dates
function validateDates($date_debut, $date_fin) {
    if (strtotime($date_debut) > strtotime($date_fin)) {
        return "La date de début doit être antérieure à la date de fin.";
    }
    return null;
}

// Fonction pour valider les fichiers uploadés
function validateFile($file) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
    if (!in_array($file['type'], $allowed_types)) {
        return "Type de fichier non autorisé. Seuls les fichiers JPEG, PNG, GIF et SVG sont acceptés.";
    }
    if ($file['size'] > 5 * 1024 * 1024) { // 5 Mo maximum
        return "Le fichier est trop volumineux. La taille maximale autorisée est de 5 Mo.";
    }
    return null;
}

// Récupérer les thèmes dynamiquement
$stmt = $conn->prepare("SELECT * FROM theme");
$stmt->execute();
$result = $stmt->get_result();
$themes = [];
while ($theme = $result->fetch_assoc()) {
    $themes[] = $theme;
}

// Ajouter une formation
if (isset($_POST['add_formation'])) {
    $f_nom = htmlspecialchars(trim($_POST['f_nom']));
    $f_theme_id = intval($_POST['f_theme_id']);
    $f_description = htmlspecialchars(trim($_POST['f_description']));
    $f_duree = htmlspecialchars(trim($_POST['f_duree']));
    $f_code = htmlspecialchars(trim($_POST['f_code']));
    $f_statut = htmlspecialchars(trim($_POST['f_statut']));
    $f_date_debut = $_POST['f_date_debut'];
    $f_date_fin = $_POST['f_date_fin'];

    // Validation des dates
    $dateError = validateDates($f_date_debut, $f_date_fin);
    if ($dateError) {
        $message[] = $dateError;
    } else {
        // Validation du fichier uploadé
        if (isset($_FILES['f_image']) && $_FILES['f_image']['error'] === UPLOAD_ERR_OK) {
            $fileError = validateFile($_FILES['f_image']);
            if ($fileError) {
                $message[] = $fileError;
            } else {
                $f_image = uniqid() . '_' . basename($_FILES['f_image']['name']); // Renommer le fichier
                $f_image_tmp_name = $_FILES['f_image']['tmp_name'];
                $f_image_folder = 'uploaded_img/' . $f_image;

                if (move_uploaded_file($f_image_tmp_name, $f_image_folder)) {
                    $stmt = $conn->prepare("INSERT INTO formation (nom, theme_id, description, image, duree, code, statut, date_debut, date_fin, date_creation) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                    $stmt->bind_param('sisssssss', $f_nom, $f_theme_id, $f_description, $f_image, $f_duree, $f_code, $f_statut, $f_date_debut, $f_date_fin);
                    $stmt->execute();

                    if ($stmt->affected_rows > 0) {
                        $message[] = 'Formation ajoutée avec succès.';
                    } else {
                        $message[] = 'Impossible d\'ajouter la formation.';
                    }
                } else {
                    $message[] = 'Erreur lors du téléchargement de l\'image.';
                }
            }
        } else {
            $message[] = 'Veuillez sélectionner une image valide.';
        }
    }
}

// Supprimer une formation
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM formation WHERE id = ?");
    $stmt->bind_param('i', $delete_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $message[] = 'Formation supprimée avec succès.';
    } else {
        $message[] = 'Impossible de supprimer la formation.';
    }
    header('location:admin.php');
    exit;
}

// Mettre à jour une formation
if (isset($_POST['update_formation'])) {
    $update_f_id = intval($_POST['update_f_id']);
    $update_f_nom = htmlspecialchars(trim($_POST['update_f_nom']));
    $update_f_theme_id = intval($_POST['update_f_theme_id']);
    $update_f_description = htmlspecialchars(trim($_POST['update_f_description']));
    $update_f_duree = htmlspecialchars(trim($_POST['update_f_duree']));
    $update_f_code = htmlspecialchars(trim($_POST['update_f_code']));
    $update_f_statut = htmlspecialchars(trim($_POST['update_f_statut']));
    $update_f_date_debut = $_POST['update_f_date_debut'];
    $update_f_date_fin = $_POST['update_f_date_fin'];

    // Validation des dates
    $dateError = validateDates($update_f_date_debut, $update_f_date_fin);
    if ($dateError) {
        $message[] = $dateError;
    } else {
        // Gestion de l'image
        $image_query = "";
        $params = [$update_f_nom, $update_f_theme_id, $update_f_description, $update_f_duree, $update_f_code, $update_f_statut, $update_f_date_debut, $update_f_date_fin, $update_f_id];
        $types = 'sissssssi';

        if (isset($_FILES['update_f_image']) && $_FILES['update_f_image']['error'] === UPLOAD_ERR_OK) {
            $fileError = validateFile($_FILES['update_f_image']);
            if ($fileError) {
                $message[] = $fileError;
            } else {
                $update_f_image = uniqid() . '_' . basename($_FILES['update_f_image']['name']);
                $update_f_image_tmp_name = $_FILES['update_f_image']['tmp_name'];
                $update_f_image_folder = 'uploaded_img/' . $update_f_image;

                if (move_uploaded_file($update_f_image_tmp_name, $update_f_image_folder)) {
                    $image_query = ", image = ?";
                    array_splice($params, -1, 0, $update_f_image);
                    $types = 'sisssssssi';
                } else {
                    $message[] = 'Erreur lors du téléchargement de la nouvelle image.';
                }
            }
        }

        $stmt = $conn->prepare("UPDATE formation SET nom = ?, theme_id = ?, description = ?, duree = ?, code = ?, statut = ?, date_debut = ?, date_fin = ? $image_query WHERE id = ?");
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $message[] = 'Formation mise à jour avec succès.';
        } else {
            $message[] = 'Impossible de mettre à jour la formation.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<meta charset="UTF-8">
<meta content="IE=edge" http-equiv="X-UA-Compatible">
<meta content="width=device-width,initial-scale=1" name="viewport">
<title>Admin Panel</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="./assets/css/stylec.css" rel="stylesheet">
<?php if (isset($message)): ?>
    <?php foreach ($message as $msg): ?>
        <div class="message"><span><?= $msg; ?></span><i class="fa-times fas" onclick='this.parentElement.style.display="none"'></i></div>
    <?php endforeach; ?>
<?php endif; ?>
<?php include 'header.php'; ?>
<div class="container">
    <section>
        <form action="" enctype="multipart/form-data" method="post" class="add-product-form">
            <h3>Ajouter une nouvelle formation</h3>
            <input name="f_nom" class="box" required placeholder="Nom de la formation">
            <select class="box" name="f_theme_id" required>
                <option value="">Sélectionner un thème</option>
                <?php foreach ($themes as $theme): ?>
                    <option value="<?= htmlspecialchars($theme['id']); ?>"><?= htmlspecialchars($theme['nom']); ?></option>
                <?php endforeach; ?>
            </select>
            <textarea class="box" name="f_description" required placeholder="Description de la formation"></textarea>
            <input name="f_image" class="box" required accept="image/png, image/jpg, image/jpeg, image/svg+xml" type="file">
            <input name="f_duree" class="box" required placeholder="Durée (ex : 5 semaines)">
            <input name="f_code" class="box" required placeholder="Code de la formation">
            <select class="box" name="f_statut" required>
                <option value="disponible">Disponible</option>
                <option value="indisponible">Indisponible</option>
            </select>
            <input name="f_date_debut" class="box" required type="date" placeholder="Date de début">
            <input name="f_date_fin" class="box" required type="date" placeholder="Date de fin">
            <input name="add_formation" class="btn" value="Ajouter la formation" type="submit">
        </form>
    </section>
    <section class="display-product-table">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>
                        Thème
                        <select id="themeFilter" class="box">
                            <option value="all">Tous les thèmes</option>
                            <?php foreach ($themes as $theme): ?>
                                <option value="<?= htmlspecialchars($theme['nom']); ?>"><?= htmlspecialchars($theme['nom']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </th>
                    <th>Description</th>
                    <th>Durée</th>
                    <th>Code</th>
                    <th>Statut</th>
                    <th>Date de début</th>
                    <th>Date de fin</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="formationTable">
                <?php
                $stmt = $conn->prepare("SELECT formation.*, theme.nom as theme_nom FROM formation JOIN theme ON formation.theme_id = theme.id");
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $description_preview = htmlspecialchars(substr($row['description'], 0, 100));
                        $description_full = htmlspecialchars($row['description']);
                        echo "<tr data-theme='" . htmlspecialchars($row['theme_nom']) . "'>
                            <td>" . htmlspecialchars($row['id']) . "</td>
                            <td><img src='uploaded_img/" . htmlspecialchars($row['image']) . "' height='70' alt=''></td>
                            <td>" . htmlspecialchars($row['nom']) . "</td>
                            <td>" . htmlspecialchars($row['theme_nom']) . "</td>
                            <td>
                                <span class='description-preview'>" . $description_preview . "...</span>
                                <span class='description-full' style='display: none;'>" . $description_full . "</span>
                                <button class='toggle-description' onclick='toggleDescription(this)'>Voir plus</button>
                            </td>
                            <td>" . htmlspecialchars($row['duree']) . "</td>
                            <td>" . htmlspecialchars($row['code']) . "</td>
                            <td>" . htmlspecialchars($row['statut']) . "</td>
                            <td>" . htmlspecialchars($row['date_debut']) . "</td>
                            <td>" . htmlspecialchars($row['date_fin']) . "</td>
                            <td>
                                <a href='admin.php?delete=" . $row['id'] . "' class='delete-btn' onclick=\"return confirm('Voulez-vous vraiment supprimer ?');\"><i class='fas fa-trash'></i> Supprimer</a>
                                <a href='admin.php?edit=" . $row['id'] . "' class='option-btn'><i class='fas fa-edit'></i> Modifier</a>
                            </td>
                        </tr>";
                    }
                }
                ?>
            </tbody>
        </table>
        <style>
            td,
            th {
                text-align: center;
                vertical-align: middle;
                padding: 10px;
                border: 1px solid #ccc;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
                width: 100%;
                border-collapse: collapse;
            }
        </style>
        <script>
            function toggleDescription(button) {
                const preview = button.previousElementSibling.previousElementSibling;
                const full = button.previousElementSibling;

                if (full.style.display === 'none') {
                    full.style.display = 'inline';
                    preview.style.display = 'none';
                    button.textContent = 'Voir moins';
                } else {
                    full.style.display = 'none';
                    preview.style.display = 'inline';
                    button.textContent = 'Voir plus';
                }
            }

            document.getElementById('themeFilter').addEventListener('change', function() {
                const selectedTheme = this.value.toLowerCase();
                const rows = document.querySelectorAll('#formationTable tr');
                let hasVisibleRows = false;

                rows.forEach(row => {
                    const theme = row.getAttribute('data-theme').toLowerCase();
                    if (selectedTheme === 'all' || theme === selectedTheme) {
                        row.style.display = '';
                        hasVisibleRows = true;
                    } else {
                        row.style.display = 'none';
                    }
                });

                const noDataRow = document.getElementById('noDataRow');
                if (!hasVisibleRows && noDataRow) {
                    noDataRow.remove();
                }
            });
        </script>
    </section>
    <?php if (isset($_GET['edit'])): ?>
        <?php
        $edit_id = intval($_GET['edit']);
        $stmt = $conn->prepare("SELECT * FROM formation WHERE id = ?");
        $stmt->bind_param('i', $edit_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $fetch_edit = $result->fetch_assoc();
        ?>
        <section class="edit-form-container" style="display:flex;flex-direction:column;max-height:500px;overflow-y:auto;border:1px solid #ccc;padding:10px;border-radius:5px">
            <form action="" enctype="multipart/form-data" method="post">
                <img alt="" src="uploaded_img/<?= htmlspecialchars($fetch_edit['image']); ?>" style="max-width: 100%; height: auto; max-height: 200px;">
                <input name="update_f_id" value="<?= $fetch_edit['id']; ?>" type="hidden" required>
                <input name="update_f_nom" class="box" required value="<?= htmlspecialchars($fetch_edit['nom']); ?>">
                <select class="box" name="update_f_theme_id" required>
                    <option value="">Sélectionner un thème</option>
                    <?php foreach ($themes as $theme): ?>
                        <option value="<?= htmlspecialchars($theme['id']); ?>" <?= $fetch_edit['theme_id'] == $theme['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($theme['nom']); ?></option>
                    <?php endforeach; ?>
                </select>
                <textarea class="box" name="update_f_description" required><?= htmlspecialchars($fetch_edit['description']); ?></textarea>
                <input name="update_f_image" class="box" type="file" accept="image/png, image/jpg, image/jpeg, image/svg+xml">
                <input name="update_f_duree" class="box" required value="<?= htmlspecialchars($fetch_edit['duree']); ?>">
                <input name="update_f_code" class="box" required placeholder="Code de la formation" value="<?= htmlspecialchars($fetch_edit['code']); ?>">
                <select class="box" name="update_f_statut" required>
                    <option value="disponible" <?= $fetch_edit['statut'] == 'disponible' ? 'selected' : ''; ?>>Disponible</option>
                    <option value="indisponible" <?= $fetch_edit['statut'] == 'indisponible' ? 'selected' : ''; ?>>Indisponible</option>
                </select>
                <input name="update_f_date_debut" class="box" required type="date" value="<?= htmlspecialchars($fetch_edit['date_debut']); ?>">
                <input name="update_f_date_fin" class="box" required type="date" value="<?= htmlspecialchars($fetch_edit['date_fin']); ?>">
                <input name="update_formation" class="btn" value="Mettre à jour la formation" type="submit">
                <a class="option-btn" href="admin.php">Annuler</a>
            </form>
        </section>
    <?php endif; ?>
</div>
<script src="./assets/js/scriptc.js"></script>