<?php

function render_input($data = array())
{
    include(__dir__ . "/../partials/input_field.php");
}

function render_button($data = array())
{
    include(__DIR__ . "/../partials/button.php");
}

function render_table($data = array())
{
    include(__DIR__ . "/../partials/table.php");
}
function render_movie_card($movie = array())
{
    include(__DIR__ . "/../partials/movie_card.php");
}

function render_result_counts($result_count, $total_count)
{
    include(__DIR__ . "/../partials/result_counts.php");
}
