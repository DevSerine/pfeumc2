<?php
// Inclusion du fichier de configuration
@@include 'config.php';

// Vérification de la connexion à la base de données
if (!$conn) {
    die("Erreur de connexion à la base de données : " . mysqli_connect_error());
}

// Récupération des thèmes depuis la base de données
$select_themes = mysqli_query($conn, "SELECT DISTINCT theme.nom AS theme_nom FROM formation JOIN theme ON formation.theme_id = theme.id");
if (!$select_themes) {
    die("Erreur lors de la récupération des thèmes : " . mysqli_error($conn));
}

// Récupération des formations avec le nom du thème
$select_formations = mysqli_query($conn, "SELECT formation.*, theme.nom AS theme_nom FROM formation JOIN theme ON formation.theme_id = theme.id");
if (!$select_formations) {
    die("Erreur lors de la récupération des formations : " . mysqli_error($conn));
}
?><!doctypehtml><html lang=fr><meta charset=UTF-8><meta content="IE=edge"http-equiv=X-UA-Compatible><meta content="width=device-width,initial-scale=1"name=viewport><title>UMC²-Formations</title><link href=https://unpkg.com/leaflet@1.9.3/dist/leaflet.css rel=stylesheet><link href=https://fonts.googleapis.com rel=preconnect><link href=https://fonts.gstatic.com rel=preconnect crossorigin><link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;500;600;700;800&family=Poppins:wght@400;500&display=swap"rel=stylesheet><link href=https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css rel=stylesheet><link href=style.css rel=stylesheet><link href=./assets/css/style.css rel=stylesheet><style>.filter-dropdown{margin-top:20px;padding:10px;display:flex;justify-content:center}.filter-select{padding:10px;font-size:14px;border-radius:5px;border:1px solid #ccc}.hidden{display:none!important}</style><section aria-label=cours class="course section"id=courses><div class=container><a href=index.php><p class=section-subtitle style=color:#009fb0>Retour</p></a><h2 class="h2 section-title">Choisissez une Formation pour Commencer !</h2><div class=filter-dropdown><select class=filter-select id=themeFilter><option value=all>Tous les Thèmes</option><?php
                    if (mysqli_num_rows($select_themes) > 0) {
                        while ($row = mysqli_fetch_assoc($select_themes)) {
                            echo '<option value="' . htmlspecialchars($row['theme_nom']) . '">' . htmlspecialchars($row['theme_nom']) . '</option>';
                        }
                    } else {
                        echo '<option disabled>Aucun thème disponible</option>';
                    }
                    ?></select> <a href=./uploaded_img/1719237018877_compressed.pdf class=download-catalogue-btn><i class="fa-download fas"></i> Télécharger le catalogue</a><style>.filter-dropdown{display:flex;align-items:center;gap:10px;flex-wrap:wrap}.filter-select{padding:10px;border:1px solid #ddd;border-radius:5px;flex:1;min-width:150px}.download-catalogue-btn{display:inline-block;padding:10px 20px;background:linear-gradient(45deg,#009fb0,#00d4ff);color:#fff;text-decoration:none;border-radius:50px;transition:background .3s ease,transform .3s ease;font-size:14px;white-space:nowrap;box-shadow:0 4px 15px #009fb0}.download-catalogue-btn i{margin-right:8px}.download-catalogue-btn:hover{background:linear-gradient(45deg,#009fb0,#0af);transform:translateY(-2px)}@media screen and (max-width:600px){.download-catalogue-btn{padding:8px 16px;font-size:12px}}</style></div><ul class=grid-list id=filterable-courses><?php
            if (mysqli_num_rows($select_formations) > 0) {
                while ($row = mysqli_fetch_assoc($select_formations)) {
                    ?><li class=course-item data-name="<?= htmlspecialchars($row['theme_nom']); ?>"><div class=course-card><figure class="card-banner img-holder"style=--width:570;--height:320><img alt="<?= htmlspecialchars($row['nom']); ?>"class=img-cover height=220 loading=lazy src="uploaded_img/<?= htmlspecialchars($row['image']); ?>"width=370></figure><div class=abs-badge><ion-icon aria-hidden=true name=time-outline></ion-icon><span class=span><?= htmlspecialchars($row['duree']); ?></span></div><div class=card-content><span class=badge><?= htmlspecialchars($row['theme_nom']); ?></span><span>Du : <?= htmlspecialchars($row['date_debut']); ?> au <?= htmlspecialchars($row['date_fin']); ?></span><h3 class=h3><a href=# class=card-title><?= htmlspecialchars($row['nom']); ?></a></h3><p><strong>Code :</strong><?= htmlspecialchars($row['code']); ?><p><strong>Statut :</strong><?= htmlspecialchars($row['statut']); ?></p><button class=more-btn onclick="togglePopup(<?= $row['id']; ?>)">Plus</button><div class=popup id="popup<?= $row['id']; ?>"><div class=popup-content><span class=close-btn onclick="togglePopup(<?= $row['id']; ?>)">×</span><h2>Description de la formation</h2><p><?= nl2br(htmlspecialchars($row['description'])); ?></div></div></div></div></li><?php
                }
            } else {
                echo "<p>Aucune formation trouvée.</p>";
            }
            ?></ul></div></section><script>document.addEventListener('DOMContentLoaded', function () {
            const themeFilter = document.getElementById('themeFilter');
            const courseItems = document.querySelectorAll('.course-item');

            themeFilter.addEventListener('change', function () {
                const filterValue = this.value.toLowerCase();
                courseItems.forEach(item => {
                    const itemTheme = item.getAttribute('data-name').toLowerCase();
                    if (filterValue === 'all' || itemTheme === filterValue) {
                        item.classList.remove('hidden');
                    } else {
                        item.classList.add('hidden');
                    }
                });
            });
        });</script><script defer src=./assets/js/script.js></script><script defer src=https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js type=module></script><script defer src=https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js nomodule></script>