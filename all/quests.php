<?php

include_once "content/content_header.php";
include_once "content/content_navBar.php";

include_once "content/content_quest.php";

if (isset($_POST['code'])) {
    echo $Quest->checkCode($_POST['code']) ? $Quest->testCodeUser() : "<div class='m-auto' style='width: 70%'><h2 class='error'>Результат:</h2> <pre class='bg-light p-3 border'>Ошибка в окне с кодом!</pre></div>";
}

include_once "content/content_solution.php";
include_once "content/content_footer.php";