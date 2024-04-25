<?php

/** @var \models\Subscriber $subscriber */

?>
<nav aria-label="breadcrumb">
    <ol id="w4" class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Главная</a></li>
        <li class="breadcrumb-item active" aria-current="page">Удаление</li>
    </ol>
</nav>

<div class="subscriber-delete">

    <h1>Удаление: <?= $subscriber->getPhone(); ?></h1>

    <p>
        Вы действительно хотите удалить этого абонента?
    </p>

    <p>
        <a class="btn btn-primary" href="/subscriber/view?phone=<?= $subscriber->getPhone(); ?>">Отмена</a>
        <a class="btn btn-danger" href="/subscriber/delete?phone=<?= $subscriber->getPhone(); ?>" data-confirm="Вы уверены, что хотите удалить этого абонента?" data-method="post">Удалить</a>
    </p>

</div>
