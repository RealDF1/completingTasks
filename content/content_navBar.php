<?php

//Функция подключения панели навигации по сайту
include_once isset($_SESSION['user_data']) ? "content_navBarAuth.php" : "content_navBarUnAuth.php";