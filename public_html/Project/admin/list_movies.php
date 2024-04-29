<?php
/*
// Include necessary files and check user roles dsp82 4/17/2024
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    //die(header("Location: $BASE_PATH" . "/home.php"));
    redirect("home.php");
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
//dsp82 4/17/2024

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
*/


//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}

//build search form
$form = [
    ["type" => "text", "name" => "title", "placeholder" => "Movie Title", "label" => "Movie Title", "include_margin" => false],
    ["type" => "select", "name" => "sort", "label" => "Sort", "options" => ["title" => "Title", "stars" => "Stars", "year" => "Year"], "include_margin" => false],
    ["type" => "select", "name" => "order", "label" => "Order", "options" => ["asc" => "+", "desc" => "-"], "include_margin" => false],
    ["type" => "number", "name" => "limit", "label" => "Limit", "value" => "10", "include_margin" => false],
];


$total_records = get_total_count("`MOVIE2`");
$query = "SELECT id, title, stars, year FROM `MOVIE2` WHERE 1=1";
$params = [];
$session_key = $_SERVER["SCRIPT_NAME"];
$is_clear = isset($_GET["clear"]);
if ($is_clear) {
    session_delete($session_key);
    unset($_GET["clear"]);
    redirect($session_key);
} else {
    $session_data = session_load($session_key);
}

if (count($_GET) == 0 && isset($session_data) && count($session_data) > 0) {
    if ($session_data) {
        $_GET = $session_data;
    }
}
if (count($_GET) > 0) {
    session_save($session_key, $_GET);
    $keys = array_keys($_GET);

    foreach ($form as $k => $v) {
        if (in_array($v["name"], $keys)) {
            $form[$k]["value"] = $_GET[$v["name"]];
        }
    }
    //name
    $name = se($_GET, "name", "", false);
    if (!empty($name)) {
        $query .= " AND name like :name";
        $params[":name"] = "%$name%";
    }
  

    //sort and order
    $sort = se($_GET, "sort", "created", false);
    if (!in_array($sort, ["title", "stars", "year", "created", "modified"])) {
        $sort = "created";
    }
    $order = se($_GET, "order", "desc", false);
    if (!in_array($order, ["asc", "desc"])) {
        $order = "desc";
    }
    //IMPORTANT make sure you fully validate/trust $sort and $order (sql injection possibility)
    $query .= " ORDER BY $sort $order";
    //limit
    try {
        $limit = (int)se($_GET, "limit", "10", false);
    } catch (Exception $e) {
        $limit = 10;
    }
    if ($limit < 1 || $limit > 100) {
        $limit = 10;
    }
    //IMPORTANT make sure you fully validate/trust $limit (sql injection possibility)
    $query .= " LIMIT $limit";
}





$db = getDB();
$stmt = $db->prepare($query);
$results = [];
try {
    $stmt->execute($params);
    $r = $stmt->fetchAll();
    if ($r) {
        $results = $r;
    }
} catch (PDOException $e) {
    error_log("Error fetching stocks " . var_export($e, true));
    flash("Unhandled error occurred", "danger");
}
foreach ($results as $index => $movie) {
    foreach ($movie as $key => $value) {
        if (is_null($value)) {
            $results[$index][$key] = "N/A";
        }
    }
}
$table = [
    "data" => $results,
    "title" => "Movies",
    "ignored_columns" => ["id"],
    "view_url" => get_url("admin/view_movie.php"),
    "delete_url" => get_url("admin/delete_movie.php"),
    "edit_url"=> get_url("admin/edit_movie.php"),
    
];
?>
<div class="container-fluid">
    <h3>List Movies</h3>
    <form method="GET">
        <div class="row mb-3" style="align-items: flex-end;">

            <?php foreach ($form as $k => $v) : ?>
                <div class="col">
                    <?php render_input($v); ?>
                </div>
            <?php endforeach; ?>

        </div>
        <?php render_button(["text" => "Search", "type" => "submit", "text" => "Filter"]); ?>
        <a href="?clear" class="btn btn-secondary">Clear</a>
    </form>
    <?php render_result_counts(count($results), $total_records); ?>
    <?php render_table($table); ?>
</div>


<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>
