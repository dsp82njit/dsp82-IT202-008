<?php
/*

require(__DIR__ . "/../../../partials/nav.php");


//build search form
$form = [
    ["type" => "text", "name" => "Username", "placeholder" => "Username", "label" => "Username", "include_margin" => false],
    ["type" => "text", "name" => "title", "placeholder" => "Movie Title", "label" => "Movie Title", "include_margin" => false],
    //["type" => "number", "name" => "year", "placeholder" => "Year", "label" => "Year", "include_margin" => false],
    //["type" => "number", "name" => "stars", "placeholder" => "Stars", "label" => "Stars", "include_margin" => false],
    ["type" => "select", "name" => "sort", "label" => "Sort", "options" => ["title" => "Title", "year" => "Year", "stars" => "Stars", "created" => "Created", "modified" => "Modified"], "include_margin" => false],
    ["type" => "select", "name" => "order", "label" => "Order", "options" => ["asc" => "+", "desc" => "-"], "include_margin" => false],
    ["type" => "number", "name" => "limit", "label" => "Limit", "value" => "10", "include_margin" => false],
];



$query = "SELECT b.id, title, stars, year, user_id FROM `MOVIE2` b
JOIN `UserMovie2` ub ON b.id = ub.movie_id JOIN Users on u.id = ub.user_id";
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
    //username
    $username = se($_GET, "username", "", false);
    if (!empty($username)) {
        $query .= " AND u.username like :username";
        $params[":username"] = "%$name%";
    }
    //name
    $name = se($_GET, "name", "", false);
    if (!empty($name)) {
        $query .= " AND name like :name";
        $params[":name"] = "%$username%";
    }
   
    //sort and order
    $sort = se($_GET, "sort", "created", false);
    if (!in_array($sort, ["title", "year", "stars", "created", "modified"])) {
        $sort = "created";
    }
    //tell mysql I care about the data from table "b"
    if ($sort === "created" || $sort === "modified") {
        $sort = "b." . $sort;
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
    error_log("Error fetching movies " . var_export($e, true));
    flash("Unhandled error occurred", "danger");
}
foreach ($results as $index => $broker) {
    foreach ($broker as $key => $value) {
        if (is_null($value)) {
            $results[$index][$key] = "N/A";
        }
    }
}

$table = [
    "data" => $results, "title" => "Movies", "ignored_columns" => ["id"],
    "view_url" => get_url("Movie.php"),
];
?>
<div class="container-fluid">
    <h3>My Movies</h3>
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
    <div class="row w-100 row-cols-auto row-cols-sm-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 g-4">
        <?php foreach ($results as $movie) : ?>
            <div class="col">
                <?php render_movie_card($movie); ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>


<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>
*/

require(__DIR__ . "/../../../partials/nav.php");


//build search form //dsp82 4/24/2024
$form = [
    ["type" => "text", "name" => "Username", "placeholder" => "Username", "label" => "Username", "include_margin" => false],
    ["type" => "text", "name" => "title", "placeholder" => "Movie Title", "label" => "Movie Title", "include_margin" => false],
    //["type" => "number", "name" => "year", "placeholder" => "Year", "label" => "Year", "include_margin" => false],
    //["type" => "number", "name" => "stars", "placeholder" => "Stars", "label" => "Stars", "include_margin" => false],
    ["type" => "select", "name" => "sort", "label" => "Sort", "options" => ["title" => "Title", "year" => "Year", "stars" => "Stars", "created" => "Created", "modified" => "Modified"], "include_margin" => false],
    ["type" => "select", "name" => "order", "label" => "Order", "options" => ["asc" => "+", "desc" => "-"], "include_margin" => false],
    ["type" => "number", "name" => "limit", "label" => "Limit", "value" => "10", "include_margin" => false],
];

//error_log("Form data: " . var_export($form, true));

$total_records = get_total_count("`MOVIE2` b
JOIN `UserMovie2` ub ON b.id = ub.movie_id");


$query = "SELECT u.username, b.id, title, stars, year, user_id FROM `MOVIE2` b
JOIN `UserMovie2` ub ON b.id = ub.movie_id JOIN Users u on u.id = ub.user_id";
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
    //username
    $username = se($_GET, "username", "", false);
    if (!empty($username)) {
        $query .= " AND u.username like :username";
        $params[":username"] = "%$username%";
    }
    //name dsp82 4/24/2024
    $name = se($_GET, "name", "", false);
    if (!empty($name)) {
        $query .= " AND name like :name";
        $params[":name"] = "%$name%";
    }
    

    //sort and order
    $sort = se($_GET, "sort", "created", false);
    if (!in_array($sort, ["title", "year", "stars", "created", "modified"])) {
        $sort = "created";
    }
    //tell mysql I care about the data from table "b"
    if ($sort === "created" || $sort === "modified") {
        $sort = "b." . $sort;
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
    //IMPORTANT make sure you fully validate/trust $limit (sql injection possibility) dsp82 4/24/2024
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
    "data" => $results, "title" => "Movies", "ignored_columns" => ["id"],
    "view_url" => get_url("Movie.php"),
];
?>
<div class="container-fluid">
    <h3>Associated Movies</h3>
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
    <div class="row w-100 row-cols-auto row-cols-sm-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 g-4">
        <?php foreach ($results as $movie) : ?>
            <div class="col">
                <?php render_movie_card($movie); ?>
            </div>
        <?php endforeach; ?>
        <?php if (count($results) === 0) : ?>
            <div class="col">
                No results to show
            </div>
        <?php endif; ?>
    </div>
</div>


<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>