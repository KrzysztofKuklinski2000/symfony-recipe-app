<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;

class NutritionApiService {
    private const API_URL = 'https://api.calorieninjas.com/v1/nutrition';
    private const TIMEOUT_SECONDS = 4.0;
    private const LANGUAGE = 'en';

    public function __construct(
        #[Autowire(service: 'monolog.logger.nutrition')]
        private LoggerInterface $logger,
        private HttpClientInterface $client,
        #[Autowire('%app.calorie_ninjas_key%')]
        private string $apiKey,
    ){}

    public function calculateTotalCalories(string $query): ?int {
        if(empty($query)) {
            return null;
        }

        $queryToSend = $this->translateQuery($query);
        return $this->fetchCaloriesFromApi($queryToSend);
    }

    private function translateQuery(string $query): string {
        try {
            $translator = new GoogleTranslate(self::LANGUAGE);
            $englishQuery = $translator->translate($query);
            return $englishQuery;
        }catch(\Throwable $e) {
            $this->logger->warning(
                'NutritionApiService: Błąd tłumaczenia Google: ' . $e->getMessage(),
                ['query' => $query]
            );
            return $query;
        }
    }

    private function fetchCaloriesFromApi(string $query): ?int {
        try {
            $response = $this->client->request('GET', self::API_URL, [
                'query' => [
                    'query' => $query,
                ],
                'headers' => [
                    'X-Api-Key' => $this->apiKey
                ],
                'timeout' => self::TIMEOUT_SECONDS,
            ]);

            $data = $response->toArray();
            $totalCalories = 0;

            foreach ($data['items'] ?? [] as $item) {
                if (isset($item['calories'])) {
                    $totalCalories += $item['calories'];
                }
            }

            return (int) round($totalCalories);
        }catch(HttpExceptionInterface $e) {
            $this->logger->error('NutritionApiService: Błąd HTTP API: ' . $e->getMessage(),[
                'status_code' => $e->getResponse()->getStatusCode(),
                'response_content' => $e->getResponse()->getContent(false),
            ]);
        }catch(TransportExceptionInterface $e) {
            $this->logger->critical('NutritionApiService: Błąd połączenia sieciowego: ' . $e->getMessage());
        }catch(\Throwable $e) {
            $this->logger->error('NutritionApiService: Nieoczekiwany błąd: ' . $e->getMessage());
        }
        return null;
    }
}
