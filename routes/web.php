<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $url = "http://109.73.206.144:6969/api/stocks";
    $response = Http::get($url, [
        "dateFrom" => "2025-08-10",
        "dateTo" => "2025-08-14",
        "page" => 1,
        "key" => "E6kUTYrYwZq2tN4QEtyzsbEBk3ie",
        "limit" => 500
    ]);
    return response()->json($response->json(), 200, [], JSON_PRETTY_PRINT);
    
});
