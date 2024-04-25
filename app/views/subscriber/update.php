<?php

use common\helpers\FormField;

/** @var \common\ValidationResult $validationResult */
/** @var array{phone:string,name:string,surname:string} $values */

?>
<nav aria-label="breadcrumb">
    <ol id="w4" class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Главная</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= $values['phone']; ?></li>
    </ol>
</nav>

<div class="subscriber-update">

    <h1>Редактировать: <?= $values['phone']; ?></h1>

    <div class="subscriber-update-form">

        <form id="subscriber" action="/subscriber/update?phone=<?= $values['phone']; ?>" method="post">

            <?= FormField::render('subscriber', 'name', 'Имя', $values, true, 100, $validationResult->getAttributeErrors('name')); ?>

            <?= FormField::render('subscriber', 'surname', 'Фамилия', $values, true, 100, $validationResult->getAttributeErrors('surname')); ?>

            <div class="form-group">
                <button type="submit" class="btn btn-success">Сохранить</button>
            </div>

        </form>

    </div>

</div>
