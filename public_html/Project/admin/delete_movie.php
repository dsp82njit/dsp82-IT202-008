<?php
session_start();
require(__DIR__ . "/../../../lib/functions.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}

$id = se($_GET, "id", -1, false);
if ($id < 1) {
    flash("Invalid id passed to delete", "danger");
    die(header("Location: " . get_url("admin/list_movies.php")));
}

$db = getDB();
$query = "DELETE FROM `MOVIE2` WHERE id = :id";
try {
    $stmt = $db->prepare($query);
    $stmt->execute([":id" => $id]);
    $affected_rows = $stmt->rowCount(); // Check the number of affected rows
    if ($affected_rows > 0) {
        flash("Deleted record with id $id", "success");
    } else {
        flash("No record found with id $id", "warning");
    }
} catch (PDOException $e) {
    error_log("Error deleting movie $id: " . $e->getMessage());
    flash("Error deleting record", "danger");
}

die(header("Location: " . get_url("admin/list_movies.php")));
?>