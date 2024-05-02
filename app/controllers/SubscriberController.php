<?php

declare(strict_types=1);

namespace controllers;

use common\AbstractApplication;
use common\AbstractController;
use common\AppException;
use common\ValidationResult;

class SubscriberController extends AbstractController
{
    /** @var \components\Application */
    protected AbstractApplication $app;

    /**
     *
     */
    public function actionIndex(): string
    {
        $page = $this->app->getSubscriberService()
            ->GetPage($this->app->request->GET()['page'] ?? 1);

        $count = count($page->subscribers);
        $subscriberChunks = array_chunk(
            $page->subscribers,
            intdiv($count, 2) + $count % 2,
            true
        );

        return $this->render('index', [
            'subscriberChunks' => $subscriberChunks,
            'pageCount' => $page->pageCount,
            'currentPage' => $page->currentPage,
        ]);
    }

    /**
     *
     */
    public function actionView(): string
    {
        return $this->render('view', [
            'subscriber' => $this->app->getSubscriberService()
                ->getSubscriber($this->app->request->GET()['phone'] ?? null),
        ]);
    }

    /**
     *
     */
    public function actionCreate(): string
    {
        $values = [];
        $validationResult = new ValidationResult();

        if (
            $this->app->request->isPost()
            && ($subscriber = $this->app->getSubscriberService()
                ->createSubscriber(
                    $values = $this->app->request->POST()['subscriber'] ?? [],
                    $validationResult = new ValidationResult()
                ))
        ) {
            throw new AppException("/subscriber/view?phone={$subscriber->getPhone()}", 301);
        }

        return $this->render('create', [
            'values' => $values,
            'validationResult' => $validationResult
        ]);
    }

    /**
     *
     */
    public function actionUpdate(): string
    {
        $phone = $this->app->request->GET()['phone'] ?? null;
        $newValues = $this->app->request->POST()['subscriber'] ?? [];
        $validationResult = new ValidationResult();

        if ($this->app->request->isPost()) {
            if ($subscriber = $this->app->getSubscriberService()
                ->updateSubscriber($phone, $newValues, $validationResult)
            ) {
                throw new AppException("/subscriber/view?phone={$subscriber->getPhone()}", 301);
            }
        } else {
            $newValues = $this->app->getSubscriberService()
                ->getSubscriber($phone)
                ?->getAttributes() ?? [];
        }

        return $this->render('update', [
            'phone' => $phone,
            'newValues' => $newValues,
            'validationResult' => $validationResult
        ]);
    }

    /**
     *
     */
    public function actionDelete(): string
    {
        if (
            $this->app->request->isPost()
            && $this->app->getSubscriberService()
            ->deleteSubscriber($this->app->request->GET()['phone'] ?? null)
        ) {
            throw new AppException('/', 301);
        }

        return $this->render('delete', [
            'subscriber' => $this->app->getSubscriberService()
                ->getSubscriber($this->app->request->GET()['phone'] ?? null),
        ]);
    }
}
