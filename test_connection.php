<?php

require_once 'includes/db.php';

if (isset($pdo)) {
    echo "PDO Created Successfully";
} else {
    echo "PDO Not Created";
}