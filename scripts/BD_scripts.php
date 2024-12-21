<?php

class BD
{
    protected mysqli|false $connect;

    protected static $_instance;

    public static function getInstance(): BD
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    private function __construct()
    {
        $this->connect = mysqli_connect('localhost', 'root', '', 'BKP') or die('Ошибка подключения: ' . mysqli_connect_errno());
    }

    // Список правильных решений
    public function getCompletedCode($user_id = false): mysqli_result|bool
    {
        $listCompletedTasksQueryTask = "SELECT * FROM `tasks_complete` WHERE id_task='" . $_SESSION['task_id'] . "';";
        $listCompletedTasksQueryUser = "SELECT tasks_complete.*, tasks.name_task FROM tasks_complete JOIN tasks ON tasks_complete.id_task = tasks.id WHERE tasks_complete.id_user = '" . $user_id . "';";;

        return mysqli_query($this->connect, $user_id ? $listCompletedTasksQueryUser : $listCompletedTasksQueryTask);
    }

    // Добавлнеи рейтинга
    public function setRaitingUserOnComplete($addRating)
    {
        $addRaitingQuery = "UPDATE `users` SET `raiting` = `raiting` + $addRating WHERE `user_id` = " . $_SESSION['user_data']['user_id'];
        mysqli_query($this->connect, $addRaitingQuery);
    }

    // Добавление лайка
    public function setLikeQuery(): bool|mysqli_result
    {
        return mysqli_query($this->connect, "UPDATE `tasks_complete` SET `likes`=`likes`+'1' WHERE id = '" . $_POST['like'] . "'");
    }

    // Добавление задания
    public function setTaskToTable(): bool|mysqli_result
    {
        $addTaskQuery = sprintf(
            "INSERT INTO `tasks`(`id`, `name_task`, `body_task`, `answer_task`, `function_name`, `type`, `function_test`, `arguments_function`, `id_creatorUsers`) VALUES (
            NULL,
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            %s);",
            $_POST['name_task'],
            $_POST['body_task'],
            isset($_POST['task_answer']) ? $_POST['task_answer'] : null,
            $_POST['function_name'],
            $_POST['type'],
            isset($_POST["function_test"]) ? $_POST["function_test"] : null,
            isset($_POST["arguments_function"]) ? $_POST["arguments_function"] : null,
            $_SESSION['user_data']['user_id']
        );

        print_r($addTaskQuery);
        return mysqli_query($this->connect, $addTaskQuery);
    }

    public function getRaitingListQueary() {
        return mysqli_query($this->connect, "SELECT * FROM users ORDER BY `raiting` DESC LIMIT 10;");
    }

    // Запись в таблицу при успешном прохождении
    public function setCompletedCodeToTable(): void
    {
        $add_query = "INSERT INTO `tasks_complete` (`id`, `code`, `likes`, `id_user`, `id_task`) VALUES (NULL, '" . $_POST['code'] . "', 0, '" . $_SESSION['user_data']['user_id'] . "', '" . $_SESSION['task_id'] . "');";
        mysqli_query($this->connect, $add_query);
    }

    public function getConnection(): bool|mysqli
    {
        return $this->connect;
    }

    // Страница выбранного задания
    public function get_quest($task_id): bool|array|null
    {
        $quest_query = "SELECT * FROM tasks WHERE id = " . $task_id;
        return mysqli_query($this->connect, $quest_query)->fetch_assoc();
    }

    public function findCreatedUser($userLogin): bool|array|null
    {
        return mysqli_fetch_assoc(mysqli_query($this->connect, "SELECT * FROM users WHERE user_login = '" . $userLogin . "'"));
    }

    public function getPatternForCodeSpaceQuery(): bool|array|null
    {
        return mysqli_query($this->connect, "SELECT * FROM tasks WHERE id = " . $_SESSION['task_id'])->fetch_assoc();
    }

    // Старинца с заданиями
    public function getListOfTasksQuery(): bool|mysqli_result
    {
        return mysqli_query($this->connect, "select * from tasks");
    }

    public function getHeaderTaskQuery(): bool|array|null
    {
        return mysqli_query($this->connect, "SELECT * FROM tasks WHERE id = " . $_SESSION['task_id'])->fetch_assoc();
    }

    public function setNewUserOnBD($newUserLogin, $newUserPass): void
    {
        mysqli_query($this->connect, "INSERT INTO users VALUES (null, '" . $newUserLogin . "', '" . $newUserPass . "', '" . date('Y-m-d H:i:s') . "', 'user', 0)");
    }

    public function getUsersName($newUserLogin): bool|array|null
    {   
        return mysqli_fetch_assoc(mysqli_query($this->connect, "SELECT * FROM users WHERE user_login = '" . $newUserLogin . "'"));
    }
}
