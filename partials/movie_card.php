<?php if(!isset($movie)){
    error_log("using movie partial without data");
    flash("Dev Alert: Movie called without data", "danger");
}
?>
<?php if(isset($movie)) : ?>
    
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
            <?php if (!isset($movie["user_id"])) : ?>
            <div class="card body">
                <a href="<?php echo get_url('api/favorite_movie.php?movie_id=' . $movie["id"]); ?>" class="card-link">Favorite Movie</a>
            </div>
            <?php endif; ?>
        </div>
        </div>
        <?php endif; ?>