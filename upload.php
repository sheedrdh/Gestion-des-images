<?php
require 'config.php';

/* ---------- 1. Vérifier le type de requête ---------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash('error', 'Méthode HTTP non autorisée.');
    header('Location: index.php');
    exit;
}

/* ---------- 2. Vérifier la présence du fichier ---------- */
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    set_flash('error', 'Aucun fichier reçu ou erreur pendant le transfert.');
    header('Location: index.php');
    exit;
}

/* ---------- 3. Récupérer les informations ---------- */
$fileTmpPath = $_FILES['image']['tmp_name'];
$originalName = $_FILES['image']['name'];
$fileSize    = $_FILES['image']['size'];

/* ---------- 4. Taille maximale ---------- */
if ($fileSize > MAX_FILE_SIZE) {
    set_flash('error', 'Le fichier dépasse la taille maximale autorisée (5 Mo).');
    header('Location: index.php');
    exit;
}

/* ---------- 5. Extension autorisée ---------- */
$pathInfo   = pathinfo($originalName);
$extension  = strtolower($pathInfo['extension'] ?? '');
if (!in_array($extension, ALLOWED_EXTENSIONS, true)) {
    set_flash('error', 'Extension non autorisée. Types acceptés : jpg, jpeg, png, gif.');
    header('Location: index.php');
    exit;
}

/* ---------- 6. Vérification MIME réelle ---------- */
$finfo   = finfo_open(FILEINFO_MIME_TYPE);
$mime    = finfo_file($finfo, $fileTmpPath);
finfo_close($finfo);
$allowedMimes = ['image/jpeg','image/png','image/gif'];
if (!in_array($mime, $allowedMimes, true)) {
    set_flash('error', 'Le type MIME du fichier ne correspond pas à une image valide.');
    header('Location: index.php');
    exit;
}

/* ---------- 7. Nettoyage du nom de base ---------- */
$baseName = preg_replace('/[^A-Za-z0-9_-]/', '_', $pathInfo['filename']);
$targetFileName = $baseName . '.' . $extension;
$targetPath = UPLOAD_DIR . $targetFileName;

/* ---------- 8. Gestion des doublons (renommage incrémental) ---------- */
$counter = 1;
while (file_exists($targetPath)) {
    $targetFileName = $baseName . '_' . $counter . '.' . $extension;
    $targetPath = UPLOAD_DIR . $targetFileName;
    $counter++;
}

/* ---------- 9. Déplacement du fichier ---------- */
if (!move_uploaded_file($fileTmpPath, $targetPath)) {
    set_flash('error', 'Impossible de déplacer le fichier sur le serveur.');
    header('Location: index.php');
    exit;
}

/* ---------- 10. Enregistrement en base ---------- */
$escapedOrigName = $conn->real_escape_string($originalName);
$escapedPath     = $conn->real_escape_string($targetFileName);
$sql = "INSERT INTO images (filename, filepath) VALUES ('$escapedOrigName', '$escapedPath')";
if ($conn->query($sql) === true) {
    set_flash('success', 'Image téléchargée avec succès.');
} else {
    // Nettoyage du fichier si la requête échoue
    unlink($targetPath);
    set_flash('error', 'Erreur BDD : ' . $conn->error);
}
header('Location: index.php');
exit;
?>
