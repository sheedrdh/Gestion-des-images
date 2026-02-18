<?php
/* config.php
 * ── Configuration globale, connexion BDD et fonctions utilitaires
 */

session_start();                     // nécessaire pour les flash‑messages

/* ---------- Paramètres de connexion ---------- */
define('DB_HOST', 'localhost');
define('DB_USER', 'root');            // XAMPP utilise "root" sans mot de passe par défaut
define('DB_PASS', '');
define('DB_NAME', 'image_gallery');

/* ---------- Constantes applicatives ---------- */
define('UPLOAD_DIR', __DIR__ . '/uploads/');   // Chemin absolu du dossier d'upload
define('MAX_FILE_SIZE', 5 * 1024 * 1024);      // 5 Mo
define('ALLOWED_EXTENSIONS', ['jpg','jpeg','png','gif']);

/* ---------- Connexion MySQLi ---------- */
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die('Erreur de connexion à la BDD : ' . $conn->connect_error);
}

/* ---------- Gestion des flash‑messages ---------- */
function set_flash(string $type, string $msg): void {
    // $type : 'success' ou 'error' (utilisé côté CSS)
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}
function get_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>
