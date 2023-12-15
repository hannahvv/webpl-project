<?php
session_start();
session_destroy();
header("Location: front_controller.php?page=index");
exit;
