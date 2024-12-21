<?php

//Подключение элементов html, head, стилей и заголовков html-документа
include_once "content/content_header.php";
include_once "content/content_navBar.php";

include_once "content/content_profile.php";
include_once "content/content_completedQueasts.php";

if ($_SESSION['user_data']['user_status'] === 'admin') {
    include_once "content/content_adminPanel.php";
}

//Подключение футера документа
include_once "content/content_footer.php";
