<?php
session_start();
header('Content-Type: application/json');
include 'config.php'; // Make sure this connects to your DB

// Check if manager is logged in
if (!isset($_SESSION['manager_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($data['action']) && $data['action'] === 'delete') {
        $id = intval($data['id']);
        $stmt = $conn->prepare("DELETE FROM staff WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    } else {
        // Add new staff
        $name = trim($data['name']);
        $shift_timing = trim($data['shift_timing']);
        $payroll = floatval($data['payroll']);

        if(empty($name) || empty($shift_timing) || $payroll <= 0){
            echo json_encode(['success'=>false,'message'=>'Invalid input']);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO staff (name, shift_timing, payroll) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $name, $shift_timing, $payroll);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }
}

// GET request – return all staff
$result = $conn->query("SELECT * FROM staff ORDER BY id DESC");
$staff = [];
while ($row = $result->fetch_assoc()) {
    $staff[] = $row;
}
echo json_encode($staff);
?>
