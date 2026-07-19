<?php
$config = include 'c:\Aadhira_erp_v_1.0\app\pos_system\bootstrap\cache\config.php';
$host = $config['database']['connections']['mysql']['host'];
$db = $config['database']['connections']['mysql']['database'];
$user = $config['database']['connections']['mysql']['username'];
$pass = $config['database']['connections']['mysql']['password'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Update header_text where it's NULL or empty
    $stmt = $pdo->prepare("UPDATE invoice_layouts SET header_text = 'Mahdev (Pvt) Ltd' WHERE header_text IS NULL OR header_text = ''");
    $stmt->execute();

    echo "Updated header_text in invoice_layouts table.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>