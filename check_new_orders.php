<?php
session_start();
require_once 'database/db_connect.php';

// Vérification de l'admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Récupérer le timestamp de la dernière vérification
$last_check = isset($_SESSION['last_order_check']) ? $_SESSION['last_order_check'] : date('Y-m-d H:i:s', strtotime('-1 minute'));

try {
    // Compter les nouvelles commandes
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as new_orders
        FROM orders
        WHERE created_at > ?
    ");
    $stmt->execute([$last_check]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Mettre à jour le timestamp de la dernière vérification
    $_SESSION['last_order_check'] = date('Y-m-d H:i:s');

    echo json_encode([
        'new_orders' => $result['new_orders']
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 