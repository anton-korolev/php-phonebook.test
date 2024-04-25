<?php

use common\helpers\FormField;

/** @var \common\ValidationResult|null $validationResult */
/** @var array<string,string> $values */
?>
<div class="subscriber-form">

    <form id="subscriber" action="/subscriber/create" method="post">

        <?= FormField::render('subscriber', 'phone', 'Номер телефона', $values, true, 10, $validationResult?->getAttributeErrors('phone')); ?>

        <?= FormField::render('subscriber', 'name', 'Имя', $values, true, 100, $validationResult?->getAttributeErrors('name')); ?>

        <?= FormField::render('subscriber', 'surname', 'Фамилия', $values, true, 100, $validationResult?->getAttributeErrors('surname')); ?>

        <div class="form-group">
            <button type="submit" class="btn btn-success">Сохранить</button>
        </div>

    </form>

</div>
