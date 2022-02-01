<?php 

session_start(); // On démarre une session ou reprend une session existante,
session_unset(); // on vide la session,
session_destroy(); // on détruit la session,
header("location:index.php"); // on est redirigé vers index.php,
exit(); // et on ferme la session