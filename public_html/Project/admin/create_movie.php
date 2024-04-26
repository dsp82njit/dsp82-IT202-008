<?php
// Include necessary files and check user roles
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    //die(header("Location: $BASE_PATH" . "/home.php"));
    redirect("home.php");
}
//dsp82 4/17/2024
?>

<?php

// Function to check for duplicate movie titles
function isDuplicateMovieTitle($title)
{
    $db = getDB();
    $query = "SELECT COUNT(*) FROM `MOVIE2` WHERE `title` = :title";
    $stmt = $db->prepare($query);
    $stmt->execute([":title" => $title]);
    $count = $stmt->fetchColumn();
    return $count > 0;
}

// Handle movie data operations
if (isset($_POST["name"]) && isset($_POST["description"]) && isset($_POST["stars"])) {
    $title = se($_POST, "name", "", false);
    $year = se($_POST, "description", "", false);
    $stars = se($_POST, "stars", "", false);
    if (empty($title)) {
        flash("Title is required", "warning");
    } else {
        if (isDuplicateMovieTitle($title)) {
            flash("A movie with the title '$title' already exists. Please choose a different title.", "warning");
        } else {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO `MOVIE2` (title, year, stars) VALUES(:title, :year, :stars)");
            try {
                $stmt->execute([":title" => $title, ":year" => $year, ":stars" => $stars]);
                flash("Successfully created movie '$title'!", "success");
            } catch (PDOException $e) {
                if ($e->errorInfo[1] === 1062) {
                    flash("A movie with this title already exists, please try another", "warning");
                } else {
                    flash("Unknown error occurred, please try again", "danger");
                    error_log(var_export($e->errorInfo, true));
                }
            }
        }
    }
}//dsp82 4/17/2024
?>

<div class="container-fluid">
    <h3>Create Movie</h3>
    <form method="POST">
        <div class="form-group">
            <label for="name">Title</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="description">Year</label>
            <input type="number" class="form-control" id="description" name="description" required>
        </div>
        <div class="form-group">
            <label for="stars">Stars</label>
            <input type="text" class="form-control" id="stars" name="stars" required>
        </div>
        <input type="hidden" name="action" value="create">
        <button type="submit" class="btn btn-primary">Create Movie</button>
    </form>
</div>

<?php
// Include flash messages
require_once(__DIR__ . "/../../../partials/flash.php");
?>