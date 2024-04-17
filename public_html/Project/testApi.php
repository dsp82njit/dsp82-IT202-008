<?php
require(__DIR__ . "/../../partials/nav.php");

$result = [];
if (isset($_GET["symbol"])) {
    //function=GLOBAL_QUOTE&symbol=MSFT&datatype=json
    $data = ["query" => "Fast", "datatype" => "json"];
    $endpoint = "https://imdb188.p.rapidapi.com/api/v1/searchIMDB";
    $isRapidAPI = true;
    $rapidAPIHost = "imdb188.p.rapidapi.com";
    $result = get($endpoint, "MOVIE_API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    //example of cached data to save the quotas, don't forget to comment out the get() if using the cached data for testing
    /* $result = ["status" => 200, "response" => '{
    "Global Quote": {
        "01. symbol": "MSFT",
        "02. open": "420.1100",
        "03. high": "422.3800",
        "04. low": "417.8400",
        "05. price": "421.4400",
        "06. volume": "17861855",
        "07. latest trading day": "2024-04-02",
        "08. previous close": "424.5700",
        "09. change": "-3.1300",
        "10. change percent": "-0.7372%"
    }
}'];*/
    error_log("Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }
}

if(isset($result["Response"])){
    $result=$result["response"];
}

//foreach ($data as $movie) {
    // Check if the entry is a movie (has 'stars', 'Year', and 'title')
  //  if (isset($movie['stars']) && isset($movie['Year']) && isset($movie['title'])) {
        // Extract star, Year, and title
    //    $stars = $movie['stars'];
      //  $year = $movie['Year'];
        //$title = $movie['title'];

        // Output the extracted data
        //echo "Stars: $stars, Year: $year, Title: $title\n";
    //}
//}
$db =getDB();
$query ="INSERT INTO 'MOVIE2' ";
$columns=[];
$params=[];


foreach($result as $index => $row) {
    foreach ($row as $k => $v) {
        if($index === 0){
        array_push($columns, "$k");
        }
        if($k === "id") { 
            continue;
        }
        if ($k === "qid") { 
            continue;
        }
        if ($k === "q") { 
            continue;
        }
        if ($k === "image") { 
            continue;
        }
        
        $params[":$k$index"] = $v;
    }
}


unset($columns[0]);
unset($columns[1]);
unset($columns[5]);
unset($columns[6]);


$query .= "(" . implode(",", $columns) . ")";
    $query .= "VALUES (" . join(",",array_keys($params)) . ")";
    var_export($query);
    error_log(var_export($params, true));



    try{
        $stmt=$db->prepare($query);
        $stmt->execute($params);
        flash("Inserted record", "success");
    }
    catch(PDOException $e){
        error_log("Something broke with the query" . var_export($e, true));
    }

?>
<div class="container-fluid">
    <h1>Hotel Info</h1>
    <p>Remember, we typically won't be frequently calling live data from our API, this is merely a quick sample. We'll want to cache data in our DB to save on API quota.</p>
    <form>
        <div>
            <label>Symbol</label>
            <input name="symbol" />
            <input type="submit" value="Fetch Stock" />
        </div>
    </form>
    <div class="row ">
        <?php if (isset($result)) : ?>
            <?php foreach ($result as $stock) : ?>
                <pre>
                    <?php var_export($stock);?>
                </pre>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php
require(__DIR__ . "/../../partials/flash.php");