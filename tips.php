<?php
// Place in: mindcare/api/tips.php
require_once '../config.php';
setHeaders();
$pdo = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET': handleGet($pdo); break;
    case 'POST': handlePost($pdo); break;
    case 'PUT': handlePut($pdo); break;
    case 'DELETE': handleDelete($pdo); break;
    default: jsonResponse(['error' => 'Method not allowed'], 405);
}

function handleGet($pdo) {
    $action = $_GET['action'] ?? 'all';
    switch ($action) {
        case 'all':
            $stmt = $pdo->query("SELECT * FROM tips WHERE is_active = 1 ORDER BY created_at DESC");
            jsonResponse(['success' => true, 'tips' => $stmt->fetchAll()]);
            break;
        case 'daily':
            $dayOfYear = date('z');
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM tips WHERE is_active = 1");
            $total = $stmt->fetch()['total'];
            if ($total == 0) jsonResponse(['error' => 'No tips'], 404);
            $offset = $dayOfYear % $total;
            $stmt = $pdo->prepare("SELECT * FROM tips WHERE is_active = 1 LIMIT 1 OFFSET ?");
            $stmt->execute([$offset]);
            jsonResponse(['success' => true, 'tip' => $stmt->fetch()]);
            break;
        case 'random':
            $excludeId = filter_input(INPUT_GET, 'exclude', FILTER_VALIDATE_INT) ?? 0;
            $stmt = $pdo->prepare("SELECT * FROM tips WHERE is_active = 1 AND id != ? ORDER BY RAND() LIMIT 1");
            $stmt->execute([$excludeId]);
            $tip = $stmt->fetch();
            $tip ? jsonResponse(['success' => true, 'tip' => $tip]) : jsonResponse(['error' => 'No tips'], 404);
            break;
        default: jsonResponse(['error' => 'Invalid action'], 400);
    }
}

function handlePost($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    $title = trim($input['title'] ?? '');
    $content = trim($input['content'] ?? '');
    $videoId = trim($input['video_id'] ?? '');
    if (empty($title) || empty($content)) jsonResponse(['error' => 'Title and content required'], 400);
    $stmt = $pdo->prepare("INSERT INTO tips (title, content, video_id) VALUES (?, ?, ?)");
    $stmt->execute([$title, $content, $videoId ?: null]);
    $newId = $pdo->lastInsertId();
    $stmt = $pdo->prepare("SELECT * FROM tips WHERE id = ?");
    $stmt->execute([$newId]);
    jsonResponse(['success' => true, 'message' => 'Tip added', 'tip' => $stmt->fetch()], 201);
}

function handlePut($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$id) jsonResponse(['error' => 'Invalid ID'], 400);
    $title = trim($input['title'] ?? '');
    $content = trim($input['content'] ?? '');
    $videoId = trim($input['video_id'] ?? '');
    if (empty($title) || empty($content)) jsonResponse(['error' => 'Title and content required'], 400);
    $stmt = $pdo->prepare("UPDATE tips SET title = ?, content = ?, video_id = ? WHERE id = ?");
    $stmt->execute([$title, $content, $videoId ?: null, $id]);
    $stmt->rowCount() > 0 ? jsonResponse(['success' => true]) : jsonResponse(['error' => 'Not found'], 404);
}

function handleDelete($pdo) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$id) jsonResponse(['error' => 'Invalid ID'], 400);
    $stmt = $pdo->prepare("UPDATE tips SET is_active = 0 WHERE id = ?");
    $stmt->execute([$id]);
    $stmt->rowCount() > 0 ? jsonResponse(['success' => true]) : jsonResponse(['error' => 'Not found'], 404);
}
?>