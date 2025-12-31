<?php
require_once 'connect.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom'] ?? 'Anonyme'); 
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    
    $uploadDir = 'images/';
    $nomFichier = basename($_FILES['image']['name']);
    $uploadFile = $uploadDir . $nomFichier;
    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

    if (!$email) {
        $message = 'Adresse e-mail invalide.';
    } else {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            
try {
    $sql = "INSERT INTO photos (uploader_name, filename, uploader_email) 
            VALUES (:nom, :adresse, :mail)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom'     => $nom,
        ':adresse' => $nomFichier, // On enregistre seulement le nom du fichier
        ':mail'    => $email
    ]);
    $message = 'Succès ! Votre participation a bien été ajoutée à la base de données.';
} catch (PDOException $e) {
    $message = 'Erreur SQL : ' . $e->getMessage();
}

        } else {
            $message = 'Erreur lors du transfert de l\'image.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participez - Espace Naturel de la Motte</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
        }
        .form-container input[type="email"],
        .form-container input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .form-container input[type="submit"] {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
        .form-container input[type="submit"]:hover {
            background-color: var(--secondary-color);
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            color: #fff;
        }
        .message.success { background-color: #28a745; }
        .message.error { background-color: #dc3545; }
    </style>
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
        <section id="participez">
            <h2>Participez en partageant vos photos</h2>
            <p>Envoyez-nous vos plus belles photos de l'Espace Naturel de la Motte. Les meilleures seront publiées sur notre site !</p>

            <div class="form-container">
                <?php if ($message): ?>
                    <div class="message <?php echo strpos($message, 'succès') !== false ? 'success' : 'error'; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                <form action="formulaire.php" method="post" enctype="multipart/form-data">
                    <label for="email">Votre adresse e-mail :</label>
                    <input type="email" id="email" name="email" required>
                    
                    <label for="image">Sélectionnez une image :</label>
                    <input type="file" id="image" name="image" accept="image/*" required>
                    
                    <input type="submit" value="Envoyer ma photo">
                </form>
            </div>

        </section>
    </main>
    <footer>
        <p>&copy; 2025 Espace Naturel de la Motte. Tous droits réservés.</p>
    </footer>
</body>
</html>
