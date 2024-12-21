<div class="solution container mt-5">
    <?php
    echo $Quest->listCompletedTasks($_SESSION['user_data']['user_id']);

    ?>
</div>