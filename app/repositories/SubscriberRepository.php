<?php

declare(strict_types=1);

namespace repositories;

// use common\AppException;

use common\AbstractEntity;
use common\RepositoryInterface;
use models\Subscriber;
use Exception;
use Throwable;

/**
 * Subscriber repository.
 *
 * @implements RepositoryInterface<Subscriber>
 */
class SubscriberRepository implements RepositoryInterface
{
    protected string $dataFile;

    /** @var array<string,array{name:string,surname:string}> subscriber list
     * (phone => [name, surname]). */
    protected array $subscribers;

    /**
     *
     */
    public function __construct(
        protected string $dataPath,
        protected bool $autoSave = true,
    ) {
        $this->dataFile = $this->dataPath . '/subscriber.json';
    }

    /**
     * Loads the database.
     *
     * @throws Exception if the database cannot be loaded.
     */
    public function load(): void
    {
        try {
            $jsonString = file_get_contents($this->dataFile);
            $this->subscribers = json_decode($jsonString, true) ?? [];
        } catch (Throwable $th) {
            throw new Exception("Не могу загрузить базу данных! Проверьте файл <b>$this->dataFile</b>.");
        }
    }

    /**
     * Saves the database.
     *
     * @throws Exception if the database cannot be saved.
     */
    public function save(): void
    {
        try {
            ksort($this->subscribers);
            $jsonString = json_encode($this->subscribers, JSON_PRETTY_PRINT);
            $fp = fopen($this->dataFile, 'w');
            fwrite($fp, $jsonString);
            fclose($fp);
        } catch (Throwable $th) {
            throw new Exception("Не могу сохранить базу данных! Проверьте файл <b>$this->dataFile</b>. Возможно необходимо установить разрешение на запись.");
        }
    }

    /**
     * Saves the database if the `autoSave` property is set to `true`.
     *
     * @throws Exception if the database cannot be saved.
     */
    public function autoSave(): void
    {
        if ($this->autoSave) {
            $this->save();
        }
    }

    /**
     * Returns the number of Subscribers.
     *
     * @return int the number of Subscribers.
     */
    public function count(): int
    {
        return count($this->subscribers);
    }

    /**
     * Returns a slice of the Subscriber list as an array, indexed by `phone`.
     *
     * Note, the values are returned from the database as is and are not validated.
     *
     * @param int $offset if is non-negative, the sequence will start at that offset in the Subscriber list;
     * if is negative, the sequence will start that far from the end of the Subscriber list.
     * @param int $length if is positive, then the sequence will have up to that many elements in it;
     * if is negative then the sequence will stop that many elements from the end of the Subscriber list.
     *
     * @return array<string,array{name:string,surname:string}> Subscriber list (phone => [name, surname]).
     */
    public function selectPage(int $offset, int $length): array
    {
        return array_slice($this->subscribers, $offset, $length, true);
    }

    /**
     * Returns the Subscriber corresponding to the phone number.
     *
     * @param string $id a phone number to search for.
     *
     * @return Subscriber|null Subscriber or `null` if no phone number is found.
     *
     * @throws Exception if incorrect data has been retrieved from the database.
     */
    public function find(string $id): Subscriber|null
    {
        $values = $this->subscribers[$id] ?? null;
        if ($values) {
            if (!($result = Subscriber::createFromArray([
                'phone' => $id,
                'name' => $values['name'],
                'surname' => $values['surname'],
            ]))) {
                throw new Exception(
                    'Недопустимые значения! Скорее всего, в базу данных попали недопустимые символы.<br>'
                        . 'Проблемный номер телефона - <b>' . htmlspecialchars($id) . '</b>.'
                );
            }
            return $result;
        }

        return null;
    }

    /**
     * Adds a new Subscriber to the repository.
     *
     * @param Subscriber $entity Subscriber to be added.
     *
     * @return bool `true` if the insertion passed without error, or `false` if the Subscriber already exists.
     *
     * @throws Exception if the database cannot be saved.
     */
    public function insert(AbstractEntity $entity): bool
    {
        if (!array_key_exists($entity->getPhone(), $this->subscribers)) {
            $this->subscribers[$entity->getPhone()] = [
                'name' => $entity->getName(),
                'surname' => $entity->getSurname(),
            ];
            $this->autoSave();
            return true;
        }

        return false;
    }

    /**
     * Updates the Subscriber in the repository.
     *
     * @param string $id phone number to be updated.
     * @param Subscriber $entity Subscriber to be added.
     *
     * @return bool `true` if the update passed without error, or `false` if no Subscriber is found.
     *
     * @throws Exception if the database cannot be saved.
     */
    public function update(string $id, AbstractEntity $entity): bool
    {
        if (array_key_exists($id, $this->subscribers)) {
            $this->subscribers[$id] = [
                'name' => $entity->getName(),
                'surname' => $entity->getSurname(),
            ];
            $this->autoSave();
            return true;
        }

        return false;
    }

    /**
     * Deletes the Subscriber from the repository.
     *
     * @param string $id phone number to be deleted.
     *
     * @return bool `true` if the deletion passed without error, or `false` if no Subscriber is found.
     *
     * @throws Exception if the database cannot be saved.
     */
    public function delete(string $id): bool
    {
        if (array_key_exists($id, $this->subscribers)) {
            unset($this->subscribers[$id]);
            $this->autoSave();
            return true;
        }

        return false;
    }

    /**
     * Generates a unique phone number.
     *
     * @return string unique phone number.
     */
    protected function generatePhone(): string
    {
        do {
            $phone = (string)mt_rand(1000000000, 9999999999);
        } while (array_key_exists($phone, $this->subscribers));
        return $phone;
    }

    /**
     * Generates 1,000 unique Subscribers.
     *
     * @return void
     *
     * @throws Exception if the database cannot be saved.
     */
    public function generate(): void
    {
        try {
            $content = file_get_contents($this->dataPath . '/names.txt');
            $names = explode("\n", $content);
        } catch (Throwable $th) {
            throw new Exception("Не могу открыть файл <b>$this->dataPath/names.txt</b>.");
        }
        try {
            $content = file_get_contents($this->dataPath . '/surnames.txt');
            $surnames = explode("\n", $content);
        } catch (Throwable $th) {
            throw new Exception("Не могу открыть файл <b>$this->dataPath/surnames.txt</b>.");
        }

        $this->subscribers = [];
        for ($i = 0; $i < 1000; $i++) {
            $this->subscribers[$this->generatePhone()] = [
                'name' => $names[array_rand($names, 1)],
                'surname' => $surnames[array_rand($surnames, 1)]
            ];
        }

        $this->save();
    }
}
