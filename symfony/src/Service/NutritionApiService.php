<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class NutritionApiService {
    private const API_URL = 'https://api.calorieninjas.com/v1/nutrition';

    public function __construct(
        private HttpClientInterface $client,
        #[Autowire('%app.calorie_ninjas_key%')]
        private string $apiKey
    ){}

    public function calculateTotalCalories(string $query): ?int {
        if(empty($query)) {
            return null;
        }

        try {
            $response = $this->client->request('GET', self::API_URL, [
                'query' => [
                    'query' => $query,
                ],
                'headers' => [
                    'X-Api-Key' => $this->apiKey
                ],
            ]);

            $items = $response->toArray();

            $totalCalories = 0;

            foreach($items ?? [] as $item) {
                $totalCalories += $item['calories'];
            }

            return (int) round($totalCalories);
        }catch(\Exception $e) {
            return null;
        }
    }
}
