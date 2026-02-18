<?php
require 'config.php';

/* ---------- 1. Vérifier la méthode POST ---------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash('error', 'Méthode HTTP non autorisée.');
    header('Location: index.php');
    exit;
}

/* ---------- 2. Récupérer l'ID de l'image à supprimer ---------- */
$id = $_POST['id'] ?? '';
if (!ctype_digit($id)) {
    set_flash('error', 'Identifiant d’image invalide.');
    header('Location: index.php');
    exit;
}

/* ---------- 3. Récupérer le chemin du fichier en base ---------- */
$stmt = $conn->prepare('SELECT filepath FROM images WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($filepath);
if ($stmt->fetch()) {
    $stmt->close();

    $fullPath = UPLOAD_DIR . $filepath;

    /* ---------- 4. Suppression du fichier physique ---------- */
    if (is_file($fullPath) && file_exists($fullPath)) {
        if (!unlink($fullPath)) {
            set_flash('error', 'Impossible de supprimer le fichier du disque.');
            header('Location: index.php');
            exit;
        }
    }

    /* ---------- 5. Suppression de la ligne BDD ---------- */
    $delStmt = $conn->prepare('DELETE FROM images WHERE id = ?');
    $delStmt->bind_param('i', $id);
    if ($delStmt->execute()) {
        set_flash('success', 'Image supprimée.');
    } else {
        set_flash('error', 'Erreur lors de la suppression en base de données.');
    }
    $delStmt->close();
} else {
    $stmt->close();
    set_flash('error', 'Image introuvable dans la base de données.');
}
header('Location: index.php');
exit;
?>
