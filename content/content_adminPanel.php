<form action="" method="POST">
    <!-- Таблица -->
    <table class="table table-bordered mt-4">
        <caption class="text-center">Добавление задания</caption>
        <thead>
        <tr>
            <th>Название</th>
            <th>Описание</th>
            <th >Результат</th>
            <th >Тип результата</th>
            <th>Наименование функции</th>
            <th>Доп. тесты</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><textarea name="name_task" rows="5" class="form-control" placeholder="Название задачи"></textarea></td>
            <td><textarea name="body_task" rows="5" class="form-control" placeholder="Описание к задаче"></textarea></td>
            <td><textarea name="task_answer" rows="5" class="form-control" placeholder="Результат выполнения функции"></textarea></td>
            <td><textarea name="type" rows="2" class="form-control" placeholder="Тип результата"></textarea></td>
            <td><textarea name="function_name" rows="5" class="form-control" placeholder="Наименование функции"></textarea></td>
            <td><textarea name="function_test" rows="10" class="form-control" placeholder="Дополнительные тесты(переменная, перемнная, ответ)"></textarea></td>
        </tr>
        </tbody>
    </table>
    <div class="d-flex justify-content-end mt-3" style="padding: 10px">
        <button type="submit" class="btn btn-success" name="link" value="profile">Добавить</button>
    </div>
</form>
<?php
    echo $Quest->addTask();