<?php

    $host = "localhost";
    $port = "5432";
    $database = "zur5ms";
    $user = "zur5ms";
    $password = "jHeT37VG_sdi"; 

    $dbHandle = pg_connect("host=$host port=$port dbname=$database user=$user password=$password");

    if (!$dbHandle) {
        die("An error occurred connecting to the database: " . pg_last_error());
    }
