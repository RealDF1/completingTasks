<?php
include_once "content/content_header.php";

?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="collapse navbar-collapse" id="navbarNav">
        <div class="mx-auto"> <!-- Центрируем текст -->
            <a class="navbar-brand">Авторизация</a>
        </div>
        <div class="ml-auto"> <!-- Выравниваем кнопку вправо -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <form action="" method="POST" class="d-inline">
                        <button type="submit" name="link" value="home" class="nav-link nav-button btn btn-primary">
                            Вернуться на главную
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<?php
include_once "content/content_auth.php";

//Подключение футера документа
include_once "content/content_footer.php";
