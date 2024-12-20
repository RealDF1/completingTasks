<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand">
        <?php
        echo $Session->getPageName();

        ?>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Переключить навигацию">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <div class="ml-auto">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <form action="" method="POST" class="d-inline">
                        <button type="submit" name="link" value="home" class="nav-link nav-button btn btn-primary">Главная</button>
                    </form>
                </li>
                <li class="nav-item">
                    <form action="" method="POST" class="d-inline">
                        <button type="submit" name="link" value="about" class="nav-link nav-button btn btn-primary">О нас</button>
                    </form>
                </li>
                <li class="nav-item">
                    <form action="" method="POST" class="d-inline">
                        <button type="submit" name="link" value="auth" class="nav-link nav-button btn btn-primary">Личный кабинет</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>