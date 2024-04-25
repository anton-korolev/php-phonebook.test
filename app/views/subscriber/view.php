<?php

/** @var \models\Subscriber $subscriber */

?>
<nav aria-label="breadcrumb">
    <ol id="w4" class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Главная</a></li>
        <li class="breadcrumb-item active" aria-current="page">Просмотр</li>
    </ol>
</nav>

<div class="subscriber-view">

    <h1>Номер телефона: <?= $subscriber->getPhone(); ?></h1>

    <table id="w0" class="table table-striped table-bordered detail-view">
        <tr>
            <th>Имя</th>
            <td><?= $subscriber->getName(); ?></td>
        </tr>
        <tr>
            <th>Фамилия</th>
            <td><?= $subscriber->getSurname(); ?></td>
        </tr>
    </table>

    <p>
        <a class="btn btn-primary" href="/subscriber/update?phone=<?= $subscriber->getPhone(); ?>">Редактировать</a>
        <a class="btn btn-danger" href="/subscriber/delete?phone=<?= $subscriber->getPhone(); ?>">Удалить</a>
    </p>

</div>
