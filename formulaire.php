<?php
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $uploadDir = 'images/';
    $uploadFile = $uploadDir . basename($_FILES['image']['name']);
    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (!$email) {
        $message = 'Adresse e-mail invalide.';
    } elseif (file_exists($uploadFile)) {
        $message = 'Désolé, le fichier image existe déjà.';
    } elseif (!in_array($imageFileType, $allowedTypes)) {
        $message = 'Désolé, seuls les fichiers JPG, JPEG, PNG & GIF sont autorisés.';
    } elseif ($_FILES['image']['size'] > 5000000) { // 5MB
        $message = 'Désolé, votre fichier est trop volumineux.';
    } elseif (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
        $message = 'Le fichier ' . htmlspecialchars(basename($_FILES['image']['name'])) . ' a été téléversé avec succès.';
    } else {
        $message = 'Désolé, une erreur s\'est produite lors du téléversement de votre fichier.';
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
                <li><a href="index.html#presentation">Présentation</a></li>
                <li><a href="index.html#faune-flore">Faune et Flore</a></li>
                <li><a href="index.html#activites">Activités</a></li>
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
