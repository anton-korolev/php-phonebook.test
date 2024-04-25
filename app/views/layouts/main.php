<?php

/** @var string $content */

?>
<!DOCTYPE html>
<html lang="ru-RU" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Телефонный справочник</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <link href="/css/bootstrap.css" rel="stylesheet">
    <link href="/css/site.css" rel="stylesheet">
</head>

<body class="d-flex flex-column h-100">

    <header id="header">
        <nav id="w0" class="navbar-expand-md navbar-dark bg-dark fixed-top navbar">
            <div class="container">
                <a class="navbar-brand" href="/">Телефонный справочник</a>
                <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#w0-collapse" aria-controls="w0-collapse" aria-expanded="false" aria-label="Переключить навигацию"><span class="navbar-toggler-icon"></span></button>
                <div id="w0-collapse" class="collapse navbar-collapse">
                    <ul id="w1" class="navbar-nav nav">
                        <li class="nav-item"><a class="nav-link" href="/">Главная</a></li>
                        <li class="nav-item"><a class="nav-link" href="/subscriber/create">Добавить номер</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main id="main" class="flex-shrink-0" role="main">
        <div class="container">
            <?= $content ?>
        </div>
    </main>

    <footer id="footer" class="mt-auto py-3 bg-light">
        <div class="container">
            <div class="row text-muted">
                <div class="col-md-6 text-center text-md-start">&copy; Антон Королев 2024</div>
            </div>
        </div>
    </footer>

</body>

<script src="/js/jquery.js"></script>
<script src="/js/yii.js"></script>
<script src="/js/bootstrap.bundle.js"></script>
</body>

</html>
