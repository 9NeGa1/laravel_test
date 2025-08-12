<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class FetchOrdersCommand extends Command
{
    protected $signature = 'fetch:orders';
    protected $description = 'Получение данных о заказах из API и сохранение в базу данных';

    public function handle()
    {
        $baseUrl = "http://109.73.206.144:6969/api/orders";
        $apiKey = "E6kUTYrYwZq2tN4QEtyzsbEBk3ie";
        $limit = 500;
        $currentPage = 1;
        $totalProcessed = 0;

        // Параметры запроса
        $params = [
            "key" => $apiKey,
            "limit" => $limit,
            "page" => $currentPage,
            "dateFrom" => "2000-01-01",
            "dateTo" => now()->format('Y-m-d')
        ];

        do {
            try {
                $response = Http::get($baseUrl, $params);

                if ($response->successful()) {
                    $data = $response->json();

                    // Проверка на пустые данные
                    if (empty($data['data'])) {
                        $this->info("Нет данных для обработки.");
                        break;
                    }

                    $pageData = $data['data'];
                    $processed = $this->processOrders($pageData);
                    $totalProcessed += $processed;

                    $this->info("Обработана страница {$currentPage}: {$processed} записей. Всего: {$totalProcessed}");

                    // Проверка на последнюю страницу (если данных меньше лимита)
                    if (count($pageData) < $limit) {
                        break;
                    }

                    // Подготовка к следующей странице
                    $currentPage++;
                    $params['page'] = $currentPage;

                    // Небольшая пауза чтобы не нагружать сервер
                    sleep(1);
                } else {
                    $this->error("Ошибка запроса для страницы {$currentPage}. Статус: " . $response->status());
                    Log::error("Ошибка запроса API заказов", [
                        'page' => $currentPage,
                        'status' => $response->status(),
                        'response' => $response->body()
                    ]);
                    break;
                }
            } catch (\Exception $e) {
                $this->error("Произошло исключение: " . $e->getMessage());
                Log::error("Ошибка при получении заказов: " . $e->getMessage());
                break;
            }
        } while (true);

        $this->info("Завершено! Всего обработано заказов: {$totalProcessed}");
        return 0;
    }

    protected function processOrders(array $orders): int
    {
        $processed = 0;

        foreach ($orders as $order) {
            try {
                // Автоматическое заполнение всех полей из массива
                Order::updateOrCreate(
                    ['g_number' => $order['g_number']], // Уникальный ключ
                    $order // Все остальные данные
                );
                $processed++;
            } catch (\Exception $e) {
                Log::error("Ошибка обработки заказа {$order['g_number']}: " . $e->getMessage());
            }
        }

        return $processed;
    }
}