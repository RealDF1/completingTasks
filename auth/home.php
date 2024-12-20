<?php

//Подключение элементов html, head, стилей и заголовков html-документа
include_once "content/content_header.php";
include_once "content/content_navBar.php";

?>

<div class="container mt-4">
    <h1 class="text-center">Выбор задания</h1>
    <div class="list-group">
        <?php
        $Session->getListOfTasks();

        ?>
    </div>
</div>

<?php
//Подключение футера документа
include_once "content/content_footer.php";
