<?php
require_once 'connect.php';

// Fetch images from database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Limit to 8 images for the homepage
    $stmt = $pdo->query("SELECT * FROM photos ORDER BY created_at DESC LIMIT 8");
    $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $photos = [];
    // Fallback to scanning directory if DB fails or is empty (optional, but good for transition)
    // For now, let's stick to DB as primary.
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Naturel de la Motte</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Bienvenue à l'Espace Naturel de la Motte</h1>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="#presentation">Présentation</a></li>
                <li><a href="#gallerie">Galerie</a></li>
                <li><a href="voter.php">Voter</a></li>
                <li><a href="formulaire.php">Participez</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section id="presentation">
            <h2>Présentation</h2>
            <p>L'Espace Naturel de la Motte est une réserve naturelle située en plein cœur de la région. Elle offre un habitat préservé pour une grande variété d'espèces animales et végétales.</p>
        </section>

        <section id="gallerie">
            <h2>Galerie d'images</h2>
            <div class="gallery-container">
                <?php if (count($photos) > 0): ?>
                    <?php foreach ($photos as $photo): ?>
                        <div class="gallery-item" onclick="openModal('<?php echo htmlspecialchars($photo['filename']); ?>', '<?php echo htmlspecialchars($photo['uploader_name']); ?>', '<?php echo htmlspecialchars($photo['uploader_email']); ?>')">
                            <img src="images/<?php echo htmlspecialchars($photo['filename']); ?>" alt="Photo de <?php echo htmlspecialchars($photo['uploader_name']); ?>">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucune image pour le moment. Soyez le premier à en poster une !</p>
                <?php endif; ?>
            </div>
        </section>

        <section id="actions">
            <h2>Rejoignez-nous !</h2>
            <div class="action-buttons">
                <a href="voter.php" class="button">Voter pour la meilleure photo</a>
                <a href="formulaire.php" class="button">Participer au concours</a>
            </div>
        </section>

        <section id="faune-flore">
            <h2>Faune et Flore</h2>
            <p>Découvrez la richesse de la biodiversité locale avec des espèces rares et protégées. Des sentiers balisés permettent d'observer la faune et la flore dans leur environnement naturel.</p>
        </section>
        <section id="activites">
            <h2>Activités</h2>
            <p>Participez à des activités éducatives, des randonnées guidées, et des ateliers de découverte pour toute la famille. Profitez également des aires de pique-nique et des espaces de détente.</p>
        </section>
    </main>
    <footer>
        <p>&copy; 2025 Espace Naturel de la Motte. Tous droits réservés.</p>
    </footer>

    <!-- Modal for Focus View -->
    <div id="imageModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="modal-content-wrapper">
            <img class="modal-content" id="modalImage">
            <div id="caption"></div>
        </div>
    </div>

    <script>
        function openModal(filename, uploaderName, uploaderEmail) {
            var modal = document.getElementById("imageModal");
            var modalImg = document.getElementById("modalImage");
            var captionText = document.getElementById("caption");
            
            modal.style.display = "block";
            modalImg.src = "images/" + filename;
            captionText.innerHTML = "Posté par : " + uploaderName;
            
            // Fetch vote count (AJAX could be used here, but for simplicity we might skip dynamic vote count on home modal for now or implement a simple fetch)
            // For now, just showing the image and uploader.
        }

        function closeModal() {
            var modal = document.getElementById("imageModal");
            modal.style.display = "none";
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            var modal = document.getElementById("imageModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
