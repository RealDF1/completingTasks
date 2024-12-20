<?php

//Подключение элементов html, head, стилей и заголовков html-документа
include_once "content/content_header.php";
include_once "content/content_navBar.php";

?>

<section class="container mt-5">
    <h1 class="text-center mb-4">Профиль</h1>
    <div id="profile_wrapper" class="card">
        <div id="profile_content" class="card-body">
            <p class="profile_content_p"><strong>Логин:</strong>
                <?php
                echo htmlspecialchars($_SESSION['user_data']['user_login']);

                ?></p>
            <p class="profile_content_p"><strong>Дата регистрации:</strong>
                <?php
                echo htmlspecialchars($_SESSION['user_data']['user_data_reg']);

                ?>
            </p>
            <?php
            if ($_SESSION['user_data']['user_status'] === 'admin') {
                include_once "content/content_adminPanel.php";
            }
            ?>
        </div>
    </div>
</section>

<?php

//Подключение футера документа
include_once "content/content_footer.php";
