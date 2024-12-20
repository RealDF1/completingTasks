<div id="log_form_wrapper" class="container mt-5" style="width: 40%">
    <form action="" method="post" class="border p-4 rounded bg-light shadow">
        <h2 class="text-center mb-4">Вход</h2>
        <div class="form-group">
            <input type="text" name="login" class="form-control" placeholder="Логин" required>
        </div>
        <div class="form-group">
            <input type="password" name="pass" class="form-control" placeholder="Пароль" required>
        </div>
        <button class="btn btn-primary btn-block" type="submit" name="login_user" value="login">Войти</button>
        <hr class="my-2">
        <div class="text-right">
            <button class="btn btn-secondary" type="submit" name="new_user" value="registration">Зарегистрироваться</button>
        </div>
        <div class="text-right mt-3">
            <a href="#" class="text-primary">Забыли пароль?</a>
        </div>
    </form>
</div>