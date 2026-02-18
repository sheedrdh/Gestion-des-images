<?php
require 'config.php';
$flash = get_flash();                     // Récupère un éventuel message flash
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestionnaire d'images</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Gestionnaire d'images</h1>

    <!-- ----- Affichage du flash (succès / erreur) ----- -->
    <?php if ($flash): ?>
        <div class="alert <?= htmlspecialchars($flash['type']) ?>">
            <?= htmlspecialchars($flash['msg']) ?>
        </div>
    <?php endif; ?>

    <!-- ----- Formulaire d'upload ----- -->
    <section class="upload-section">
        <h2>Uploader une image</h2>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <input type="file" name="image"
                   accept=".jpg,.jpeg,.png,.gif" required>
            <button type="submit">Envoyer</button>
        </form>
    </section>

    <!-- ----- Galerie d'images enregistrées ----- -->
    <section class="gallery">
        <h2>Images enregistrées</h2>
        <div class="grid">
            <?php
            $sql = "SELECT * FROM images ORDER BY uploaded_at DESC";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
                    $imgPath = 'uploads/' . $row['filepath'];   // chemin relatif pour le <img>
            ?>
                <div class="card">
                    <img src="<?= htmlspecialchars($imgPath) ?>"
                         alt="<?= htmlspecialchars($row['filename']) ?>">
                    <div class="info">
                        <p class="filename"><?= htmlspecialchars($row['filename']) ?></p>
                        <p class="date"><?= htmlspecialchars($row['uploaded_at']) ?></p>
                    </div>
                    <!-- Formulaire de suppression (POST) -->
                    <form action="delete.php" method="post"
                          onsubmit="return confirm('Supprimer cette image ?');">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button type="submit" class="delete-btn">Supprimer</button>
                    </form>
                </div>
            <?php
                endwhile;
            else:
            ?>
                <p>Aucune image enregistrée.</p>
            <?php endif; ?>
        </div>
    </section>
</div>
</body>
</html>
