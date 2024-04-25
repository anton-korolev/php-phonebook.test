<?php

declare(strict_types=1);

namespace repositories;

use common\AppException;
use common\RepositoryInterface;
use common\ValidationResult;
use models\Subscriber;
use Throwable;

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
     *
     */
    public function load(): void
    {
        try {
            $jsonString = file_get_contents($this->dataFile);
            $this->subscribers = json_decode($jsonString, true) ?? [];
        } catch (Throwable $th) {
            throw new AppException('Не могу загрузить базу данных! Проверьте файл <b>app/db/subscriber.json</b>.', 500);
        }
    }

    /**
     *
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
            throw new AppException('Не могу сохранить базу данных! Проверьте файл <b>app/db/subscriber.json</b>. Возможно необходимо установить права на запись.', 500);
        }
    }

    /**
     *
     */
    public function autoSave(): void
    {
        if ($this->autoSave) {
            $this->save();
        }
    }

    /**
     *
     */
    public function count(): int
    {
        return count($this->subscribers);
    }

    /**
     * @return array<string,array{name:string,surname:string}> subscriber list
     * (phone => [name, surname]).
     */
    public function select(int $offset, int $count): array
    {
        // echo '<pre>';
        // var_dump(array_slice($this->subscribers, $offset, $count, true));
        // die;
        return array_slice($this->subscribers, $offset, $count, true);
    }

    /**
     *
     */
    public function find(string $phone): Subscriber|null
    {
        $value = $this->subscribers[$phone] ?? null;
        if ($value) {
            $result = Subscriber::createFromArray([
                'phone' => $phone,
                'name' => $value['name'],
                'surname' => $value['surname'],
            ]);
            if (!$result) {
                throw new AppException("Недопустимые значения! Скорее всего, в базу данных попали недопустимые символы.<br>Проблемный номер телефона - <b>$phone</b>.", 500);
            }
            return $result;
        }

        return null;
    }

    /**
     *
     */
    public function insert(array $values, ValidationResult|null $result = null): Subscriber|null
    {
        $subscriber = Subscriber::createFromArray($values, $result);
        if (!$subscriber) {
            return null;
        }

        if (!array_key_exists($subscriber->getPhone(), $this->subscribers)) {
            $this->subscribers[$subscriber->getPhone()] = [
                'name' => $subscriber->getName(),
                'surname' => $subscriber->getSurname(),
            ];
            $this->autoSave();
        } else {
            $result->addError('phone', 'Такой номер уже существует.');
            $subscriber = null;
        }

        return $subscriber;
    }

    /**
     *
     */
    public function update(array $values, ValidationResult|null $result = null): Subscriber|null
    {
        if (
            !array_key_exists('phone', $values)
            || !($subscriber = $this->find($values['phone']))
        ) {
            throw new AppException('Номер телефона не найден.', 404);
        }

        unset($values['phone']);
        if ($subscriber->setAttributes($values, $result)) {
            $this->subscribers[$subscriber->getPhone()] = [
                'name' => $subscriber->getName(),
                'surname' => $subscriber->getSurname(),
            ];
            $this->autoSave();
        } else {
            $subscriber = null;
        }

        return $subscriber;
    }

    /**
     *
     */
    public function delete(string $phone): bool
    {
        if (!array_key_exists($phone, $this->subscribers)) {
            throw new AppException('Номер телефона не найден.', 404);
        } else {
            unset($this->subscribers[$phone]);
            $this->autoSave();
        }

        return true;
    }

    public function generate(): void
    {
        $content = file_get_contents($this->dataPath . '/names.txt');
        $names = explode("\n", $content);
        $content = file_get_contents($this->dataPath . '/surnames.txt');
        $surnames = explode("\n", $content);

        // $nameCount = count($names);
        // $surnameCount = count($surnames);
        $this->subscribers = [];
        for ($i = 0; $i < 1000; $i++) {
            // random_bytes
            $this->subscribers[(string)mt_rand(1000000000, 9999999999)] = [
                'name' => $names[array_rand($names, 1)],
                'surname' => $surnames[array_rand($surnames, 1)]
            ];
        }

        $this->save();
        echo "Ok!";
        die;
    }
}
