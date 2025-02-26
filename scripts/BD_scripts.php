<?php

interface BDQuests
{
    public function setTaskToTable();

    public function getCompletedCode();

    public function setLikeQuery();

    public function getQuest($task_id);

    public function setCompletedCodeToTable();

    public function setRaitingUserOnComplete($addRating);

}

interface BDSession
{
    public function getRaitingListQueary();

    public function getUsersName($newUserLogin);

    public function setNewUserOnBD($newUserLogin, $newUserPass);

    public function getHeaderTaskQuery();

    public function getPatternForCodeSpaceQuery();

    public function getListOfTasksQuery();

    public function findCreatedUser($userLogin);
}

class BD implements BDSession, BDQuests
{
    protected PDO $connect;
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
        try {
            $this->connect = new PDO('mysql:host=localhost;dbname=BKP', 'root', '');
            $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Ошибка подключения: ' . $e->getMessage());
        }
    }

    // Список правильных решений
    public function getCompletedCode($user_id = false): array|bool
    {
        if ($user_id) {
            $stmt = $this->connect->prepare(
                "SELECT tasks_complete.*, tasks.name_task FROM tasks_complete JOIN tasks ON tasks_complete.id_task = tasks.id WHERE tasks_complete.id_user = :user_id"
            );
            $stmt->execute(['user_id' => $user_id]);
        } else {
            $stmt = $this->connect->prepare("SELECT * FROM `tasks_complete` WHERE id_task = :task_id");
            $stmt->execute(['task_id' => $_SESSION['task_id']]);
        }
        return $stmt->fetchAll();
    }

    // Добавление рейтинга
    public function setRaitingUserOnComplete($addRating): void
    {
        $stmt = $this->connect->prepare("UPDATE `users` SET `raiting` = `raiting` + :addRating WHERE `user_id` = :user_id");
        $stmt->execute(['addRating' => $addRating, 'user_id' => $_SESSION['user_data']['user_id']]);
    }

    // Добавление лайка
    public function setLikeQuery(): bool
    {
        $stmt = $this->connect->prepare("UPDATE `tasks_complete` SET `likes` = `likes` + 1 WHERE id = :like_id");
        return $stmt->execute(['like_id' => $_POST['like']]);
    }

    // Добавление задания
    public function setTaskToTable(): bool
    {
        $stmt = $this->connect->prepare(
            "INSERT INTO `tasks`(`name_task`, `body_task`, `answer_task`, `function_name`, `type`, `function_test`, `arguments_function`, `id_creatorUsers`) VALUES (:name_task, :body_task, :answer_task, :function_name, :type, :function_test, :arguments_function, :id_creatorUsers)"
        );
        return $stmt->execute([
            'name_task' => $_POST['name_task'],
            'body_task' => $_POST['body_task'],
            'answer_task' => $_POST['task_answer'] ?? null,
            'function_name' => $_POST['function_name'],
            'type' => $_POST['type'],
            'function_test' => $_POST['function_test'] ?? null,
            'arguments_function' => $_POST['arguments_function'] ?? null,
            'id_creatorUsers' => $_SESSION['user_data']['user_id']
        ]);
    }

    public function getRaitingListQueary(): array|bool
    {
        $stmt = $this->connect->query("SELECT * FROM users ORDER BY `raiting` DESC LIMIT 10");
        return $stmt->fetchAll();
    }

    // Запись в таблицу при успешном прохождении
    public function setCompletedCodeToTable(): void
    {
        $stmt = $this->connect->prepare(
            "INSERT INTO `tasks_complete` (`code`, `likes`, `id_user`, `id_task`) VALUES (:code, 0, :id_user, :id_task)"
        );
        $stmt->execute([
            'code' => $_POST['code'],
            'id_user' => $_SESSION['user_data']['user_id'],
            'id_task' => $_SESSION['task_id']
        ]);
    }

    public function getConnection(): PDO
    {
        return $this->connect;
    }

    // Страница выбранного задания
    public function getQuest($task_id): array|bool
    {
        $stmt = $this->connect->prepare("SELECT * FROM tasks WHERE id = :task_id");
        $stmt->execute(['task_id' => $task_id]);
        return $stmt->fetch();
    }

    public function findCreatedUser($userLogin): array|bool
    {
        $stmt = $this->connect->prepare("SELECT * FROM users WHERE user_login = :userLogin");
        $stmt->execute(['userLogin' => $userLogin]);
        return $stmt->fetch();
    }

    public function getPatternForCodeSpaceQuery(): array|bool
    {
        $stmt = $this->connect->prepare("SELECT * FROM tasks WHERE id = :task_id");
        $stmt->execute(['task_id' => $_SESSION['task_id']]);
        return $stmt->fetch();
    }

    // Страница с заданиями
    public function getListOfTasksQuery(): array|bool
    {
        $stmt = $this->connect->query("SELECT * FROM tasks");
        return $stmt->fetchAll();
    }

    public function getHeaderTaskQuery(): array|bool
    {
        $stmt = $this->connect->prepare("SELECT * FROM tasks WHERE id = :task_id");
        $stmt->execute(['task_id' => $_SESSION['task_id']]);
        return $stmt->fetch();
    }

    public function setNewUserOnBD($newUserLogin, $newUserPass): void
    {
        $stmt = $this->connect->prepare(
            "INSERT INTO users (user_login, user_pass, created_at, role, raiting) VALUES (:user_login, :user_pass, :created_at, 'user', 0)"
        );
        $stmt->execute([
            'user_login' => $newUserLogin,
            'user_pass' => $newUserPass,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function getUsersName($newUserLogin)
    {
        $stmt = $this->connect->prepare("SELECT * FROM users WHERE user_login = :userLogin");
        $stmt->execute(['userLogin' => $newUserLogin]);
        return $stmt->fetch();
    }
}