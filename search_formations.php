<?php
include 'config.php';
session_start();

if (isset($_GET['query'])) {
    $searchQuery = '%' . $_GET['query'] . '%';
    $sql = "SELECT formation.nom, formation.image, theme.nom AS theme_nom 
            FROM formation 
            JOIN theme ON formation.theme_id = theme.id 
            WHERE formation.nom LIKE ? 
            ORDER BY formation.nom ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $searchQuery);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Ajouter un lien autour de chaque élément de la liste
            echo "<li style='display: flex; align-items: center; margin-bottom: 10px;'>"; 
            echo "<a href='filtration.php?formation=" . urlencode($row['nom']) . "' style='display: flex; align-items: center; text-decoration: none;'>"; 
            // Le lien vers filtration.php avec le paramètre 'formation'
            echo "<img src='uploaded_img/" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['nom']) . "' style='width:50px; height:50px; margin-right:10px;'>";
            echo "<span>" . htmlspecialchars($row['nom']) . " (" . htmlspecialchars($row['theme_nom']) . ")</span>";  // Le nom et le thème à côté de l'image
            echo "</a>";
            echo "</li>";
        }
    } else {
        echo "<li>Aucune formation trouvée</li>";
    }
}
?>