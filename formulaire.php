<?php
require_once 'connect.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $uploadDir = 'images/';
    $fileName = basename($_FILES['image']['name']);
    
    // Rename file to avoid collisions and ensure safety (timestamp prefix)
    $newFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);
    $uploadFile = $uploadDir . $newFileName;
    
    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (!$email) {
        $message = 'Adresse e-mail invalide.';
    } elseif (empty($name)) {
        $message = 'Veuillez entrer votre nom.';
    } elseif (!in_array($imageFileType, $allowedTypes)) {
        $message = 'Désolé, seuls les fichiers JPG, JPEG, PNG & GIF sont autorisés.';
    } elseif ($_FILES['image']['size'] > 5000000) { // 5MB
        $message = 'Désolé, votre fichier est trop volumineux.';
    } elseif (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
        // Insert into database
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $pdo->prepare("INSERT INTO photos (filename, uploader_name, uploader_email) VALUES (?, ?, ?)");
            $stmt->execute([$newFileName, $name, $email]);
            
            $message = 'Le fichier a été téléversé avec succès et ajouté à la galerie !';
        } catch (PDOException $e) {
            $message = 'Erreur lors de l\'enregistrement en base de données : ' . $e->getMessage();
            // Optionally delete the file if DB insert fails
            unlink($uploadFile);
        }
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
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }
        .form-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .form-container input[type="text"],
        .form-container input[type="email"],
        .form-container input[type="file"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box; /* Fix padding issue */
        }
        .form-container input[type="submit"] {
            background-color: var(--primary-color);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1.1rem;
            width: 100%;
            transition: var(--transition);
        }
        .form-container input[type="submit"]:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            color: #fff;
            text-align: center;
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
                <li><a href="index.php#presentation">Présentation</a></li>
                <li><a href="index.php#gallerie">Galerie</a></li>
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
                    <label for="name">Votre nom :</label>
                    <input type="text" id="name" name="name" required placeholder="Jean Dupont">

                    <label for="email">Votre adresse e-mail :</label>
                    <input type="email" id="email" name="email" required placeholder="jean.dupont@example.com">
                    
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
