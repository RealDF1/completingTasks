<div class="container">
    <h1 class="text-center mt-5">Рейтинг</h1>
    <table class="table table-striped table-bordered rating-table">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Имя пользователя</th>
                <th scope="col">Рейтинг</th>
            </tr>
        </thead>
        <tbody>
            <?php
            echo $Session->getRaitingList();

            ?>
        </tbody>
    </table>
</div>