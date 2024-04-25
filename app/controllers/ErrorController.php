<?php

declare(strict_types=1);

namespace controllers;

use common\AbstractApplication;
use common\AbstractController;

class ErrorController extends AbstractController
{
    /** @var \components\Application */
    protected AbstractApplication $app;

    /**
     *
     */
    public function actionIndex(): string
    {
        return $this->render('index', [
            'responseHeader' => $this->app->responseHeader,
        ]);
    }
}
