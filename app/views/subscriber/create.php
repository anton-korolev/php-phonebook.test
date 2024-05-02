<?php

use common\helpers\FormField;

/** @var \common\ValidationResult $validationResult */
/** @var array<string,string> $values */

?>
<nav aria-label="breadcrumb">
    <ol id="w4" class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Главная</a></li>
        <li class="breadcrumb-item active" aria-current="page">Добавиь</li>
    </ol>
</nav>

<div class="subscriber-create">

    <h1>Новый абонент</h1>

    <div class="subscriber-create-form">

        <form id="subscriber" action="/subscriber/create" method="post">

            <?= FormField::render('subscriber', 'phone', 'Номер телефона', $values, true, 10, $validationResult->getAttributeErrors('phone')); ?>

            <?= FormField::render('subscriber', 'name', 'Имя', $values, true, 100, $validationResult->getAttributeErrors('name')); ?>

            <?= FormField::render('subscriber', 'surname', 'Фамилия', $values, true, 100, $validationResult->getAttributeErrors('surname')); ?>

            <div class="form-group">
                <button type="submit" class="btn btn-success">Сохранить</button>
            </div>

        </form>

    </div>

</div>
