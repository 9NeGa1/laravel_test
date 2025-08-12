<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Income;

class FetchIncomesCommand extends Command
{
    protected $signature = 'fetch:incomes';
    protected $description = 'Получение данных о поставках из API и сохранение в базу данных';

    public function handle()
    {
        $baseUrl = "http://109.73.206.144:6969/api/incomes";
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
                    $processed = $this->processIncomes($pageData);
                    $totalProcessed += $processed;

                    $this->info("Обработана страница {$currentPage}: {$processed} записей. Всего: {$totalProcessed}");

                    // Проверка на последнюю страницу
                    if (count($pageData) < $limit) {
                        break;
                    }

                    // Подготовка к следующей странице
                    $currentPage++;
                    $params['page'] = $currentPage;

                    // Пауза между запросами
                    sleep(1);
                } else {
                    $this->error("Ошибка запроса для страницы {$currentPage}. Статус: " . $response->status());
                    Log::error("Ошибка запроса API поставок", [
                        'page' => $currentPage,
                        'status' => $response->status(),
                        'response' => $response->body()
                    ]);
                    break;
                }
            } catch (\Exception $e) {
                $this->error("Произошло исключение: " . $e->getMessage());
                Log::error("Ошибка при получении поставок: " . $e->getMessage());
                break;
            }
        } while (true);

        $this->info("Завершено! Всего обработано поставок: {$totalProcessed}");
        return 0;
    }

    protected function processIncomes(array $incomes): int
    {
        $processed = 0;

        foreach ($incomes as $income) {
            try {
                // Определяем уникальный ключ (предполагаем, что income_id уникален)
                $attributes = ['income_id' => $income['income_id']];

                // Исключаем income_id из массового заполнения
                $fillableAttributes = array_diff_key($income, ['income_id' => '']);

                Income::updateOrCreate(
                    $attributes,
                    $fillableAttributes
                );
                $processed++;
            } catch (\Exception $e) {
                Log::error("Ошибка обработки поставки {$income['income_id']}: " . $e->getMessage());
            }
        }

        return $processed;
    }
}
