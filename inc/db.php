<?php
// Simple SQLite helper for registrations
$baseDir = __DIR__ . '/../data';
if (!is_dir($baseDir)) {
    mkdir($baseDir, 0755, true);
}
$dbFile = $baseDir . '/registrations.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create table if it doesn't exist
$pdo->exec("CREATE TABLE IF NOT EXISTS registrations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    event_id INTEGER,
    event_title TEXT,
    name TEXT,
    email TEXT,
    phone TEXT,
    amount INTEGER,
    notes TEXT,
    status TEXT,
    stripe_session_id TEXT,
    created_at TEXT,
    updated_at TEXT
)");

// Ensure notes column exists in older DBs
$cols = $pdo->query("PRAGMA table_info(registrations);")->fetchAll(PDO::FETCH_ASSOC);
$colNames = array_column($cols, 'name');
if (!in_array('notes', $colNames)) {
    $pdo->exec("ALTER TABLE registrations ADD COLUMN notes TEXT;");
}

function insert_registration($pdo, $data) {
    $stmt = $pdo->prepare('INSERT INTO registrations (event_id, event_title, name, email, phone, amount, notes, status, stripe_session_id, created_at, updated_at) VALUES (:event_id, :event_title, :name, :email, :phone, :amount, :notes, :status, :stripe_session_id, :created_at, :updated_at)');
    $now = date('c');
    $stmt->execute([
        ':event_id' => $data['event_id'] ?? null,
        ':event_title' => $data['event_title'] ?? null,
        ':name' => $data['name'] ?? null,
        ':email' => $data['email'] ?? null,
        ':phone' => $data['phone'] ?? null,
        ':amount' => $data['amount'] ?? 0,
        ':notes' => $data['notes'] ?? null,
        ':status' => $data['status'] ?? 'pending',
        ':stripe_session_id' => $data['stripe_session_id'] ?? null,
        ':created_at' => $now,
        ':updated_at' => $now,
    ]);
    return (int)$pdo->lastInsertId();
}

function update_registration($pdo, $id, $fields) {
    $set = [];
    $params = [];
    foreach ($fields as $k => $v) {
        $set[] = "$k = :$k";
        $params[":$k"] = $v;
    }
    $params[':id'] = $id;
    $sql = 'UPDATE registrations SET ' . implode(', ', $set) . ', updated_at = :updated_at WHERE id = :id';
    $params[':updated_at'] = date('c');
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

function find_registration_by_id($pdo, $id) {
    $stmt = $pdo->prepare('SELECT * FROM registrations WHERE id = :id');
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function find_registration_by_session($pdo, $sessionId) {
    $stmt = $pdo->prepare('SELECT * FROM registrations WHERE stripe_session_id = :sid');
    $stmt->execute([':sid' => $sessionId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function find_registration_by_meta($pdo, $eventId, $email) {
    $stmt = $pdo->prepare('SELECT * FROM registrations WHERE event_id = :eid AND email = :email ORDER BY id DESC LIMIT 1');
    $stmt->execute([':eid' => $eventId, ':email' => $email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

return $pdo;
