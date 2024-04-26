<?php
/*
//note we need to go up 1 more directory dsp82 4/17/2024
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}
?>

<?php
$id = se($_GET, "id", -1, false);

$movie = [];
if ($id > -1) {
    //fetch
    $db = getDB();
    $query = "SELECT title, stars, year FROM `MOVIE2` WHERE id = :id";
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
    die(header("Location:" . get_url("admin/list_movies.php")));
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
        <a href="<?php echo get_url("admin/list_movies.php"); ?>" class="btn btn-secondary">Back</a>
        <?php if (has_role("Admin")): ?>
            <a href="<?php echo get_url("admin/edit_movie.php?id=" . $id); ?>" class="btn btn-primary">Edit</a>
            <a href="<?php echo get_url("admin/delete_movie.php?id=" . $id); ?>" class="btn btn-danger">Delete</a>
        <?php endif;?>
    </div>
    <div class="card mx-auto" style="width: 18rem;">
        <img src="https://example.com/<?php se($movie, "title", "Unknown"); ?>.jpg" class="card-img-top" alt="...">
        <div class="card-body">
            <h5 class="card-title"><?php se($movie, "title", "Unknown"); ?></h5>
            <div class="card-text">
                <ul class="list-group">
                    <li class="list-group-item">Stars: <?php se($movie, "stars", "Unknown"); ?></li>
                    <li class="list-group-item">Year: <?php se($movie, "year", "Unknown"); ?></li>
                </ul>
            </div>
        </div>
    </div>
    <?php render_movie_card($movie); ?>
</div>

<?php
//note we need to go up 1 more directory dsp82 4/17/2024
require_once(__DIR__ . "/../../../partials/flash.php");
?>

*/

//note we need to go up 1 more directory

//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}
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
    redirect("admin/list_movies.php");
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
        <a href="<?php echo get_url("admin/list_movies.php"); ?>" class="btn btn-secondary">Back</a>
        <?php if (has_role("Admin")): ?>
            <a href="<?php echo get_url("admin/edit_movie.php?id=" . $id); ?>" class="btn btn-primary">Edit</a>
            <a href="<?php echo get_url("admin/delete_movie.php?id=" . $id); ?>" class="btn btn-danger">Delete</a>
        <?php endif;?>
    </div>

    <?php render_movie_card($movie); ?>

</div>


<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>