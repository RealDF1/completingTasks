<?php

namespace Scripts;

use ErrorException;
use Scripts\Interfaces\BDQuests;
use Throwable;

class TASK
{
    private BDQuests $BD;
    // Ссылка на сервис для выполнения произвольного кода
    private $url = "http://executecode/index.php/";

    public function __construct(BDQuests $BD)
    {
        $this->BD = $BD;
    }

    // Отправка json методом post и получние ответа
    private function sendPostRequest($data)
    {
        // Инициализация cURL
        $ch = curl_init($this->url);

        // Настройка параметров cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Возвращать ответ как строку
        curl_setopt($ch, CURLOPT_POST, true); // Устанавливаем метод POST
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json', // Указываем, что отправляем JSON
            'Content-Length: ' . strlen(json_encode($data))
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Отправляем данные в формате JSON

        // Выполнение запроса
        $response = curl_exec($ch);

        // Проверка на ошибки
        if (curl_errno($ch)) {
            echo 'Ошибка cURL: ' . curl_error($ch);
        }

        // Закрытие cURL
        curl_close($ch);

        return $response; // Возвращаем ответ
    }

    // Формирование json и вызов функции отправки post запроса
    private function callFunction($function, array $args, $functionBody): bool|string
    {
        $data = [
            'functionName' => $function,
            'arguments' => $args,
            'functionBody' => $functionBody
        ];

        return $this->sendPostRequest($data);
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
            || !isset($_POST['function_test'])
            || !isset($_POST['arguments_function'])
        ) {
            return;
        }

        return $this->BD->setTaskToTable(
        ) ? "<h2 class='sucsess text-center'>Добавлено</h2>" : "<h2 class='error text-center'>Ошибка</h2>";
    }

    //Список готовых решений
    public function listCompletedTasks($user_id = null): string
    {
        $listCompletedTasks = $this->BD->getCompletedCode($user_id);
        $ans = '';

        foreach ($listCompletedTasks as $task) {
            $ans .= "
            <div class='review-text'>
                <pre><code>" . trim($task['code']) . "</code></pre>
            </div>";

            if (!is_null($user_id)) {
                $ans .= "
            <div class='like-button'>
                <span class=''>Тема задания: </span>
                <span class='themeCompletedTask'>" . $task['name_task'] . "</span>
                <span class='like-count ml-auto'>👍 " . ($task['likes'] = null ? "0" : $task['likes']) . "</span>
            </div>";

                continue;
            }
            $ans .= "
            <div class='like-button'>
                <span class='like-count ml-auto'>👍 " . ($task['likes'] = null ? "0" : $task['likes']) . "</span>
                " . (!isset($_SESSION['user_data']) ? '' : "<form method='POST' action=''>
                    <input type='hidden' name='link' value='quests'>
                    <button type='submit' class='btn btn-primary' name='like' value='" . $task['id'] . "'>Лайк</button>
                </form>") . "
            </div>";
        }
        return $ans;
    }

    //Лайк решения
    public function setLike(): void
    {
        if (!isset($_POST['like']) || !isset($_SESSION['user_data'])) {
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
    public function testCodeUser(): string
    {
        $addRating = 5;
        // Получение данных о задании
        $result = $this->BD->getQuest($_SESSION['task_id']);

        // Форматирование для выполнения написанного пользователем кода
        $code = $_POST['code'];


        try {
            // Извлекаем только вызов функции
            if (preg_match(
                '/function\s+' . $result['function_name'] . '\s*\([^)]*\)\s*:\s*' . $result['type'] . '\s*\{[^}]*\}/',
                $code,
                $matches
            )) {
                $safeCode = $matches[0];
            } else {
                throw new ErrorException("Не найдена функция " . $result['function_name']);
            }
            //eval($safeCode);

            if ($result['function_test']) { // Если кол-во аргументов функции >0
                // Форматирование данных для теста
                // tests = Array [
                //          'arguments' => $argumentsArray,
                //          'result' => trim($resultValue)
                //          ];
                $tests = $this->getArrayForTesting($result["function_test"]);


                foreach ($tests as $test) {
                    if ($this->callFunction(
                            $result['function_name'],
                            $test['arguments'],
                            $safeCode
                        ) != $test['result']) {
                        throw new ErrorException(
                            'Дополнительные тесты не пройдены! Функция работает не правильно, при значениях ' . print_r(
                                $test['arguments'],
                                true
                            ) . 'правильный результат ' . $test['result'] . ". Результат работы функции равен: " . $this->callFunction(
                                $result['function_name'],
                                $test['arguments'],
                                $safeCode
                            )
                        );
                    }
                }
            } elseif ($result['function_name']() != $result['answer_task']) { // Если кол-во аргументов функции 0
                throw new ErrorException(
                    'Дополнительные тесты не пройдены! Функция работает не правильно. Результат функции: ' . $result['function_name'](
                    )
                );
            }
            $answer = "<h2 class='sucsess' style='color: green;'>Успех!</h2> <pre class='bg-light p-3 border'>Функция работает корректно! <br><br>Также пройдены дополнительные тесты</pre>";

            // Проверка на авторизацию
            if (isset($_SESSION['user_data']['user_id'])) {
                //$this->BD->setCompletedCodeToTable(); // Запись правильного кода
                $this->BD->setRaitingUserOnComplete($addRating); // Добавление рейтинга
                $_SESSION['user_data']['user_raiting'] += $addRating;
            }
        } catch (Throwable $e) {
            $answer = "<h2 class='error'>Результат:</h2> <pre class='bg-light p-3 border'>Результат выполнения кода: <br>" . $e->getMessage(
                ) . "</pre>";
        }

        // Вывод ответа
        return "<div class='m-auto' style='width: 70%'>" . $answer . "</div>";
    }

    public function runCodeUser($safeCode, $functionName): mixed
    {
        $url = 'http://0.0.0.0:8080/script/execute.php'; // Укажите IP вашего контейнера

        $data = [
            'code' => $safeCode,
            'function' => $functionName
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return json_decode($result, true);
    }
}