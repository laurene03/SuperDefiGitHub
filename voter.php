<?php
require_once 'connect.php';

$message = '';
$messageType = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['photo_id'])) {
        $photoId = (int)$_POST['photo_id'];
        $voterIp = $_SERVER['REMOTE_ADDR'];
        
        $checkStmt = $pdo->prepare("SELECT id FROM votes WHERE voter_ip = ?");
        $checkStmt->execute([$voterIp]);
        
        if ($checkStmt->fetch()) {
            $message = "Vous avez déjà voté ! Un seul vote est autorisé par personne.";
            $messageType = "error";
        } else {
            // Insert vote
            $voteStmt = $pdo->prepare("INSERT INTO votes (photo_id, voter_ip) VALUES (?, ?)");
            $voteStmt->execute([$photoId, $voterIp]);
            $message = "Merci pour votre vote !";
            $messageType = "success";
        }
    }

    $stmt = $pdo->query("
        SELECT p.*, COUNT(v.id) as vote_count 
        FROM photos p 
        LEFT JOIN votes v ON p.id = v.photo_id 
        GROUP BY p.id 
        ORDER BY vote_count DESC, p.created_at DESC
    ");
    $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $message = "Erreur de base de données : " . $e->getMessage();
    $messageType = "error";
    $photos = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter - Espace Naturel de la Motte</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .vote-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
        }
        .vote-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }
        .vote-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .vote-info {
            padding: 15px;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .vote-count {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        .vote-btn {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1rem;
            transition: var(--transition);
        }
        .vote-btn:hover {
            background-color: #c08b5c;
        }
        .message-banner {
            max-width: 800px;
            margin: 20px auto;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            color: white;
            font-weight: bold;
        }
        .message-banner.success { background-color: #28a745; }
        .message-banner.error { background-color: #dc3545; }
    </style>
</head>
<body>
    <header>
        <h1>Bienvenue à l'Espace Naturel de la Motte</h1>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="index.php#presentation">Présentation</a></li>
                <li><a href="index.php#gallerie">Galerie</a></li>
                <li><a href="voter.php">Voter</a></li>
                <li><a href="formulaire.php">Participez</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section id="voter">
            <h2>Votez pour votre photo préférée</h2>
            <p>Soutenez les photographes en votant pour la plus belle image. Attention, un seul vote par personne !</p>

            <?php if ($message): ?>
                <div class="message-banner <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="gallery-container">
                <?php if (count($photos) > 0): ?>
                    <?php foreach ($photos as $photo): ?>
                        <div class="vote-card">
                            <img src="images/<?php echo htmlspecialchars($photo['filename']); ?>" alt="Photo de <?php echo htmlspecialchars($photo['uploader_name']); ?>">
                            <div class="vote-info">
                                <p>Posté par : <strong><?php echo htmlspecialchars($photo['uploader_name']); ?></strong></p>
                                <div class="vote-count"><?php echo $photo['vote_count']; ?> votes</div>
                                <form method="post" action="voter.php">
                                    <input type="hidden" name="photo_id" value="<?php echo $photo['id']; ?>">
                                    <button type="submit" class="vote-btn">Voter pour cette photo</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucune photo n'a encore été postée. Soyez le premier !</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <footer>
        <p>&copy; 2025 Espace Naturel de la Motte. Tous droits réservés.</p>
    </footer>
</body>
</html>
