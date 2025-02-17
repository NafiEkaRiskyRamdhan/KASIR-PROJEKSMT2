<?php

//utk menjalankan
session_start();

//menghapus seluruh data dlm sesion
session_unset();

//
session_destroy();

header('Location: login.php');
?>