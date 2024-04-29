<?php
// Include necessary files and check user roles dsp82 4/17/2024
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}

$id = se($_GET, "id", -1, false);

// Fetch movie data if ID is provided
$movie = [];
if ($id > -1) {
    // Fetch movie from the database
    $db = getDB();
    $query = "SELECT title, year, stars, modified FROM `MOVIE2` WHERE id = :id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":id" => $id]);
        $movie = $stmt->fetch();
        if (!$movie) {
            flash("Movie not found", "danger");
            //die(header("Location:" . get_url("admin/list_movies.php")));
            redirect("admin/list_movies.php");
        }
    } catch (PDOException $e) {
        error_log("Error fetching movie record: " . var_export($e, true));
        flash("Error fetching movie record", "danger");
        die(header("Location:" . get_url("admin/list_movies.php")));
        redirect("admin/list_movies.php");
    }
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize form data
    $title = se($_POST, "title", "", false);
    $year = se($_POST, "year", "", false);
    $stars = se($_POST, "stars", "", false);

    // Update movie in the database dsp82 4/17/2024
    $db = getDB();
    $query = "UPDATE `MOVIE2` SET title = :title, year = :year, stars = :stars, modified = CURRENT_TIMESTAMP WHERE id = :id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":title" => $title, ":year" => $year, ":stars" => $stars, ":id" => $id]);
        flash("Movie updated successfully", "success");
        // Redirect to movie list page
        //header("Location: " . get_url("admin/list_movies.php"));
        redirect("admin/list_movies.php");
        exit();
    } catch (PDOException $e) {
        error_log("Error updating movie: " . var_export($e, true));
        flash("An error occurred while updating movie", "danger");
    }
}

// Render the form
$form = [
    ["type" => "text", "name" => "title", "label" => "Title", "placeholder" => "Movie Title", "value" => $movie["title"] ?? "", "rules" => ["required" => true]],
    ["type" => "number", "name" => "year", "label" => "Year", "placeholder" => "Release Year", "value" => $movie["year"] ?? "", "rules" => ["required" => true]],
    ["type" => "textarea", "name" => "stars", "label" => "Stars", "placeholder" => "Lead Stars", "value" => $movie["stars"] ?? "", "rules" => ["required" => true]],
];

?>

<div class="container-fluid">
    <h3>Edit Movie</h3>
    <div>
        <a href="<?php echo get_url("admin/list_movies.php"); ?>" class="btn btn-secondary">Back</a>
    </div>
    <form method="POST">
        <?php foreach ($form as $field) : ?>
            <?php render_input($field); ?>
        <?php endforeach; ?>
        <?php render_button(["text" => "Update Movie", "type" => "submit"]); ?>
    </form>
</div>

<?php
// Include flash messages dsp82 4/17/2024
require_once(__DIR__ . "/../../../partials/flash.php");
?>