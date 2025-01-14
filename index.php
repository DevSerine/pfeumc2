<?php
include 'config.php';
session_start();

// Vérifier si le paramètre 'logout' est présent dans l'URL
if (isset($_GET['logout'])) {
    // Détruire la session et rediriger vers la même page (ou vers une autre)
    session_unset(); // Supprime toutes les variables de session
    session_destroy(); // Détruit la session
    header('Location: index.php'); // Rediriger vers la page actuelle après la déconnexion
    exit();
}

// Vérifier si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = 'guest'; // Par défaut, un utilisateur non connecté
$userName = 'Utilisateur'; // Par défaut

if ($isLoggedIn) {
    $userId = $_SESSION['user_id'];
    $query = mysqli_query($conn, "SELECT * FROM utilisateur WHERE id = '$userId'");

    if ($query && mysqli_num_rows($query) > 0) {
        $userData = mysqli_fetch_assoc($query);
        $userRole = isset($userData['type_utilisateur']) ? $userData['type_utilisateur'] : 'guest'; // Utilisation de 'type_utilisateur' ici
        $userName = isset($userData['nom']) ? $userData['nom'] : 'Utilisateur'; // Par défaut
    }
}

$query = "SELECT * FROM `formation` ORDER BY `date_creation` DESC LIMIT 3";
$result = mysqli_query($conn, $query);

?><!doctypehtml><html lang="fr"><meta charset="UTF-8"><meta content="IE=edge" http-equiv="X-UA-Compatible"><meta content="width=device-width,initial-scale=1" name="viewport"><title>Centre-UMC²</title><meta content="UMC²-Formations" name="title"><link href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" rel="stylesheet"><link href="./assets/images/logo.png" rel="icon" type="image/webp"><link href="https://fonts.googleapis.com" rel="preconnect"><link href="https://fonts.gstatic.com" rel="preconnect" crossorigin><link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;500;600;700;800&family=Poppins:wght@400;500&display=swap" rel="stylesheet"><link href="./assets/js/main.js" rel="preload" as="script"><link href="./assets/css/style.css" rel="preload" as="style"><link href="./favicon.svg" rel="shortcut icon" type="image/svg+xml"><link href="./assets/css/style.css" rel="stylesheet"><link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"><link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;500;600;700;800&family=Poppins:wght@400;500&display=swap" rel="stylesheet"><link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet"><body id="top"><div class="loader">Loading...</div><div id="content"><header class="header" data-header><div class="container"><a href="#" class="logo"><img alt="logo" src="./assets/images/logo.png" height="50" width="100" loading="lazy"></a><nav class="navbar" data-navbar><div class="wrapper"><a href="#" class="logo"><img alt="logo" src="./assets/images/logo.png" height="50" width="100" loading="lazy"></a><button class="nav-close-btn" aria-label="close menu" data-nav-toggler><ion-icon name="close-outline" aria-hidden="true"></ion-icon></button></div><ul style="font-size:18px" class="navbar-list"><li class="navbar-item"><a href="#home" class="navbar-link" data-nav-link>Accueil</a><li class="navbar-item"><a href="#about" class="navbar-link" data-nav-link>Qui sommes-nous?</a><li class="navbar-item"><a href="#courses" class="navbar-link" data-nav-link>Formations</a><li class="navbar-item"><a href="#category" class="navbar-link" data-nav-link>Certifications</a><li class="navbar-item"><a href="#rejoindre" class="navbar-link" data-nav-link>Rejoignez-nous</a></ul></nav><div class="header-action"><button class="header-action-btn" onclick='window.location.href="login.php"' aria-label="Inscription" title="Inscription"></button><div class="icon-container"><ul class="icon-list"><li class="icon-item"><button class="icon-button" id="search-icon"><ion-icon name="search-outline" aria-hidden="true"></ion-icon></button></li><li class="icon-item"><button class="icon-button" id="mail-icon"><ion-icon name="mail-outline" aria-hidden="true"></ion-icon></button></li><div id="mail-popup" class="popup"><div class="popup-content"><span class="close-btn" id="close-mail-popup">&times;</span><p>Num:0560 00 55 24</p><p>E-mail:formation@umc2algerie.com</p></div></div> <?php if ($isLoggedIn): ?> <li class="icon-item user-dropdown-container"><div class="user-name-circle" onclick="toggleDropdown()"> <?= strtoupper(substr($userName, 0, 1)) ?></div><div class="user-dropdown"><span class="user-name">Hello, <?= htmlspecialchars($userName); ?></span><ul class="dropdown-menu"><li><a href="update_profile.php">Modifier le profil</a></li> <?php if ($userRole === 'admin'): ?> <li><a href="admin.php">Gestion CRUD</a></li> <?php endif; ?> <li><a href="?logout=1" class="delete-btn">Déconnexion</a></li></ul></div></li> <?php else: ?> <li class="icon-item"><a href="login.php"><button class="icon-button"><ion-icon name="person-outline" aria-hidden="true"></ion-icon></button></a></li> <?php endif; ?> </ul></div><div id="search-popup" class="popup"><div class="popup-content"><span class="close-btn" id="close-popup">&times;</span><input type="text" placeholder="Rechercher..."><button type="button">Rechercher</button></div></div></div><button class="header-action-btn" aria-label="open menu" data-nav-toggler><ion-icon name="menu-outline" aria-hidden="true"></ion-icon></button></div><div class="overlay" data-nav-toggler data-overlay></div></header><main><article><section aria-label="home" class="section has-bg-video hero" id="home"><div class="video-background"><video autoplay id="hero-video" loading="lazy" loop muted poster="./assets/images/videoframe_0.png" preload="auto"><source src="./assets/images/Bgvideo.mp4" type="video/mp4"><source src="./assets/images/Bgvideo.mp4" type="video/webm">Votre navigateur ne supporte pas la lecture de vidéos.</video></div><div class="container" style="display:flex;justify-content:center;align-items:center;height:100vh;text-align:center;margin-top:-90px"><div class="hero-content"><h1 class="section-title h1" style="color:#f0f8ff"><span class="span">UMC²</span>-Make it smart !</h1><p class="hero-text" style="color:#f0f8ff">Bienvenue chez le leader de la formation IT, Digital et Management en Algérie</p><ul class="social-list"><li><a href="https://www.facebook.com/UMC2.dz?locale=fr_FR" class="social-link facebook"><ion-icon name="logo-facebook"></ion-icon></a></li><li><a href="https://www.linkedin.com/company/umc2-algerie/about/" class="social-link linkedin"><ion-icon name="logo-linkedin"></ion-icon></a></li><li><a href="https://x.com/umc2center" class="social-link twitter"><ion-icon name="logo-twitter"></ion-icon></a></li><li><a href="https://www.instagram.com/umc2.dz/" class="social-link instagram"><ion-icon name="logo-instagram"></ion-icon></a></li></ul><div class="search-container"><input type="text" placeholder="Rechercher une formation..." id="search-input" onkeyup="fetchFormations(this.value)" style="width:100%;padding:15px 20px;font-size:16px;border:2px solid #ccc;border-radius:30px;outline:0;transition:border-color .3s ease"><button id="search-button" style="width:40px;height:37px;border-radius:50%;background-color:#009fb0;color:#fff;cursor:pointer;transition:background-color .3s ease;display:flex;justify-content:center;align-items:center"><ion-icon name="search-outline" aria-hidden="true"></ion-icon></button><ul id="suggestions-list" style="top:15px;position:absolute;width:60%;max-height:200px;overflow-y:auto;background-color:#fff;border:1px solid #ccc;border-radius:10px;box-shadow:0 4px 6px rgba(0,0,0,.1);list-style:none;padding:0;margin-top:45px;z-index:1000;color:#000"></ul></div></div></div><a href="./uploaded_img/1719237018877_compressed.pdf" target="_blank"><button id="fixedButton" style="position:fixed;top:130px;right:0;z-index:1000;padding:15px;background:linear-gradient(135deg,#009fb0,#00d4ff);color:#fff;border:none;border-radius:5px 0 0 5px;box-shadow:0 4px 15px rgba(0,0,0,.2);cursor:pointer;transition:all .3s ease;display:flex;flex-direction:column;align-items:center;justify-content:center;width:250px;height:70px"><i class="fas fa-book" style="font-size:24px;margin-bottom:10px"></i><span style="font-size:14px;text-align:center;line-height:1.2">CATALOGUE DE FORMATIONS</span></button></a></section><br><section aria-label="À propos de UMC²" class="section about" id="about"><div class="container"><figure class="about-banner"><div class="img-holder" style="--width:200;--height:200"><img alt="Illustration de formation en programmation" src="./assets/images/Application programming interface-amico.png" height="200" width="200" loading="lazy" class="img-cover"></div></figure><div class="about-content"><p class="section-subtitle" style="color:#009fb0">Qui sommes-nous?<p class="section-text"><h1 class="about-title" style="color:#009fb0;font-weight:700;font-style:italic">UMC²</h1>est un centre de formation professionnelle, agréé par l'état, créé en 2020 par la société UMAITEK. Spécialisé dans les formations qualifiantes et certifiantes, nos formations couvrent les domaines suivants :<ul class="about-list"><li class="about-item" aria-label="Technologies Linux Redhat"><ion-icon name="checkmark-done-outline" aria-hidden="true"></ion-icon><span class="span">Technologies Linux Redhat</span><li class="about-item" aria-label="DataCenter, Virtualisation, Cloud"><ion-icon name="checkmark-done-outline" aria-hidden="true"></ion-icon><span class="span">DataCenter, Virtualisation, Cloud</span><li class="about-item" aria-label="Stockage, Réseautage"><ion-icon name="checkmark-done-outline" aria-hidden="true"></ion-icon><span class="span">Stockage, Réseautage</span><li class="about-item" aria-label="Cybersécurité"><ion-icon name="checkmark-done-outline" aria-hidden="true"></ion-icon><span class="span">Cybersécurité</span><li class="about-item" aria-label="Gestion de Projet"><ion-icon name="checkmark-done-outline" aria-hidden="true"></ion-icon><span class="span">Gestion de Projet</span></ul></div></div></section><section aria-label="cours" class="section course" id="courses"><div class="container"><p class="section-subtitle" style="color:#009fb0">Formations<h2 class="section-title h2">Découvrez Nos Formations d'Actualité !</h2><ul class="grid-list" id="filterable-courses"><?php

                            // Requête pour récupérer les 3 dernières formations
                            $query = "SELECT * FROM `formation` ORDER BY `date_creation` DESC LIMIT 3";
                            $result = mysqli_query($conn, $query);

                            // Requête pour récupérer les 3 dernières formations avec le nom du thème
                            $query = "SELECT formation.*, theme.nom AS theme_nom FROM formation JOIN theme ON formation.theme_id = theme.id ORDER BY formation.date_creation DESC LIMIT 3";
                            $result = mysqli_query($conn, $query);

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    ?><li class="course-item"><div class="course-card"><figure class="img-holder card-banner" style="--width:370;--height:220"><img alt="<?php echo htmlspecialchars($row['nom']); ?>" src="uploaded_img/<?php echo htmlspecialchars($row['image']); ?>" height="220" width="370" loading="lazy" class="img-cover"></figure><div class="abs-badge"><ion-icon name="time-outline" aria-hidden="true"></ion-icon><span class="span"><?php echo htmlspecialchars($row['duree']); ?></span></div><div class="card-content"><span class="badge"><?php echo htmlspecialchars($row['theme_nom']); ?></span><span>Du : <?= htmlspecialchars($row['date_debut']); ?> au <?= htmlspecialchars($row['date_fin']); ?></span><h3 class="h3"><a href="#" class="card-title"><?php echo htmlspecialchars($row['nom']); ?></a></h3><p><strong>Code :</strong><?php echo htmlspecialchars($row['code']); ?> <p><strong>Statut :</strong><?= htmlspecialchars($row['statut']); ?></p><button class="more-btn" onclick="togglePopup(<?php echo $row['id']; ?>)">Plus</button></div></div><div class="popup" id="popup<?php echo $row['id']; ?>"><div class="popup-content"><span class="close-btn" onclick="togglePopup(<?php echo $row['id']; ?>)">×</span><h2>Description de la formation</h2><p><?php echo nl2br(htmlspecialchars($row['description'])); ?> </div></div><div></div></li><?php
                                }
                            } else {
                                echo "Aucune formation disponible.";
                            }
                            ?></ul><a href="filtration.php" class="btn has-before"><span class="span">Parcourir d'autres Formations</span><ion-icon name="arrow-forward-outline" aria-hidden="true"></ion-icon></a></div></section><section aria-label="category" class="section brand-list" id="category"><div class="container"><p class="section-subtitle" style="color:#009fb0">Certifications<h1 class="section-title h2">Formations Qualifiantes & Certifications</h1><br><ul class="brand-grid-list"><li class="brand-item"><a href="https://www.brand1.com" target="_blank"><img alt="Brand 1 Logo" src="assets/images/Red_Hat-Logo.wine.png" height="100" width="100" loading="lazy"></a><li class="brand-item"><a href="https://www.brand2.com" target="_blank"><img alt="Brand 2 Logo" src="assets/images/Sophos-Logo.wine.png" height="100" width="100" loading="lazy"></a><li class="brand-item"><a href="https://www.brand3.com" target="_blank"><img alt="Brand 3 Logo" src="assets/images/zimbra.png" height="100" width="100" loading="lazy"></a><li class="brand-item"><a href="https://www.brand4.com" target="_blank"><img alt="Brand 4 Logo" src="assets/images/pecb-logo.png" height="100" width="100" loading="lazy"></a><li class="brand-item"><a href="https://www.brand4.com" target="_blank"><img alt="Brand 4 Logo" src="assets/images/EC-Council.png" height="100" width="100" loading="lazy"></a><li class="brand-item"><a href="https://www.brand4.com" target="_blank"><img alt="Brand 4 Logo" src="assets/images/cisco-logo-transparent.png" height="100" width="100" loading="lazy"></a><li class="brand-item"><a href="https://www.brand4.com" target="_blank"><img alt="Brand 4 Logo" src="assets/images/pvue.png" height="100" width="100" loading="lazy"></a><li class="brand-item"><a href="https://www.brand4.com" target="_blank"><img alt="Brand 4 Logo" src="assets/images/Fortinet-Logo.wine.png" height="100" width="100" loading="lazy"></a><li class="brand-item"><a href="https://www.brand4.com" target="_blank"><img alt="Brand 4 Logo" src="uploaded_img/comptia+partner+badge.png" height="100" width="100" loading="lazy"></a></ul></div></section><br><section class="certification-section" id="rejoindre"><p class="section-subtitle">Rejoignez-nous<h1 class="section-title">Valorisez Votre Expertise en IT !</h1><div class="content"><div class="box"><h3>Pour Vous</h3><p>Que vous soyez étudiant, employeur ou entreprise,<br>obtenir une certification est un investissement stratégique.<br>C'est une reconnaissance tangible de vos compétences<br>et de votre expertise, renforçant votre crédibilité<br>et votre impact dans votre domaine.</p></div><div class="image"><img src="uploaded_img/serious-male-employee-working-project-office (2).jpg" alt="Person giving presentation" loading="lazy"></div></div><div class="content"><div class="image"><img src="uploaded_img/interviewer-reading-applicants-long-resume (1) (1).jpg" alt="Professional meeting" loading="lazy"></div><div class="box"><h3>Rejoignez Notre Équipe Pédagogique !</h3><p>Si vous disposez d'une expertise en technologies de l'information,<br>nous vous invitons à rejoindre notre équipe pédagogique.<br>Participez activement à la formation des futurs professionnels<br>en partageant vos connaissances et en contribuant<br>à l'enrichissement de nos programmes de certification.</p></div></div><div class="cta"><a href="register.php" class="btn has-before"><ion-icon name="person-outline" aria-hidden="true"></ion-icon><span class="span">Inscrivez-vous maintenant !</span></a></div></section><section aria-label="stats" class="section stats"><p class="section-subtitle" style="color:#009fb0">En Savoir Plus<h1 class="section-title h2">Explorez l'essence de nos principes >_</h1><br><div class="container"><ul class="grid-list"><li><div class="stats-card" style="--color:170,75%,41%"><h3 class="card-title" id="learnersCount">0</h3><p class="card-text">Apprenants Inscrits</div><li><div class="stats-card" style="--color:351,83%,61%"><h3 class="card-title" id="coursesCompleted">0</h3><p class="card-text">Cours Complétés</div><li><div class="stats-card" style="--color:260,100%,67%"><h3 class="card-title" id="satisfactionRate">0%</h3><p class="card-text">Taux de Satisfaction</div><li><div class="stats-card" style="--color:42,94%,55%"><h3 class="card-title" id="trainersCount">0</h3><p class="card-text">Formateurs agréés</div></ul></div></section><div class="carousel-container"><h1>Témoignages de nos étudiants</h1><div class="carousel" id="student-carousel"><div class="carousel-item"><div class="profile"><div class="information"><div class="stars"><span>★★★★★</span></div><p>Mohammed Kaci - La formation Linux Redhat m'a permis de gérer les serveurs efficacement.</p></div></div></div><div class="carousel-item"><div class="profile"><div class="information"><div class="stars"><span>★★★★★</span></div><p>Imene Tasaadith - La formation cybersécurité m'a aidé à protéger les données sensibles.</p></div></div></div><div class="carousel-item"><div class="profile"><div class="information"><div class="stars"><span>★★★★★</span></div><p>Khaled Ait Saadi - La formation DataCenter m'a permis de gérer le Cloud efficacement.</p></div></div></div><div class="carousel-item"><div class="profile"><div class="information"><div class="stars"><span>★★★★★</span></div><p>Amine Benali - La formation virtualisation m'a aidé à optimiser les infrastructures.</p></div></div></div><div class="carousel-item"><div class="profile"><div class="information"><div class="stars"><span>★★★★★</span></div><p>Amina Lahlou - La formation gestion de projet m'a permis de planifier efficacement.</p></div></div></div></div><button class="carousel-button left">❮</button><button class="carousel-button right">❯</button></div><div class="carousel-container"><h1>Nos Partenaires</h1><div class="carousel" id="partner-carousel"><div class="carousel-item partner-item"><img alt="Partenaire 1" src="/assets/images/cisco-logo-transparent.png"></div><div class="carousel-item partner-item"><img alt="Partenaire 2" src="assets/images/EC-Council.png"></div><div class="carousel-item partner-item"><img alt="Partenaire 3" src="/assets/images/Fortinet-Logo.wine.png"></div><div class="carousel-item partner-item"><img alt="Partenaire 4" src="/assets/images/pecb-logo.png"></div><div class="carousel-item partner-item"><img alt="Partenaire 5" src="/assets/images/Red_Hat-Logo.wine.png"></div><div class="carousel-item partner-item"><img alt="Partenaire 6" src="/assets/images/Sophos-Logo.wine.png"></div><div class="carousel-item partner-item"><img alt="Partenaire 7" src="/assets/images/zimbra.png"></div></div><button class="carousel-button left">❮</button><button class="carousel-button right">❯</button></div><script>document.addEventListener("DOMContentLoaded", function () {
    const carousels = document.querySelectorAll('.carousel');
    const itemWidth = 200;

    carousels.forEach(carousel => {
        const items = carousel.querySelectorAll('.carousel-item');
        const totalItems = items.length;
        let index = 0;

        function moveToItem(carousel, index) {
            carousel.style.transition = 'transform 0.5s ease-in-out';
            carousel.style.transform = `translateX(-${index * itemWidth}px)`;
        }

        carousel.parentElement.querySelector('.carousel-button.left').addEventListener('click', function () {
            index = (index > 0) ? index - 1 : totalItems - 1;
            moveToItem(carousel, index);
        });

        carousel.parentElement.querySelector('.carousel-button.right').addEventListener('click', function () {
            index = (index + 1) % totalItems;
            moveToItem(carousel, index);
        });
    });
});</script></article></main><br><footer class="footer" style="background-image:url(assets/images/footer-bg.png)"><div class="section footer-top"><div class="container grid-list"><div class="footer-brand"><a href="#" class="logo"><img alt="EduWeb logo" src="./assets/images/logo.png" height="50" width="100"></a><div class="wrapper"><span class="span">Locale:</span><address class="address">Cite Pons N 178 Jolie vue 2 Kouba, Kouba, Algeria</address></div><div style="height:150px;width:100%" id="map"></div><script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script><script>/*<![CDATA[*/const map = L.map("map").setView([36.7333, 3.0833], 15); L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", { maxZoom: 19, attribution: "© OpenStreetMap" }).addTo(map); L.marker([36.7333, 3.0833]).addTo(map).bindPopup("<b>Siège social</b><br>Cite Pons N 178 Jolie vue 2 Kouba, Alger, DZ").openPopup();/*]]>*/</script><br><div class="wrapper"><span class="span">Tel:</span><a href="#" class="footer-link">+213(0) 560 005 524</a></div><div class="wrapper"><span class="span">E-mail:</span><a href="#" class="footer-link">formation@umc2algerie.com</a></div></div><ul class="footer-list"><li><p class="footer-list-title">NavBar<li><a href="#" class="footer-link">Accueil</a><li><a href="#" class="footer-link">Qui sommes-nous?</a><li><a href="#" class="footer-link">Formations</a><li><a href="#" class="footer-link">Certifications</a><li><a href=# class=footer-link>Rejoignez-nous</a></ul><ul class="footer-list"><li><p class="footer-list-title">Liens<li><a href="#" class="footer-link">Contactez-nous</a><li><a href="#" class="footer-link">Galerie</a><li><a href="filtration.php" class="footer-link">Actualités et articles</a><li><a href="#" class="footer-link">FAQ</a><li><a href="login.php" class="footer-link">Connexion/Inscription</a><li><a href="#" class="footer-link">À venir</a></ul><div class="footer-list"><p class="footer-list-title">>_Contact<p class="footer-list-text">Entrez votre adresse e-mail pour plus de details !<form action="" class="newsletter-form"><input placeholder="Your email" class="input-field" name="email_address" required type="email"><button class="btn has-before" type="submit"><span class="span">Envoyer</span><ion-icon name="arrow-forward-outline" aria-hidden="true"></ion-icon></button></form><ul class="social-list"><li><a href="https://www.facebook.com/UMC2.dz?locale=fr_FR" class="social-link facebook"><ion-icon name="logo-facebook"></ion-icon></a><li><a href="https://www.linkedin.com/company/umc2-algerie/about/" class="social-link linkedin"><ion-icon name="logo-linkedin"></ion-icon></a><li><a href="https://x.com/umc2center" class="social-link twitter"><ion-icon name="logo-twitter"></ion-icon></a><li><a href="https://www.instagram.com/umc2.dz/" class="social-link instagram"><ion-icon name="logo-instagram"></ion-icon></a></ul></div></div></div><div class="footer-bottom"><div class="container"><p class="copyright">© Copyright<a href="#" class="copyright-link">Centre-UMC²</a>. Service proposé par UMAITEK. All Rights Reserved</div></div></footer><a href="#top" class="back-top-btn" aria-label="back top top" data-back-top-btn><ion-icon name="chevron-up" aria-hidden="true"></ion-icon></a><script src="./assets/js/script.js" defer="defer"></script><script src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js" type="module"></script><script src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js" nomodule></script><script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"><style>.search-popup{display:none;position:fixed;z-index:1;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:#000;background-color:rgba(0,0,0,.4)}.search-popup-content{background-color:#fefefe;margin:15% auto;padding:20px;border:1px solid #888;width:80%}.close-btn{color:#aaa;float:right;font-size:28px;font-weight:700}.close-btn:focus,.close-btn:hover{color:#000;text-decoration:none;cursor:pointer}.header-action{display:flex;align-items:center}.icon-container{display:flex;align-items:center}.icon-list{display:flex;list-style:none;padding:0;margin:0}.icon-item{margin-right:30px}.icon-item:last-child{margin-right:0}.icon-button{font-size:25px;background:0 0;border:none;cursor:pointer}.icon-button ion-icon{font-size:25px}.header-action{position:relative;display:inline-block}.user-dropdown-container{position:relative;display:inline-block}.user-name-circle{width:30px;height:30px;border-radius:50%;background-color:#007bff;color:#fff;display:flex;align-items:center;justify-content:center;font-size:20px;cursor:pointer;margin-left:10px}.user-dropdown{position:absolute;top:100%;right:0;background-color:#fff;border:1px solid #ddd;border-radius:5px;display:none;width:150px;box-shadow:0 4px 6px rgba(0,0,0,.1);z-index:1000}.user-dropdown ul{list-style:none;margin:0;padding:0}.user-dropdown ul li{text-align:center}.user-dropdown ul li a{display:block;padding:10px;color:#333;text-decoration:none;transition:background-color .3s ease}.user-dropdown ul li a:hover{background-color:#ddd}.search-container{display:flex;align-items:center;justify-content:center;width:100%;margin-top:20px;position:relative}#search-input{color:#fff}#search-input::placeholder{color:#ccc}#search-input:focus{border-color:#009fb0}.social-list{display:flex;justify-content:center;gap:10px}.social-link{font-size:35px;padding:10px;transition:transform .3s ease;display:inline-block}.social-link:hover{transform:scale(1.2)}.social-link:active{transform:scale(1.5)}.social-link.facebook{color:#3b5998}.social-link.twitter{color:#1da1f2}.social-link.instagram{color:#e1306c}.social-link.linkedin{color:#0077b5}#fixedButton{position:fixed;top:130px;right:0;z-index:1000;padding:15px;background:linear-gradient(135deg,#009fb0,#00d4ff);color:#fff;border:none;border-radius:5px 0 0 5px;box-shadow:0 4px 15px rgba(0,0,0,.2);cursor:pointer;transition:all .3s ease;display:flex;flex-direction:column;align-items:center;justify-content:center;width:250px;height:70px}#fixedButton i{font-size:24px;margin-bottom:10px}#fixedButton span{font-size:14px;text-align:center;line-height:1.2}@media (max-width:768px){#fixedButton{width:200px;height:60px;padding:10px}#fixedButton i{font-size:20px}#fixedButton span{font-size:12px}}@media (max-width:480px){#fixedButton{width:150px;height:50px;padding:8px}#fixedButton i{font-size:18px}#fixedButton span{font-size:10px}}.certification-section{text-align:center;padding:60px 20px;font-family:Montserrat,sans-serif;background-color:#f9f9f9}.certification-section .section-subtitle{font-size:13.5px;color:#009fb0;margin-bottom:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase}.certification-section .section-title{font-size:42px;color:#333;margin-bottom:40px;font-weight:700;letter-spacing:-1px;line-height:1.2}.certification-section .content{display:flex;align-items:center;justify-content:center;margin-bottom:40px;flex-wrap:wrap}.certification-section .box{width:45%;text-align:left;margin:10px}.certification-section .box h3{font-size:28px;margin-bottom:15px;color:#002f6c;font-weight:600;text-transform:uppercase;letter-spacing:1px;position:relative;display:inline-block}.certification-section .box h3::after{content:'';display:block;width:60px;height:3px;background-color:#009fb0;margin-top:10px}.certification-section .box p{font-size:18px;line-height:1.8;color:#555}.certification-section .image{width:45%;margin:10px}.certification-section .image img{width:100%;height:auto;border-radius:10px;box-shadow:0 4px 8px rgba(0,0,0,.1)}.certification-section .cta{margin-top:40px;text-align:center}.certification-section .btn{display:inline-flex;align-items:center;justify-content:center;padding:15px 30px;font-size:20px;color:#fff;background-color:#009fb0;border-radius:8px;text-decoration:none;transition:background-color .3s,transform .3s}.certification-section .btn ion-icon{margin-right:10px;font-size:24px}.certification-section .btn:hover{background-color:#007a8c;transform:scale(1.05)}@media screen and (max-width:768px){.certification-section .content{flex-direction:column}.certification-section .box,.certification-section .image{width:100%;margin-bottom:20px}.certification-section .section-title{font-size:32px}.certification-section .box h3{font-size:24px}}.carousel-container{width:80%;height:320px;overflow:hidden;position:relative;background:#f9f9f9;display:flex;justify-content:center;align-items:center;flex-direction:column;margin:0 auto}h1{font-size:24px;color:#333;text-align:center;margin-bottom:20px}.carousel{display:flex;flex-direction:row;width:100%;justify-content:flex-start;transition:transform .5s ease-in-out}.carousel-item{width:200px;flex-shrink:0;margin:10px;background:#fff;border-radius:10px;box-shadow:0 4px 6px rgba(0,0,0,.1);padding:15px;text-align:center;display:flex;flex-direction:column;justify-content:space-between}.carousel-item img{max-width:100%;border-radius:10px}.partner-item{width:150px;background:0 0;box-shadow:none;padding:0;display:flex;align-items:center;justify-content:center}.carousel-button{position:absolute;top:50%;transform:translateY(-50%);background-color:rgba(0,0,0,.5);color:#fff;border:none;padding:10px;cursor:pointer}.carousel-button.left{left:10px}.carousel-button.right{right:10px}</style><script>document.addEventListener('DOMContentLoaded', () => {
            const hasSeenPreloader = localStorage.getItem('hasSeenPreloader');
            const loader = document.querySelector('.loader');
            const content = document.getElementById('content');
            if (!hasSeenPreloader) {
                window.addEventListener('load', () => {
                    setTimeout(() => {
                        loader.style.opacity = '0';
                        setTimeout(() => {
                            loader.style.display = 'none';
                            content.style.display = 'block';
                            localStorage.setItem('hasSeenPreloader', 'true');
                        }, 500);
                    }, 1000);
                });
            } else {
                loader.style.display = 'none';
                content.style.display = 'block';
            }

            document.getElementById('search-icon').addEventListener('click', function () {
                document.getElementById('search-popup').style.display = 'block';
            });

            document.getElementById('close-popup').addEventListener('click', function () {
                document.getElementById('search-popup').style.display = 'none';
            });

            window.addEventListener('click', function (event) {
                if (event.target == document.getElementById('search-popup')) {
                    document.getElementById('search-popup').style.display = 'none';
                }
            });

            document.getElementById('mail-icon').addEventListener('click', function () {
                document.getElementById('mail-popup').style.display = 'block';
            });

            document.getElementById('close-mail-popup').addEventListener('click', function () {
                document.getElementById('mail-popup').style.display = 'none';
            });

            window.addEventListener('click', function (event) {
                if (event.target == document.getElementById('mail-popup')) {
                    document.getElementById('mail-popup').style.display = 'none';
                }
            });
        });

        function toggleDropdown() {
            const dropdown = document.querySelector('.user-dropdown');
            dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
        }

        document.addEventListener('click', function (event) {
            const dropdown = document.querySelector('.user-dropdown');
            const userCircle = document.querySelector('.user-name-circle');
            if (!userCircle.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });

        function fetchFormations(query) {
            const suggestionsList = document.getElementById('suggestions-list');
            suggestionsList.innerHTML = '';

            if (query.length < 1) return;

            fetch(`search_formations.php?query=${encodeURIComponent(query)}`)
                .then((response) => response.text())
                .then((html) => {
                    suggestionsList.innerHTML = html;
                })
                .catch((error) => console.error('Erreur lors de la recherche :', error));
        }

        function selectFormation(name) {
            const searchInput = document.getElementById('search-input');
            searchInput.value = name;
            document.getElementById('suggestions-list').innerHTML = '';
        }

        document.getElementById('fixedButton').addEventListener('mouseover', function () {
            this.style.background = 'linear-gradient(135deg, #00d4ff, #009fb0)';
            this.style.boxShadow = '0 6px 20px rgba(0, 0, 0, 0.3)';
        });

        document.getElementById('fixedButton').addEventListener('mouseout', function () {
            this.style.background = 'linear-gradient(135deg, #009fb0, #00d4ff)';
            this.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.2)';
        });

       
        window.addEventListener('load', function () {
            const loader = document.getElementById('loader');
            const content = document.getElementById('content');
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.style.display = 'none';
                content.style.display = 'block';
            }, 50);
        });</script></div></body></html>