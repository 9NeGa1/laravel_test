<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Stock;
use Carbon\Carbon;

class FetchStocksCommand extends Command
{
    protected $signature = 'fetch:stocks';
    protected $description = 'Fetch all stocks data from API and store in database';

    public function handle()
    {
        $baseUrl = "http://109.73.206.144:6969/api/stocks";
        $apiKey = "E6kUTYrYwZq2tN4QEtyzsbEBk3ie";
        $limit = 500;
        $currentPage = 1;
        $totalProcessed = 0;

        // Дата начала - вчера (как требует API)
        // Дата окончания - конец 2026 года
        $params = [
            "key" => $apiKey,
            "limit" => $limit,
            "page" => $currentPage,
            "dateFrom" => Carbon::today()->format('Y-m-d'),
            "dateTo" => "2026-12-31"
        ];

        do {
            try {
                $response = Http::get($baseUrl, $params);

                if ($response->successful()) {
                    $data = $response->json();

                    if (empty($data['data'])) {
                        $this->info("No more data available.");
                        break;
                    }

                    $processed = $this->processStocks($data['data']);
                    $totalProcessed += $processed;

                    $this->info("Processed page {$currentPage}: {$processed} records. Total: {$totalProcessed}");

                    if (count($data['data']) < $limit) break;

                    $currentPage++;
                    $params['page'] = $currentPage;
                    sleep(1);
                } else {
                    $this->handleRequestError($currentPage, $response);
                    break;
                }
            } catch (\Exception $e) {
                $this->handleException($e);
                break;
            }
        } while (true);

        $this->info("Finished! Total stocks processed: {$totalProcessed}");
        return 0;
    }

    protected function processStocks(array $stocks): int
    {
        $processed = 0;

        foreach ($stocks as $stock) {
            try {
                // Используем комбинацию полей для уникальности записи
                $uniqueKeys = [
                    'nm_id' => $stock['nm_id'],
                    'warehouse_name' => $stock['warehouse_name'],
                    'date' => $stock['date']
                ];

                Stock::updateOrCreate(
                    $uniqueKeys,
                    $this->prepareStockData($stock)
                );
                $processed++;
            } catch (\Exception $e) {
                Log::error("Failed to process stock: " . json_encode($stock) . " Error: " . $e->getMessage());
            }
        }

        return $processed;
    }

    protected function prepareStockData(array $stock): array
    {
        // Исключаем поля, используемые для уникального ключа
        unset($stock['nm_id'], $stock['warehouse_name'], $stock['date']);

        return $stock;
    }

    protected function handleRequestError(int $page, $response): void
    {
        $this->error("Request failed for page {$page}. Status: " . $response->status());
        Log::error("Stocks API request failed", [
            'page' => $page,
            'status' => $response->status(),
            'response' => $response->body()
        ]);
    }

    protected function handleException(\Exception $e): void
    {
        $this->error("Exception occurred: " . $e->getMessage());
        Log::error("Stocks fetch exception: " . $e->getMessage());
    }
}
