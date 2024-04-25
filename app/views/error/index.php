<?php

/** @var \component\ResponseHeader $responseHeader */

?>
<div class="error-index">

    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">Ошибка <?= $responseHeader->statusCode; ?></h1>
        <p class="lead"><?= $responseHeader->errorMessage; ?></p>
        <hr class="my-4">
    </div>

</div>
