<?php
// Include necessary files and check user roles
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}

// Build search form
$form = [
    ["type" => "text", "name" => "title", "placeholder" => "Movie Title", "label" => "Movie Title", "include_margin" => false],
    ["type" => "select", "name" => "sort", "label" => "Sort", "options" => ["title" => "Title", "stars" => "Stars", "year" => "Year"], "include_margin" => false],
    ["type" => "select", "name" => "order", "label" => "Order", "options" => ["asc" => "+", "desc" => "-"], "include_margin" => false],
    ["type" => "number", "name" => "limit", "label" => "Limit", "value" => "10", "include_margin" => false],
];

// Initialize query and parameters
$query = "SELECT id, title, stars, year FROM `MOVIE2` WHERE 1=1";
$params = [];

// Process form data and update the query
if (isset($_GET['title']) && !empty($_GET['title'])) {
    $query .= " AND title LIKE :title";
    $params[':title'] = '%' . $_GET['title'] . '%';
}

// Fetch data from the database
$db = getDB();
$stmt = $db->prepare($query);
$results = [];
try {
    $stmt->execute($params);
    $results = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching movies: " . $e->getMessage());
    flash("Failed to fetch movies", "danger");
}

// Prepare table data
$table = [
    "data" => $results,
    "title" => "Movies",
    "ignored_columns" => ["id"],
    "view_url" => get_url("admin/view_movie.php"),
    "delete_url" => get_url("admin/delete_movie.php"),
    "edit_url"=> get_url("admin/edit_movie.php"),
];


// Render the page content
?>
<div class="container-fluid">
    <h3>List Movies</h3>
    <!-- Render search form -->
    <form method="GET">
        <div class="row mb-3" style="align-items: flex-end;">
            <?php foreach ($form as $field) : ?>
                <div class="col">
                    <?php render_input($field); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php render_button(["text" => "Search", "type" => "submit", "text" => "Filter"]); ?>
        <a href="?clear" class="btn btn-secondary">Clear</a>
    </form>

    <!-- Render table -->
    <?php render_table($table); ?>
</div>

<?php
// Include flash messages
require_once(__DIR__ . "/../../../partials/flash.php");
?>