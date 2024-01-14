<?php

// Read JSON file content
$jsonData = file_get_contents('statsCountry.json');

// Decode JSON data
$data = json_decode($jsonData, true);

// Calculate the total amount of numbers
$total = array_sum($data);

// Echo the total
echo "Status: $total";

?>
