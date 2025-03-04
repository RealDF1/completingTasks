<?php

namespace Scripts;

use Scripts\Interfaces\BDSession;

class SESSION
{

    private BDSession $BD;

    public function __construct(BDSession $BD)
    {
        $this->BD = $BD;
    }

    // Функция регистрации пользователя
    public function setUserRegistration(): void
    {
        $newUserLogin = trim(htmlspecialchars($_POST['login']));
        $newUserPass = password_hash(trim(htmlspecialchars($_POST['pass'])), PASSWORD_DEFAULT);

        if (
            !isset($_POST['new_user'])
            || !isset($_POST['login'])
            || !isset($_POST['pass'])
        ) {
            return;
        }

        // Проверка на существующий логин
        if (isset($this->BD->getUsersName($newUserLogin)['user_login'])) {
            return;
        }

        $this->BD->setNewUserOnBD($newUserLogin, $newUserPass);
    }

    // Шапка задания при выполнении
    public function getHeaderTask(): void
    {
        if (!isset($_SESSION['task_id'])) {
            return;
        }

        $result = $this->BD->getHeaderTaskQuery();

        echo "<h1 class='text-center'>" . $result['name_task'] . "</h1>";
        echo "<div class='alert alert-info m-auto' style='width: 70%' role='alert'><h4 class=alert-heading'>Задание:</h4><p>" . $result['body_task'] . ". </p><p>Заголовок функции " . $result['function_name'] . "(" . $result["arguments_function"] . ") <br>Результат выполнения функции должен быть тип " . $result['type'] . " </p></div>";
    }

    // Шаблон при написании кода
    public function getPatternForCodeSpace(): string
    {
        $result = $this->BD->getPatternForCodeSpaceQuery();
        return "function " . $result['function_name'] . "(" . $result["arguments_function"] . "):" . $result['type'] . " {\n\n}";
    }

    // Старинца с заданиями
    public function getListOfTasks(): void
    {
        $result = $this->BD->getListOfTasksQuery();

        foreach ($result as $task) {
            echo "
        <form action=\"\" method=\"post\">
            <input type=\"hidden\" name=\"task_id\" value=\"" . $task["id"] . "\">
            <button type=\"submit\" class=\"list-group-item list-group-item-action\" name=\"link\" value=\"quests\">" . $task["name_task"] . "<br>" . $task["body_task"] . "</button>
        </form>
        ";
        }
    }

    // Функция авторизации пользователя
    public function getUserAuthorization(): void
    {
        if (
            !isset($_POST['login_user'])
            || !isset($_POST['login'])
            || !isset($_POST['pass'])
        ) {
            return;
        }

        $userLogin = trim(htmlspecialchars($_POST['login']));
        $userPass = trim(htmlspecialchars($_POST['pass']));

        $result_arr = $this->BD->findCreatedUser($userLogin);

        $user_login_db = $result_arr['user_login'];
        $user_pass_db = $result_arr['user_pass'];

        if (
            $userLogin !== $user_login_db
            || !password_verify($userPass, $user_pass_db)
        ) {
            return;
        }

        $_SESSION['user_data']['user_id'] = $result_arr['user_id'];
        $_SESSION['user_data']['user_login'] = $result_arr['user_login'];
        $_SESSION['user_data']['user_status'] = $result_arr['user_status'];
        $_SESSION['user_data']['user_data_reg'] = $result_arr['user_data_reg'];
        $_SESSION['user_data']['user_raiting'] = $result_arr['raiting'];
        header('Location: /');
    }

    // Функция проверки пользователя на предмет авторизации
    private function getVerifyUser($user_data): string
    {
        return isset($user_data) ? 'auth/' : 'all/';
    }

    // Функция проверки пути для дальнейшего создания полного относительного пути от текущего каталога
    private function getVerifyPath(): string
    {
        return $_SERVER['REQUEST_URI'] === '/' ? '' : '../';
    }

    // Список рейтинга пользователей
    public function getRaitingList()
    {
        $answer = "";
        $result = $this->BD->getRaitingListQueary();

        foreach ($result as $item) {
            $answer .= "
            <tr>
                <td>" . $item['user_login'] . "</td>
                <td> ⭐ " . $item['raiting'] . "</td>
            </tr>";
        }
        return $answer;
    }

    // Функция подключения контента страницы в зависимости от выбранного пути
    public function getPathToPage(): string
    {
        $folder = $this->getVerifyUser($_SESSION['user_data']);
        $dir = $this->getVerifyPath();
        $link = $_POST['link'];

        if ($link == '' || !isset($link)) {
            return $folder . '/home.php';
        }

        return $link === 'exit' ? 'all/home.php' : $dir . $folder . $link . ".php";
    }

    // Функция уничтожения сессии
    public function sessionDestroy(): void
    {
        if ($_POST['link'] !== 'exit') {
            return;
        }

        $_SESSION = [];
        $_POST['link'] = null;
        $_POST = null;
        unset($_COOKIE[session_name()]);
        session_destroy();
        header('Location: /');
    }

    // Функция определения имени страницы в заголовке вкладки
    public function getPageName(): string
    {
        $page_name = $_POST['link'];

        return match ($page_name) {
            $page_name === 'about' => 'Информация о создателе',
            $page_name === 'auth' => 'Авторизация',
            $page_name === 'quests' => 'Практическое задание PHP',
            $page_name === 'profile' => '',
            $page_name === 'raiting' => 'Рейтинг',
            default => 'PHP задания',
        };
    }
}