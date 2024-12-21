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
            <p class="profile_content_p"><strong>Рейтинг</strong>
                <?php
                echo htmlspecialchars($_SESSION['user_data']['user_raiting']);

                ?>
            </p>
        </div>
    </div>
</section>