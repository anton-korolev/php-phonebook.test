<?php

declare(strict_types=1);

namespace controllers;

use common\AbstractApplication;
use common\AbstractController;
use common\AppException;
use common\ValidationResult;
// use components\Application;
use models\Subscriber;

class SubscriberController extends AbstractController
{
    /** @var \components\Application */
    protected AbstractApplication $app;

    /**
     *
     */
    public function actionIndex(): string
    {
        $pageSize = 20;
        $count = $this->app->subscriberRepository->count();
        $pageCount = intdiv($count, $pageSize)
            + ($count % $pageSize === 0 ? 0 : 1);

        $currentPage = $this->app->request->GET()['page'] ?? 1;
        if ($currentPage < 1) {
            throw new AppException('?page=1', 301);
        }
        if ($currentPage > $pageCount) {
            throw new AppException("?page=$pageCount", 301);
        }

        $subscribers = $this->app->subscriberRepository
            ->select(($currentPage - 1) * $pageSize, $pageSize);

        $count = count($subscribers);
        $subscribers = array_chunk($subscribers, intdiv($count, 2) + $count % 2, true);

        return $this->render('index', [
            'subscriberChunks' => $subscribers,
            'pageCount' => $pageCount,
            'currentPage' => $currentPage,
        ]);
    }

    /**
     *
     */
    protected function findSubscriber(): Subscriber
    {
        $phone = $this->app->request->GET()['phone'] ?? null;
        if ((null === $phone)
            || !($subscriber = $this->app->subscriberRepository->find($phone))
        ) {
            throw new AppException("Номер не найден.", 404);
        };

        return $subscriber;
    }

    /**
     *
     */
    public function actionView(): string
    {
        return $this->render('view', [
            'subscriber' => $this->findSubscriber(),
        ]);
    }

    /**
     *
     */
    public function actionCreate(): string
    {
        $values = null;
        $validationResult = new ValidationResult();

        if ($this->app->request->isPost()) {
            $values = $this->app->request->POST()['subscriber'] ?? [];
            if ($this->app->subscriberRepository
                ->insert($values, $validationResult)
            ) {
                throw new AppException(
                    "/subscriber/view?phone={$values['phone']}",
                    301
                );
            }
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
        $subscriber = null;
        $validationResult = new ValidationResult();

        if ($this->app->request->isPost()) {
            $values = $this->app->request->POST()['subscriber'] ?? [];
            if (array_key_exists('phone', $this->app->request->GET())) {
                $values['phone'] = $this->app->request->GET()['phone'];
            } else {
                unset($values['phone']);
            }

            if ($this->app->subscriberRepository
                ->update($values, $validationResult)
            ) {
                throw new AppException(
                    "/subscriber/view?phone={$values['phone']}",
                    301
                );
            }
        } else {
            $subscriber = $this->findSubscriber();
            $values = $subscriber->getAttributes();
        }

        return $this->render('update', [
            'values' => $values,
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
            && array_key_exists('phone', $this->app->request->GET())
            && $this->app->subscriberRepository->delete(
                $this->app->request->GET()['phone']
            )
        ) {
            throw new AppException('/', 301);
        }

        return $this->render('delete', [
            'subscriber' => $this->findSubscriber(),
        ]);
    }
}
