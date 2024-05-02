<?php

declare(strict_types=1);

namespace services;

use common\AppException;
use common\RepositoryInterface;
use common\ValidationResult;
use models\Subscriber;
use Throwable;

class SubscriberService
{
    /**
     * @param RepositoryInterface<Subscriber> $repository
     */
    public function __construct(
        protected RepositoryInterface $repository
    ) {
    }

    public function GetPage(string|int $currentPage, int $pageSize = 20): SubscriberListPage
    {
        if (is_string($currentPage)) {
            if (is_numeric($currentPage)) {
                $num = (int)$currentPage;
                if ($currentPage !== (string)$num) {
                    throw new AppException("?page=$num", 301);
                }
                $currentPage = $num;
            } else {
                throw new AppException('', 404);
            }
        }

        $count = $this->repository->count();
        $pageCount = intdiv($count, $pageSize)
            + (($count % $pageSize === 0) ? 0 : 1);

        if ($currentPage < 1) {
            throw new AppException('?page=1', 301);
        }
        if ($currentPage > $pageCount) {
            throw new AppException("?page=$pageCount", 301);
        }

        return new SubscriberListPage(
            $currentPage,
            $pageCount,
            $this->repository->selectPage(
                ($currentPage - 1) * $pageSize,
                $pageSize
            )
        );
    }

    /**
     * Returns the Subscriber corresponding to the phone number.
     *
     * @param string|null $phone a phone number to search for.
     *
     * @return Subscriber found subscriber.
     *
     * @throws AppException
     * - with code 500 if incorrect data has been retrieved from the repository.
     * - with code 404 if the subscriber is not found.
     */
    public function getSubscriber(string|null $phone): Subscriber
    {
        $subscriber = null;
        if (null !== $phone) {
            try {
                $subscriber = $this->repository->find($phone);
            } catch (Throwable $th) {
                throw new AppException($th->getMessage(), 500);
            }
        };

        if (null === $subscriber) {
            throw new AppException('Номер не найден.', 404);
        }

        return $subscriber;
    }

    /**
     * Returns a new Subscriber.
     *
     * A new Subscriber will be created (with attribute validation) and stored into the repository.
     *
     * @param array<string,mixed> $values an array of new Subscriber attribute values
     * (attribute => value).
     * @param ValidationResult|null &$result storage for collecting validation errors.
     * By default is `null`.
     *
     * @return Subscriber|null new Subscriber or `null` if the creation failed.
     *
     * @throws AppException
     * - with code 500 if there is an internal repository error.
     */
    public function createSubscriber(array $values, ValidationResult|null $result = null): Subscriber|null
    {
        $subscriber = Subscriber::createFromArray($values, $result);
        if (!$subscriber) {
            return null;
        }

        try {
            if ($this->repository->insert($subscriber)) {
                return $subscriber;
            } else {
                $result?->addError('phone', 'Такой номер уже существует.');
            }
        } catch (Throwable $th) {
            throw new AppException($th->getMessage(), 500);
        }

        return null;
    }

    /**
     * Updates the Subscriber.
     *
     * @param string|null $phone phone number to be updated.
     * @param srray<string,mixed> $newVAlues an array of new Subscriber attribute values
     * (attribute => value).
     * @param ValidationResult|null &$result storage for collecting validation errors.
     * By default is `null`.
     *
     * @return Subscriber|null updated Subscriber or `null` if the updation failed.
     *
     * @throws AppException
     * - with code 500 if there is an internal repository error.
     * - with code 404 if the subscriber is not found.
     */
    public function updateSubscriber(string|null $phone, array $newValues, ValidationResult|null $result = null): Subscriber|null
    {
        $subscriber = $this->getSubscriber($phone);

        try {
            if (
                $subscriber->setAttributes($newValues, $result)
                && $this->repository->update($phone, $subscriber)
            ) {
                return $subscriber;
            }
        } catch (Throwable $th) {
            throw new AppException($th->getMessage(), 500);
        }

        return null;
    }

    /**
     * Deletes the Subscriber.
     *
     * @param string|null $phone phone number to be updated.
     *
     * @return bool `true` if the deletion passed without error.
     *
     * @throws AppException
     * - with code 500 if there is an internal repository error.
     * - with code 404 if the subscriber is not found.
     */
    public function deleteSubscriber(string|null $phone): bool
    {
        try {
            return (null !== $phone) &&
                $this->repository->delete($phone);
        } catch (Throwable $th) {
            throw new AppException($th->getMessage(), 500);
        }

        throw new AppException('Номер не найден.', 404);

        return false;
    }
}
