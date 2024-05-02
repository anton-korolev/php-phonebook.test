<?php

use common\helpers\FormField;

/** @var string $phone */
/** @var array{phone:string,name:string,surname:string} $newValues */
/** @var \common\ValidationResult $validationResult */

$phone = htmlspecialchars($phone);

?>
<nav aria-label="breadcrumb">
    <ol id="w4" class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Главная</a></li>
        <li class="breadcrumb-item active" aria-current="page"><a href="/subscriber/view?phone=<?= $phone; ?>"><?= $phone; ?></a></li>
    </ol>
</nav>

<div class="subscriber-update">

    <h1>Редактировать: <?= $phone; ?></h1>

    <div class="subscriber-update-form">

        <form id="subscriber" action="/subscriber/update?phone=<?= $phone; ?>" method="post">

            <?= FormField::render('subscriber', 'name', 'Имя', $newValues, true, 100, $validationResult->getAttributeErrors('name')); ?>

            <?= FormField::render('subscriber', 'surname', 'Фамилия', $newValues, true, 100, $validationResult->getAttributeErrors('surname')); ?>

            <div class="form-group">
                <button type="submit" class="btn btn-success">Сохранить</button>
            </div>

        </form>

    </div>

</div>
