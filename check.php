<?php
$pdo = new PDO('sqlite:'.__DIR__.'/database/database.sqlite');
$res = $pdo->query('PRAGMA table_info(users)');
foreach($res as $row){
    echo $row['name']."\n";
}
