<?php

    $host = "localhost";
    $port = "5432";
    $database = "name of database";
    $user = "user of database";
    $password = "password"; 

    $dbHandle = pg_connect("host=$host port=$port dbname=$database user=$user password=$password");

    if (!$dbHandle) {
        die("An error occurred connecting to the database: " . pg_last_error());
    }
