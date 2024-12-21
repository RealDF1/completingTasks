<?php
$Session->getHeaderTask();

?>

<form method="post" class="mt-4">
    <div class="form-group m-auto" style="width: 70%">
        <textarea id="code" name="code" rows="20" class="form-control">
            <?php
            echo trim(isset($_POST['code']) ? $_POST['code'] : $Session->getPatternForCodeSpace());

            ?>
        </textarea>
    </div>
    <div class="d-flex justify-content-end mt-2" style="width: 85%">
        <button type="submit" name="link" value="quests" class="btn btn-primary">Выполнить</button>
    </div>
</form>