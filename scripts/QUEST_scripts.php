<?php

class QUEST
{
    private BD $BD;

    public function __construct()
    {
        $this->BD = BD::getInstance();
    }

    // Вызов функции со случайным количеством аргументов
    private function callFunction(callable $function, array $args)
    {
        return $function(...$args);
    }

    //Подготовка к тестам поля function_test
    private function getArrayForTesting($input): array
    {
        // Разделяем входную строку на наборы данных по точке с запятой
        $dataSets = explode(';', $input);
        $result = [];

        foreach ($dataSets as $dataSet) {
            // Удаляем лишние пробелы
            $dataSet = trim($dataSet);

            // Разделяем аргументы и ответ по точке
            list($arguments, $resultValue) = explode('.', $dataSet);

            // Разделяем аргументы по запятой и удаляем лишние пробелы
            $argumentsArray = array_map('trim', explode(',', $arguments));

            // Сохраняем в результирующий массив
            $result[] = [
                'arguments' => $argumentsArray,
                'result' => trim($resultValue)
            ];
        }

        return $result;
    }

    //Добавление задания
    public function addTask()
    {
        if (
            !isset($_POST['name_task'])
            || !isset($_POST['body_task'])
            || !isset($_POST['task_answer'])
            || !isset($_POST['type'])
            || !isset($_POST['function_name'])
        ) {
            return;
        }

        return $this->BD->setTaskToTable() ? "<h2 class='sucsess text-center'>Добавлено</h2>" : "<h2 class='error text-center'>Ошибка</h2>";
    }

    //Список готовых решений
    public function listCompletedTasks(): void
    {
        $list_completed_tasks = $this->BD->getCompletedCode();

        foreach ($list_completed_tasks as $task) {
            echo "
        <div class='review-text'>
            <pre><code>" . $task['code'] . "</code></pre>
        </div>
        <div class='like-button'>
            <span class='like-count ml-auto'>👍 " . ($task['likes'] = null ? "0" : $task['likes']) . "</span>
            " . (!isset($_SESSION['user_data']) ? '' : "<form method='POST' action=''>
                <input type='hidden' name='link' value='quests'>
                <button type='submit' class='btn btn-primary' name='like' value='" . $task['id'] . "'>Лайк</button>
            </form>") . "
        </div>";
        }
    }

    //Лайк решения
    public function setLike(): void
    {
        if (isset($_POST['like']) || isset($_SESSION['user_data'])) {
            return;
        }

        if (!$this->BD->setLikeQuery()) {
            echo "Ошибка";
        }
    }

    //Выбор задания
    public function listQuestsToComplete(): void
    {
        if (
            !isset($_POST['link'])
            || $_POST['link'] !== 'quests'
            || !isset($_POST['task_id'])
        ) {
            return;
        }

        $_SESSION['task_id'] = $_POST['task_id'];
    }

    // Проверка input с кодом
    public function checkCode($code): bool
    {
        if (empty($code)) { // Проверка на наличие синтаксических ошибок
            return false;
        }

        if (!strpos($code, 'function')) { // Проверка на наличие слова function
            return false;
        }

        return true;
    }

    // Тестирование написанного кода
    public function testCodeUser()
    {
        // Получение данных о задании
        $result = $this->BD->get_quest($_SESSION['task_id']);

        // Форматирование для выполнения написанного пользователем кода
        $code = "<?php " . $_POST['code'] . " ?>";

        try {
            // Загрузка в компилятор
            eval("?>$code");

            if ($result['function_test']) { // Если кол-во аргументов функции >0
                // Форматирование данных для теста
                // tests = Array [
                //          'arguments' => $argumentsArray,
                //          'result' => trim($resultValue)
                //          ];
                $tests = $this->getArrayForTesting($result["function_test"]);

                foreach ($tests as $test) {
                    if ($this->callFunction($result['function_name'], $test['arguments']) != $test['result']) {
                        throw new ErrorException(
                            'Дополнительные тесты не пройдены! Функция работает не правильно, при значениях ' . print_r(
                                $test['arguments'],
                                true
                            ) . 'правильный результат ' . $test['result'] . ". Результат работы функции равен: " . $this->callFunction(
                                $result['function_name'],
                                $test['arguments']
                            )
                        );
                    }
                }
            } elseif ($result['function_name']() != $result['answer_task']) { // Если кол-во аргументов функции 0
                throw new ErrorException(
                    'Дополнительные тесты не пройдены! Функция работает не правильно. Результат функции: ' . $result['function_name']()
                );
            }
            $answer = "<h2 class='sucsess' style='color: green;'>Успех!</h2> <pre class='bg-light p-3 border'>Функция работает корректно! <br><br>Также пройдены дополнительные тесты</pre>";

            // Проверка на авторизацию
            if (isset($_SESSION['user_data']['user_id'])) {
                $this->BD->setCompletedCodeToTable(); // Запись правильного кода
            }
        } catch (Throwable $e) {
            $answer = "<h2 class='error'>Результат:</h2> <pre class='bg-light p-3 border'>Результат выполнения кода: <br>" . $e->getMessage() . "</pre>";
        }

        // Вывод ответа
        return "<div class='m-auto' style='width: 70%'>" . $answer . "</div>";
    }
}
