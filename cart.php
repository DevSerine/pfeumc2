<?php
include('config.php');

session_start();
if (!isset($_SESSION['user_email']) || $_SESSION['user_email'] != 'admin@gmail.com') {
    // Si l'utilisateur n'est pas connecté ou n'est pas l'admin, redirigez-le vers la page de connexion
    header('location:login.php');
    exit;
}

// Récupérer les statistiques pour les utilisateurs, formations, et thèmes
$stmt = $conn->prepare("SELECT COUNT(*) as total_postulations_entreprise FROM entreprise WHERE formation_id IS NOT NULL");
$stmt->execute();
$result_postulation_entreprise = $stmt->get_result();
$data_postulation_entreprise = $result_postulation_entreprise->fetch_assoc();
$total_postulations_entreprise = $data_postulation_entreprise['total_postulations_entreprise'];

$stmt = $conn->prepare("SELECT COUNT(*) as total_postulations_apprenant FROM apprenant WHERE formation_id IS NOT NULL");
$stmt->execute();
$result_postulation_apprenant = $stmt->get_result();
$data_postulation_apprenant = $result_postulation_apprenant->fetch_assoc();
$total_postulations_apprenant = $data_postulation_apprenant['total_postulations_apprenant'];

$stmt = $conn->prepare("SELECT COUNT(*) as total_postulations_formateur FROM formateur WHERE cv IS NOT NULL");
$stmt->execute();
$result_postulation_formateur = $stmt->get_result();
$data_postulation_formateur = $result_postulation_formateur->fetch_assoc();
$total_postulations_formateur = $data_postulation_formateur['total_postulations_formateur'];

// Récupérer les coordonnées des apprenants, des formateurs et des entreprises

$stmt = $conn->prepare("SELECT utilisateur.*, apprenant.type_apprenant, formation.nom as formation_nom FROM utilisateur LEFT JOIN apprenant ON utilisateur.id = apprenant.id_apprenant LEFT JOIN formation ON apprenant.formation_id = formation.id WHERE utilisateur.type_utilisateur = 'apprenant'");
$stmt->execute();
$result_apprenants = $stmt->get_result();

$stmt = $conn->prepare("SELECT utilisateur.*, formateur.cv FROM utilisateur LEFT JOIN formateur ON utilisateur.id = formateur.id_formateur WHERE utilisateur.type_utilisateur = 'formateur'");
$stmt->execute();
$result_formateurs = $stmt->get_result();

$stmt = $conn->prepare("SELECT utilisateur.*, formation.nom as formation_nom, entreprise.nb_employes, entreprise.adresse FROM utilisateur LEFT JOIN entreprise ON utilisateur.id = entreprise.id_entreprise LEFT JOIN formation ON entreprise.formation_id = formation.id WHERE utilisateur.type_utilisateur = 'entreprise'");
$stmt->execute();
$result_entreprises = $stmt->get_result();

// Mettre à jour la colonne 'vue' pour un utilisateur spécifique
if (isset($_POST['mark_as_seen'])) {
    $user_id = intval($_POST['user_id']);
    $stmt = $conn->prepare("UPDATE utilisateur SET vue = TRUE WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        echo "<script>alert('Utilisateur marqué comme vu');</script>";
        header("Refresh:0");
    } else {
        echo "<script>alert('Erreur lors de la mise à jour');</script>";
    }
}

// Récupérer le nombre de formations disponibles
$stmt = $conn->prepare("SELECT COUNT(*) as total_disponibles FROM formation WHERE statut = 'Disponible'");
$stmt->execute();
$result_formations_disponibles = $stmt->get_result();
$data_formations_disponibles = $result_formations_disponibles->fetch_assoc();
$total_formations_disponibles = $data_formations_disponibles['total_disponibles'];

// Récupérer le nombre de formations indisponibles
$stmt = $conn->prepare("SELECT COUNT(*) as total_indisponibles FROM formation WHERE statut = 'Indisponible'");
$stmt->execute();
$result_formations_indisponibles = $stmt->get_result();
$data_formations_indisponibles = $result_formations_indisponibles->fetch_assoc();
$total_formations_indisponibles = $data_formations_indisponibles['total_indisponibles'];

$stmt = $conn->prepare("SELECT COUNT(*) as total_themes FROM theme");
$stmt->execute();
$result_theme = $stmt->get_result();
$data_theme = $result_theme->fetch_assoc();
$total_themes = $data_theme['total_themes'];

// Suppression d'un utilisateur
if (isset($_POST['delete'])) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM utilisateur WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "<script>alert('Utilisateur supprimé avec succès');</script>";
        header("Refresh:0");
    } else {
        echo "<script>alert('Erreur lors de la suppression de l'utilisateur');</script>";
    }
}
?>
<!doctypehtml>
<html lang=fr>
<meta charset=UTF-8>
<meta content="width=device-width,initial-scale=1" name=viewport>
<title>Statistiques des Tables</title>
<link href=https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css rel=stylesheet>
<link href=./assets/css/stylec.css rel=stylesheet>
<link href=https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css rel=stylesheet>
<script src=https://cdn.jsdelivr.net/npm/chart.js></script>
<style>
    .stats-container {
        margin-top: 50px
    }

    .card {
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, .1)
    }

    .card-body {
        text-align: center
    }

    .card-title {
        font-size: 1.5rem;
        color: #007bff
    }

    .card-text {
        font-size: 2rem;
        font-weight: 700
    }

    .delete-btn {
        background-color: #ff4d4d;
        color: #fff;
        border: none;
        border-radius: 5px;
        padding: 8px 12px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color .3s ease
    }

    .delete-btn:hover {
        background-color: #ff1a1a
    }

    table {
        width: 100%;
        border-collapse: collapse
    }

    table td,
    table th {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center
    }

    table th {
        background-color: #f4f4f4
    }

    header,
    header * {
        text-decoration: none;
        text-align: center
    }
</style>
<div class="container stats-container">
    <div class=text-center><a href=admin.php class=back-btn>Retour</a></div>
    <style>
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(45deg, #009fb0, #00d4ff);
            border: none;
            border-radius: 50px;
            text-align: center;
            text-decoration: none;
            transition: background .3s ease, transform .3s ease
        }

        .back-btn:hover {
            background: linear-gradient(45deg, #0056b3, #00a3cc);
            transform: scale(1.05)
        }

        .text-center {
            text-align: center;
            margin-top: 20px
        }
    </style><br>
    <h1 class=heading>Statistiques/Informations</h1>
    <div class="justify-content-center row">
        <div class=col-md-3>
            <div class=card>
                <div class=card-body>
                    <h5 class=card-title>Nombre Total de Postulations Entreprises</h5>
                    <p class=card-text><?php echo $total_postulations_entreprise; ?>
                </div>
            </div>
        </div>
        <div class=col-md-3>
            <div class=card>
                <div class=card-body>
                    <h5 class=card-title>Nombre Total de Postulations Apprenants</h5>
                    <p class=card-text><?php echo $total_postulations_apprenant; ?>
                </div>
            </div>
        </div>
        <div class=col-md-3>
            <div class=card>
                <div class=card-body>
                    <h5 class=card-title>Nombre Total de Postulations Formateurs</h5>
                    <p class=card-text><?php echo $total_postulations_formateur; ?>
                </div>
            </div>
        </div>
        <div class=col-md-3>
            <div class=card>
                <div class=card-body>
                    <h5 class=card-title>Formations Disponibles</h5>
                    <p class=card-text><?php echo $total_formations_disponibles; ?>
                </div>
            </div>
        </div>
        <div class=col-md-3>
            <div class=card>
                <div class=card-body>
                    <h5 class=card-title>Formations Indisponibles</h5>
                    <p class=card-text><?php echo $total_formations_indisponibles; ?>
                </div>
            </div>
        </div>
        <div class=col-md-3>
            <div class=card>
                <div class=card-body>
                    <h5 class=card-title>Nombre Total de Thèmes</h5>
                    <p class=card-text><?php echo $total_themes; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="justify-content-center row mt-5">
        <div class=col-md-8>
            <div class=card>
                <div class=card-body>
                    <h5 class=card-title>Statistiques Globales</h5><canvas id=statistiquesGraph></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var totalPostulationsEntreprise = <?php echo $total_postulations_entreprise; ?>,
        totalPostulationsApprenant = <?php echo $total_postulations_apprenant; ?>,
        totalPostulationsFormateur = <?php echo $total_postulations_formateur; ?>,
        totalFormationsDisponibles = <?php echo $total_formations_disponibles; ?>,
        totalFormationsIndisponibles = <?php echo $total_formations_indisponibles; ?>,
        totalThemes = <?php echo $total_themes; ?>,
        ctx = document.getElementById("statistiquesGraph").getContext("2d"),
        statistiquesGraph = new Chart(ctx, {
            type: "bar",
            data: {
                labels: ["Postulations Entreprises", "Postulations Apprenants", "Postulations Formateurs", "Formations Disponibles", "Formations Indisponibles", "Thèmes"],
                datasets: [{
                    label: "Nombre",
                    data: [totalPostulationsEntreprise, totalPostulationsApprenant, totalPostulationsFormateur, totalFormationsDisponibles, totalFormationsIndisponibles, totalThemes],
                    backgroundColor: ["#007bff", "#dc3545", "#28a745", "#ffc107", "#6f42c1", "#17a2b8"],
                    borderColor: ["#0056b3", "#c82333", "#218838", "#e0a800", "#563d7c", "#138496"],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: !0,
                plugins: {
                    legend: {
                        position: "top"
                    },
                    tooltip: {
                        callbacks: {
                            label: function(t) {
                                return t.raw
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: !0,
                        title: {
                            display: !0,
                            text: "Nombre"
                        }
                    }
                }
            }
        });
</script>
<script src=https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js></script>
<div class=container><br>
    <h1 class=heading>Coordonnées des Postulations</h1>
    <div class=btn-group aria-label="Basic example" role=group>
        <button class="btn btn-primary" type=button onclick='showTable("entreprises")'>Entreprises</button>
        <button class="btn btn-secondary" type=button onclick='showTable("apprenants")'>Apprenants</button>
        <button class="btn btn-success" type=button onclick='showTable("formateurs")'>Formateurs</button>
    </div>
    <section class=shopping-cart>
        <div class=table-responsive id=entreprisesTable>
            <h2>Entreprises</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Numéro Téléphone</th>
                        <th>Adresse</th>
                        <th>Formation</th>
                        <th>Nombre d'employés</th>
                        <th>Image</th>
                        <th>Vue</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_entreprises->num_rows > 0) {
                        while ($row = $result_entreprises->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['nom']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['numero_tel']); ?></td>
                                <td><?php echo htmlspecialchars($row['adresse']); ?></td>
                                <td><?php echo htmlspecialchars($row['formation_nom']); ?></td>
                                <td><?php echo htmlspecialchars($row['nb_employes']); ?></td>
                                <td><?php if (!empty($row['image'])) { ?><img alt="Image de <?php echo htmlspecialchars($row['nom']); ?>" src="uploaded_img/<?php echo htmlspecialchars($row['image']); ?>" style="width:70px;display:block;margin:0 auto"><?php } else { ?>Pas d'image<?php } ?></td>
                                <td><?php echo $row['vue'] ? 'Oui' : 'Non'; ?></td>
                                <td>
                                    <form action="" method=POST>
                                        <input name=user_id type=hidden value="<?php echo $row['id']; ?>">
                                        <button class="btn btn-success" type=submit name=mark_as_seen><i class="fa fa-eye"></i> Marquer comme vu</button>
                                    </form>
                                    <form action="" method=POST onsubmit='return confirm("Êtes-vous sûr de vouloir supprimer cette entreprise ?")'>
                                        <input name=delete_id type=hidden value="<?php echo $row['id']; ?>">
                                        <button class=delete-btn type=submit name=delete><i class="fa fa-trash"></i> Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan=10>Aucune entreprise trouvée.</td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class=table-responsive id=apprenantsTable style=display:none>
            <h2>Apprenants</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Numéro Téléphone</th>
                        <th>Formation</th>
                        <th>Type d'apprenant</th>
                        <th>Image</th>
                        <th>Vue</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_apprenants->num_rows > 0) {
                        while ($row = $result_apprenants->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['nom']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['numero_tel']); ?></td>
                                <td><?php echo htmlspecialchars($row['formation_nom']); ?></td>
                                <td><?php echo isset($row['type_apprenant']) ? htmlspecialchars($row['type_apprenant']) : 'N/A'; ?></td>
                                <td><?php if (!empty($row['image'])) { ?><img alt="Image de <?php echo htmlspecialchars($row['nom']); ?>" src="uploaded_img/<?php echo htmlspecialchars($row['image']); ?>" style="width:70px;display:block;margin:0 auto"><?php } else { ?>Pas d'image<?php } ?></td>
                                <td><?php echo $row['vue'] ? 'Oui' : 'Non'; ?></td>
                                <td>
                                    <form action="" method=POST>
                                        <input name=user_id type=hidden value="<?php echo $row['id']; ?>">
                                        <button class="btn btn-success" type=submit name=mark_as_seen><i class="fa fa-eye"></i> Marquer comme vu</button>
                                    </form>
                                    <form action="" method=POST onsubmit='return confirm("Êtes-vous sûr de vouloir supprimer cet apprenant ?")'>
                                        <input name=delete_id type=hidden value="<?php echo $row['id']; ?>">
                                        <button class=delete-btn type=submit name=delete><i class="fa fa-trash"></i> Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan=9>Aucun apprenant trouvé.</td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class=table-responsive id=formateursTable style=display:none>
            <h2>Formateurs</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Numéro Téléphone</th>
                        <th>CV</th>
                        <th>Image</th>
                        <th>Vue</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_formateurs->num_rows > 0) {
                        while ($row = $result_formateurs->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['nom']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['numero_tel']); ?></td>
                                <td><?php if (!empty($row['cv'])) { ?><a href="uploaded_img/<?php echo htmlspecialchars($row['cv']); ?>" target=_blank>Voir le CV</a><?php } else { ?>Pas de CV<?php } ?></td>
                                <td><?php if (!empty($row['image'])) { ?><img alt="Image de <?php echo htmlspecialchars($row['nom']); ?>" src="uploaded_img/<?php echo htmlspecialchars($row['image']); ?>" style="width:70px;display:block;margin:0 auto"><?php } else { ?>Pas d'image<?php } ?></td>
                                <td><?php echo $row['vue'] ? 'Oui' : 'Non'; ?></td>
                                <td>
                                    <form action="" method=POST>
                                        <input name=user_id type=hidden value="<?php echo $row['id']; ?>">
                                        <button class="btn btn-success" type=submit name=mark_as_seen><i class="fa fa-eye"></i> Marquer comme vu</button>
                                    </form>
                                    <form action="" method=POST onsubmit='return confirm("Êtes-vous sûr de vouloir supprimer ce formateur ?")'>
                                        <input name=delete_id type=hidden value="<?php echo $row['id']; ?>">
                                        <button class=delete-btn type=submit name=delete><i class="fa fa-trash"></i> Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan=8>Aucun formateur trouvé.</td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
<script>
    function showTable(e) {
        document.getElementById("entreprisesTable").style.display = "entreprises" === e ? "block" : "none";
        document.getElementById("apprenantsTable").style.display = "apprenants" === e ? "block" : "none";
        document.getElementById("formateursTable").style.display = "formateurs" === e ? "block" : "none";
    }
</script>
<script src=https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js></script>
