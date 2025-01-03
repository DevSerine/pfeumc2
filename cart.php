<?php
include('config.php');

session_start();
if (!isset($_SESSION['user_email']) || $_SESSION['user_email'] != 'admin@gmail.com') {
    // Si l'utilisateur n'est pas connecté ou n'est pas l'admin, redirigez-le vers la page de connexion
    header('location:login.php');
    exit;
}

// Récupérer les statistiques pour les utilisateurs, formations, et thèmes
$query_admin = "SELECT COUNT(*) as total_admins FROM utilisateur WHERE type_utilisateur = 'admin'";
$result_admin = mysqli_query($conn, $query_admin);
$data_admin = mysqli_fetch_assoc($result_admin);
$total_admins = $data_admin['total_admins'];

$query_postulation_etudiant = "SELECT COUNT(*) as total_postulations_etudiant FROM utilisateur WHERE type_utilisateur = 'etudiant' AND formation_id IS NOT NULL";
$result_postulation_etudiant = mysqli_query($conn, $query_postulation_etudiant);
$data_postulation_etudiant = mysqli_fetch_assoc($result_postulation_etudiant);
$total_postulations_etudiant = $data_postulation_etudiant['total_postulations_etudiant'];

$query_postulation_formateur = "SELECT COUNT(*) as total_postulations_formateur FROM utilisateur WHERE type_utilisateur = 'formateur' AND cv IS NOT NULL";
$result_postulation_formateur = mysqli_query($conn, $query_postulation_formateur);
$data_postulation_formateur = mysqli_fetch_assoc($result_postulation_formateur);
$total_postulations_formateur = $data_postulation_formateur['total_postulations_formateur'];

// Récupérer les coordonnées des étudiants et des formateurs
$query_etudiants = "SELECT utilisateur.*, formation.nom as formation_nom FROM utilisateur LEFT JOIN formation ON utilisateur.formation_id = formation.id WHERE type_utilisateur = 'etudiant'";
$result_etudiants = mysqli_query($conn, $query_etudiants);

$query_formateurs = "SELECT * FROM utilisateur WHERE type_utilisateur = 'formateur'";
$result_formateurs = mysqli_query($conn, $query_formateurs);

// Mettre à jour la colonne 'vue' pour un utilisateur spécifique
if (isset($_POST['mark_as_seen'])) {
    $user_id = intval($_POST['user_id']);
    $update_vue_query = "UPDATE utilisateur SET vue = TRUE WHERE id = $user_id";
    if (mysqli_query($conn, $update_vue_query)) {
        echo "<script>alert('Utilisateur marqué comme vu');</script>";
        header("Refresh:0");
    } else {
        echo "<script>alert('Erreur lors de la mise à jour');</script>";
    }
}

// Récupérer le nombre de formations disponibles
$query_formations_disponibles = "SELECT COUNT(*) as total_disponibles FROM formation WHERE statut = 'Disponible'";
$result_formations_disponibles = mysqli_query($conn, $query_formations_disponibles);
$data_formations_disponibles = mysqli_fetch_assoc($result_formations_disponibles);
$total_formations_disponibles = $data_formations_disponibles['total_disponibles'];

// Récupérer le nombre de formations indisponibles
$query_formations_indisponibles = "SELECT COUNT(*) as total_indisponibles FROM formation WHERE statut = 'Indisponible'";
$result_formations_indisponibles = mysqli_query($conn, $query_formations_indisponibles);
$data_formations_indisponibles = mysqli_fetch_assoc($result_formations_indisponibles);
$total_formations_indisponibles = $data_formations_indisponibles['total_indisponibles'];

$query_theme = "SELECT COUNT(*) as total_themes FROM theme";
$result_theme = mysqli_query($conn, $query_theme);
$data_theme = mysqli_fetch_assoc($result_theme);
$total_themes = $data_theme['total_themes'];

// Suppression d'un utilisateur
if (isset($_POST['delete'])) {
    $delete_id = intval($_POST['delete_id']);
    $delete_query = "DELETE FROM utilisateur WHERE id = $delete_id";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Utilisateur supprimé avec succès');</script>";
        header("Refresh:0");
    } else {
        echo "<script>alert('Erreur lors de la suppression de l'utilisateur');</script>";
    }
}
?><!doctypehtml><html lang=fr><meta charset=UTF-8><meta content="width=device-width,initial-scale=1"name=viewport><title>Statistiques des Tables</title><link href=https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css rel=stylesheet><link href=./assets/css/stylec.css rel=stylesheet><link href=https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css rel=stylesheet><script src=https://cdn.jsdelivr.net/npm/chart.js></script><style>.stats-container{margin-top:50px}.card{border:1px solid #ddd;border-radius:10px;box-shadow:0 4px 8px rgba(0,0,0,.1)}.card-body{text-align:center}.card-title{font-size:1.5rem;color:#007bff}.card-text{font-size:2rem;font-weight:700}.delete-btn{background-color:#ff4d4d;color:#fff;border:none;border-radius:5px;padding:8px 12px;cursor:pointer;font-size:14px;transition:background-color .3s ease}.delete-btn:hover{background-color:#ff1a1a}table{width:100%;border-collapse:collapse}table td,table th{border:1px solid #ddd;padding:8px;text-align:center}table th{background-color:#f4f4f4}header,header *{text-decoration:none;text-align:center}</style><div class="container stats-container"><div class=text-center><a href=admin.php class=back-btn>Retour</a></div><style>.back-btn{display:inline-block;padding:10px 20px;font-size:18px;font-weight:700;color:#fff;background:linear-gradient(45deg,#009fb0,#00d4ff);border:none;border-radius:50px;text-align:center;text-decoration:none;transition:background .3s ease,transform .3s ease}.back-btn:hover{background:linear-gradient(45deg,#0056b3,#00a3cc);transform:scale(1.05)}.text-center{text-align:center;margin-top:20px}</style><br><h1 class=heading>Statistiques/Informations</h1><div class="justify-content-center row"><div class=col-md-3><div class=card><div class=card-body><h5 class=card-title>Nombre Total de Postulations Étudiants</h5><p class=card-text><?php echo $total_postulations_etudiant; ?></div></div></div><div class=col-md-3><div class=card><div class=card-body><h5 class=card-title>Nombre Total de Postulations Formateurs</h5><p class=card-text><?php echo $total_postulations_formateur; ?></div></div></div><div class=col-md-3><div class=card><div class=card-body><h5 class=card-title>Nombre Total d'Administrateurs</h5><p class=card-text><?php echo $total_admins; ?></div></div></div><div class=col-md-3><div class=card><div class=card-body><h5 class=card-title>Formations Disponibles</h5><p class=card-text><?php echo $total_formations_disponibles; ?></div></div></div><div class=col-md-3><div class=card><div class=card-body><h5 class=card-title>Formations Indisponibles</h5><p class=card-text><?php echo $total_formations_indisponibles; ?></div></div></div><div class=col-md-3><div class=card><div class=card-body><h5 class=card-title>Nombre Total de Thèmes</h5><p class=card-text><?php echo $total_themes; ?></div></div></div></div><div class="justify-content-center row mt-5"><div class=col-md-8><div class=card><div class=card-body><h5 class=card-title>Statistiques Globales</h5><canvas id=statistiquesGraph></canvas></div></div></div></div></div><script>var totalPostulationsEtudiant=<?php echo $total_postulations_etudiant; ?>,totalPostulationsFormateur=<?php echo $total_postulations_formateur; ?>,totalAdmins=<?php echo $total_admins; ?>,totalFormationsDisponibles=<?php echo $total_formations_disponibles; ?>,totalFormationsIndisponibles=<?php echo $total_formations_indisponibles; ?>,totalThemes=<?php echo $total_themes; ?>,ctx=document.getElementById("statistiquesGraph").getContext("2d"),statistiquesGraph=new Chart(ctx,{type:"bar",data:{labels:["Postulations Étudiants","Postulations Formateurs","Administrateurs","Formations Disponibles","Formations Indisponibles","Thèmes"],datasets:[{label:"Nombre",data:[totalPostulationsEtudiant,totalPostulationsFormateur,totalAdmins,totalFormationsDisponibles,totalFormationsIndisponibles,totalThemes],backgroundColor:["#007bff","#dc3545","#28a745","#ffc107","#6f42c1","#17a2b8"],borderColor:["#0056b3","#c82333","#218838","#e0a800","#563d7c","#138496"],borderWidth:1}]},options:{responsive:!0,plugins:{legend:{position:"top"},tooltip:{callbacks:{label:function(t){return t.raw}}}},scales:{y:{beginAtZero:!0,title:{display:!0,text:"Nombre"}}}}})</script><script src=https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js></script><div class=container><br><h1 class=heading>Coordonnées des Postulations</h1><div class=btn-group aria-label="Basic example"role=group><button class="btn btn-primary"type=button onclick='showTable("etudiants")'>Étudiants</button> <button class="btn btn-secondary"type=button onclick='showTable("formateurs")'>Formateurs</button></div><section class=shopping-cart><div class=table-responsive id=etudiantsTable><h2>Étudiants</h2><table><thead><tr><th>ID<th>Nom<th>Email<th>Numéro Téléphone<th>Formation<th>Image<th>Vue<th>Action<tbody><?php
                        if (mysqli_num_rows($result_etudiants) > 0) {
                            while ($row = mysqli_fetch_assoc($result_etudiants)) {
                                ?><tr><td><?php echo $row['id']; ?><td><?php echo htmlspecialchars($row['nom']); ?><td><?php echo htmlspecialchars($row['email']); ?><td><?php echo htmlspecialchars($row['numero_tel']); ?><td><?php echo htmlspecialchars($row['formation_nom']); ?><td><?php if (!empty($row['image'])) { ?><img alt="Image de<?php echo htmlspecialchars($row['nom']); ?>"src="uploaded_img/<?php echo htmlspecialchars($row['image']); ?>"style="width:70px;display:block;margin:0 auto"><?php } else { ?>Pas d'image<?php } ?><td><?php echo $row['vue'] ? 'Oui' : 'Non'; ?><td><form action=""method=POST><input name=user_id type=hidden value="<?php echo $row['id']; ?>"> <button class="btn btn-success"type=submit name=mark_as_seen><i class="fa fa-eye"></i> Marquer comme vu</button></form><form action=""method=POST onsubmit='return confirm("Êtes-vous sûr de vouloir supprimer cet étudiant ?")'><input name=delete_id type=hidden value="<?php echo $row['id']; ?>"> <button class=delete-btn type=submit name=delete><i class="fa fa-trash"></i> Supprimer</button></form></tr><?php
                            }
                        } else {
                            ?><tr><td colspan=8>Aucun étudiant trouvé.</tr><?php
                        }
                        ?></table></div><div class=table-responsive id=formateursTable style=display:none><h2>Formateurs</h2><table><thead><tr><th>ID<th>Nom<th>Email<th>Numéro Téléphone<th>CV<th>Image<th>Vue<th>Action<tbody><?php
                        if (mysqli_num_rows($result_formateurs) > 0) {
                            while ($row = mysqli_fetch_assoc($result_formateurs)) {
                                ?><tr><td><?php echo $row['id']; ?><td><?php echo htmlspecialchars($row['nom']); ?><td><?php echo htmlspecialchars($row['email']); ?><td><?php echo htmlspecialchars($row['numero_tel']); ?><td><?php if (!empty($row['cv'])) { ?><a href="uploaded_img/<?php echo htmlspecialchars($row['cv']); ?>"target=_blank>Voir le CV</a><?php } else { ?>Pas de CV<?php } ?><td><?php if (!empty($row['image'])) { ?><img alt="Image de<?php echo htmlspecialchars($row['nom']); ?>"src="uploaded_img/<?php echo htmlspecialchars($row['image']); ?>"style="width:70px;display:block;margin:0 auto"><?php } else { ?>Pas d'image<?php } ?><td><?php echo $row['vue'] ? 'Oui' : 'Non'; ?><td><form action=""method=POST><input name=user_id type=hidden value="<?php echo $row['id']; ?>"> <button class="btn btn-success"type=submit name=mark_as_seen><i class="fa fa-eye"></i> Marquer comme vu</button></form><form action=""method=POST onsubmit='return confirm("Êtes-vous sûr de vouloir supprimer ce formateur ?")'><input name=delete_id type=hidden value="<?php echo $row['id']; ?>"> <button class=delete-btn type=submit name=delete><i class="fa fa-trash"></i> Supprimer</button></form></tr><?php
                            }
                        } else {
                            ?><tr><td colspan=8>Aucun formateur trouvé.</tr><?php
                        }
                        ?></table></div></section></div><script>function showTable(e){document.getElementById("etudiantsTable").style.display="etudiants"===e?"block":"none",document.getElementById("formateursTable").style.display="formateurs"===e?"block":"none"}</script><script src=https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js></script>