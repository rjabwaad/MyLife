<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function index(Request $request)
    {
        // المدينة من input أو افتراضيًا Tunis
        $city = $request->get('city', 'Tunis');

        $response = Http::get('https://api.weatherapi.com/v1/current.json', [
            'key'  => config('services.weatherapi.key'),
            'q'    => $city,
            'lang' => 'fr',
        ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'فشل في جلب بيانات الطقس'
            ], 500);
        }

        return response()->json($response->json());
    }
    /**
     * Afficher la météo dans la page d'accueil
     */
    public function showWithCalendar(Request $request)
    {
        $city = $request->get('city', 'Tunis');

        try {
            // Récupérer les données météo
            $weatherResponse = Http::get('https://api.weatherapi.com/v1/current.json', [
                'key' => config('services.weatherapi.key'),
                'q' => $city,
                'lang' => 'fr'
            ]);

            if ($weatherResponse->failed()) {
                $weather = null;
                $weatherError = 'Impossible de récupérer les données météo';
            } else {
                $weather = $weatherResponse->json();
                $weatherError = null;
            }
        } catch (\Exception $e) {
            $weather = null;
            $weatherError = 'Erreur: ' . $e->getMessage();
        }

        // Récupérer les événements du calendrier (même logique que CalendarEventController)
        $events = $this->getCalendarEvents();

        return view('home', compact('weather', 'weatherError', 'events'));
    }

    /**
     * Récupérer les événements du calendrier
     */
    private function getCalendarEvents()
    {
        try {
            $user = auth()->user();

            if (!$user || !$user->google_token) {
                return [];
            }

            $client = new \Google\Client();
            $client->setClientId(env('GOOGLE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
            $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
            $client->setAccessToken($user->google_token);

            if ($client->isAccessTokenExpired()) {
                if ($user->google_refresh_token) {
                    $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
                    $newToken = $client->getAccessToken();
                    $user->google_token = $newToken['access_token'];
                    $user->save();
                } else {
                    return [];
                }
            }

            $service = new \Google\Service\Calendar($client);

            $timeMin = (new \DateTime())->modify('-6 months')->format(\DateTime::RFC3339);
            $timeMax = (new \DateTime())->modify('+6 months')->format(\DateTime::RFC3339);

            $eventsData = $service->events->listEvents('primary', [
                'maxResults' => 2500,
                'orderBy' => 'startTime',
                'singleEvents' => true,
                'timeMin' => $timeMin,
                'timeMax' => $timeMax,
            ]);

            $events = [];
            foreach ($eventsData->getItems() as $event) {
                $events[] = [
                    'id' => $event->id,
                    'title' => $event->getSummary(),
                    'start' => $event->start->dateTime ?? $event->start->date,
                    'end' => $event->end->dateTime ?? $event->end->date,
                ];
            }

            return $events;
        } catch (\Exception $e) {
            \Log::error('Weather controller calendar error: ' . $e->getMessage());
            return [];
        }
    }
}

