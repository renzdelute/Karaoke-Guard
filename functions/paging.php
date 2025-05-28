<?php
require_once 'db.php';

// Default current page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$records_per_page = 6; // Number of records to show per page

// Calculate the offset for the query
$offset = ($page - 1) * $records_per_page;

// Count total karaoke items
$total_stmt = $conn->prepare("SELECT COUNT(*) FROM karaoke");
$total_stmt->execute();
$total_karaokes = $total_stmt->fetchColumn();

// Calculate total pages
$total_pages = ceil($total_karaokes / $records_per_page);

// Fetch current page karaoke records
$stmt = $conn->prepare("SELECT * FROM karaoke ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$karaokes = $stmt->fetchAll(PDO::FETCH_ASSOC);
