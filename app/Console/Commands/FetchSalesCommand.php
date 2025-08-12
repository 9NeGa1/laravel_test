<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Sale;

class FetchSalesCommand extends Command
{
    protected $signature = 'fetch:sales';
    protected $description = 'Fetch all sales data from API and store in database';

    public function handle()
    {
        $baseUrl = "http://109.73.206.144:6969/api/sales";
        $apiKey = "E6kUTYrYwZq2tN4QEtyzsbEBk3ie";
        $limit = 500;
        $currentPage = 1;
        $totalProcessed = 0;

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

                    if (empty($data['data'])) {
                        $this->info("No more data available.");
                        break;
                    }

                    $processed = $this->processSales($data['data']);
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

        $this->info("Finished! Total sales processed: {$totalProcessed}");
        return 0;
    }

    protected function processSales(array $sales): int
    {
        $processed = 0;

        foreach ($sales as $sale) {
            try {
                Sale::updateOrCreate(
                    ['g_number' => $sale['g_number']],
                    $this->prepareSaleData($sale)
                );
                $processed++;
            } catch (\Exception $e) {
                Log::error("Failed to process sale {$sale['g_number']}: " . $e->getMessage());
            }
        }

        return $processed;
    }

    protected function prepareSaleData(array $sale): array
    {
        // Автоматически преобразуем все поля из API, кроме g_number
        $data = [];
        foreach ($sale as $key => $value) {
            if ($key !== 'g_number') {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    protected function handleRequestError(int $page, $response): void
    {
        $this->error("Request failed for page {$page}. Status: " . $response->status());
        Log::error("Sales API request failed", [
            'page' => $page,
            'status' => $response->status(),
            'response' => $response->body()
        ]);
    }

    protected function handleException(\Exception $e): void
    {
        $this->error("Exception occurred: " . $e->getMessage());
        Log::error("Sales fetch exception: " . $e->getMessage());
    }
}
