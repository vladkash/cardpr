<?php

use Faker\Factory as FakerFactory;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\TransferStats;

class ApiTest
{
    const API_URL = 'https://core.codepr.ru/api/v2/crm/user_create_or_update';

    const API_KEY = '5240f691-60b0-4360-ac1f-601117c5408f';

    const API_ACCOUNT = 'testphp.codepr.ru';

    private $guzzle;

    private $faker;

    static $requests = 0;

    static $time = 0;

    public function __construct()
    {
        $this->guzzle = new Guzzle([
            'base_uri' => self::API_URL,
        ]);

        $this->faker = FakerFactory::create('ru_RU');
    }

    public function testMethodIsAvailable()
    {
        echo 'Проверка работоспособности метода...'.PHP_EOL.PHP_EOL;

        $this->makeFakeRequest();

        echo PHP_EOL.PHP_EOL;
    }

    public function testResponseTime($requestsCount)
    {
        echo 'Тестирование времени ответа (количество запросов: '.$requestsCount.')...'.PHP_EOL.PHP_EOL;

        for ($i = 0; $i < $requestsCount; $i++) {
            $this->makeRequestWithTime();
        }
        echo PHP_EOL.'Среднее время ответа составило: '.round(self::$time / self::$requests, 2).'сек.'.PHP_EOL.PHP_EOL;
    }

    public function testLoad($requestsCount)
    {
        echo 'Нагрузочное тестирование (количество запросов: '.$requestsCount.')...'.PHP_EOL.PHP_EOL;

        for ($i = 0; $i < $requestsCount; $i++) {
            $this->makeAsyncRequest();
        }

        sleep(5);

        echo PHP_EOL.'Проверка работы сервиса после нагрузки...'.PHP_EOL;
        $this->makeFakeRequest();
    }

    private function makeFakeRequest()
    {
        try {
            $response = $this->guzzle->post('', ['json' => $this->fakeData()]);
            echo 'Запрос выполнен успешно: '.$response->getBody()->getContents().PHP_EOL;
        } catch (ClientException $e) {
            echo 'Ошибка: '.$e->getMessage();
        }
    }

    private function makeRequestWithTime()
    {
        try {
            $response = $this->guzzle->post('', [
                'json' => $this->fakeData(),
                'on_stats' => function (TransferStats $stats) {
                    self::$time += $stats->getTransferTime();
                    self::$requests += 1;
                }
            ]);
            echo 'Запрос выполнен успешно: '.$response->getBody()->getContents().PHP_EOL;
        } catch (ClientException $e) {
            echo 'Ошибка: '.$e->getMessage();
        }
    }

    private function makeAsyncRequest()
    {
        try {
            $this->guzzle->postAsync('', ['json' => $this->fakeData()]);
        } catch (ClientException $e) {}
    }

    private function fakeData()
    {
        return [
            'app_key' => self::API_KEY,
            'phone' => '+7'.$this->faker->numberBetween(9000000000, 9999999999),
            'email' => $this->faker->email,
            'name' => $this->faker->firstName,
            'surname' => $this->faker->lastName,
            'middlename' => $this->faker->lastName,
            'birthday' => $this->faker->dateTimeBetween('-70 years', '-18 years')->format('d.m.Y'),
            'discount' => $this->faker->numberBetween(0, 100),
            'bonus' => $this->faker->numberBetween(0, 100),
            'balance' => $this->faker->numberBetween(0, 100),
            'link' => self::API_ACCOUNT,
            'sms' => $this->faker->realText(50)
        ];
    }

}