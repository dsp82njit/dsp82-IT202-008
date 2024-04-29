<?php
require(__DIR__ . "/../../partials/nav.php");


?>

<?php
$id = se($_GET, "id", -1, false);


$movie = [];
if ($id > -1) {
    //fetch
    $db = getDB();
    $query = "SELECT title, year, stars, created, modified FROM `MOVIE2` WHERE id = :id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":id" => $id]);
        $r = $stmt->fetch();
        if ($r) {
            $movie = $r;
        }
    } catch (PDOException $e) {
        error_log("Error fetching record: " . var_export($e, true));
        flash("Error fetching record", "danger");
    }
} else {
    flash("Invalid id passed", "danger");
    redirect("Movies.php");
}
foreach ($movie as $key => $value) {
    if (is_null($value)) {
        $movie[$key] = "N/A";
    }
}
?>
<div class="container-fluid">
    <h3>Movie: <?php se($movie, "title", "Unknown"); ?></h3>
    <div>
        <a href="<?php echo get_url("Movies.php"); ?>" class="btn btn-secondary">Back</a>
        <?php if (has_role("Admin")): ?>
            <a href="<?php echo get_url("admin/edit_movie.php?id=" . $id); ?>" class="btn btn-primary">Edit</a>
            <a href="<?php echo get_url("admin/delete_movie.php?id=" . $id); ?>" class="btn btn-danger">Delete</a>
        <?php endif;?>
    </div>

    <?php render_movie_card($movie); ?>

</div>


<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../partials/flash.php");
?>