<?php

namespace App\Http\Controllers;

use Google\Client as GoogleClient;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use DateTime;
class CalendarEventController extends Controller
{
    /**
     * Obtenir le client Google avec gestion du token
     */
    protected function getClient()
    {
        $user = Auth::user();

        if (!$user->google_token) {
            throw new \Exception('Google token not found. Please reconnect your Google account.');
        }

        $client = new GoogleClient();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->setAccessToken($user->google_token);

        // Vérifier et rafraîchir le token si expiré
        if ($client->isAccessTokenExpired()) {
            if ($user->google_refresh_token) {
                $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
                $newToken = $client->getAccessToken();
                $user->google_token = $newToken['access_token'];
                $user->save();
            } else {
                throw new \Exception('Refresh token not found. Please reconnect your Google account.');
            }
        }

        return $client;
    }

    /**
     * Afficher la page d'accueil avec les événements du calendrier
     */
    public function index()
    {
        try {
            $service = new Calendar($this->getClient());

            // Récupérer les événements des 6 derniers mois jusqu'à 6 mois dans le futur
            $timeMin = (new DateTime())->modify('-6 months')->format(DateTime::RFC3339);
            $timeMax = (new DateTime())->modify('+6 months')->format(DateTime::RFC3339);

            $eventsData = $service->events->listEvents('primary', [
                'maxResults' => 2500, // Augmenté pour afficher plus d'événements
                'orderBy' => 'startTime',
                'singleEvents' => true,
                'timeMin' => $timeMin, // 6 mois dans le passé
                'timeMax' => $timeMax, // 6 mois dans le futur
            ]);

            $events = [];
            foreach ($eventsData->getItems() as $event) {
                $eventData = [
                    'id' => $event->id,
                    'title' => $event->getSummary(),
                    'start' => $event->start->dateTime ?? $event->start->date,
                    'end' => $event->end->dateTime ?? $event->end->date,
                ];

                // Ajouter la description si elle existe
                if ($event->getDescription()) {
                    $eventData['description'] = $event->getDescription();
                }

                // Ajouter la couleur si elle existe
                if ($event->getColorId()) {
                    $eventData['color'] = $this->getColorFromId($event->getColorId());
                }

                $events[] = $eventData;
            }

            Log::info('Calendar events loaded:', ['count' => count($events)]);

            return view('home', compact('events'));
        } catch (\Exception $e) {
            Log::error('Calendar index error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return view('home', ['events' => []]);
        }
    }

    /**
     * Obtenir la couleur à partir de l'ID de couleur Google
     */
    private function getColorFromId($colorId)
    {
        $colors = [
            '1' => '#a4bdfc', // Lavender
            '2' => '#7ae7bf', // Sage
            '3' => '#dbadff', // Grape
            '4' => '#ff887c', // Flamingo
            '5' => '#fbd75b', // Banana
            '6' => '#ffb878', // Tangerine
            '7' => '#46d6db', // Peacock
            '8' => '#e1e1e1', // Graphite
            '9' => '#5484ed', // Blueberry
            '10' => '#51b749', // Basil
            '11' => '#dc2127', // Tomato
        ];

        return $colors[$colorId] ?? null;
    }

    /**
     * Créer un nouvel événement
     */
    public function store(Request $request)
    {
        try {
            // Log pour déboguer
            Log::info('Calendar store request:', $request->all());

            // Validation simple sans règles strictes
            $title = $request->input('title');
            $start = $request->input('start');
            $end = $request->input('end');

            if (!$title || !$start) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le titre et la date de début sont requis'
                ], 400);
            }

            $service = new Calendar($this->getClient());

            // Convertir les dates au format correct
            $startDateTime = new DateTime($start);
            $endDateTime = $end ? new DateTime($end) : (clone $startDateTime)->modify('+1 hour');

            // Créer l'événement
            $event = new Event([
                'summary' => $title,
                'description' => $request->input('description', ''),
                'start' => [
                    'dateTime' => $startDateTime->format(DateTime::RFC3339),
                    'timeZone' => 'Africa/Tunis',
                ],
                'end' => [
                    'dateTime' => $endDateTime->format(DateTime::RFC3339),
                    'timeZone' => 'Africa/Tunis',
                ],
            ]);

            $createdEvent = $service->events->insert('primary', $event);

            Log::info('Event created successfully:', ['id' => $createdEvent->id]);

            return response()->json([
                'success' => true,
                'id' => $createdEvent->id,
                'title' => $createdEvent->summary,
                'start' => $createdEvent->start->dateTime,
                'end' => $createdEvent->end->dateTime,
            ]);
        } catch (\Exception $e) {
            Log::error('Calendar store error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Mettre à jour un événement existant
     */
    public function update(Request $request, $id)
    {
        try {
            Log::info('Calendar update request:', ['id' => $id, 'data' => $request->all()]);

            $title = $request->input('title');
            $start = $request->input('start');
            $end = $request->input('end');

            if (!$title || !$start) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le titre et la date de début sont requis'
                ], 400);
            }

            $service = new Calendar($this->getClient());

            // Récupérer l'événement existant
            $event = $service->events->get('primary', $id);

            // Mettre à jour les propriétés
            $event->setSummary($title);

            if ($request->has('description')) {
                $event->setDescription($request->input('description'));
            }

            // Convertir et mettre à jour les dates
            $startDateTime = new DateTime($start);
            $endDateTime = $end ? new DateTime($end) : (clone $startDateTime)->modify('+1 hour');

            $startEvent = new \Google_Service_Calendar_EventDateTime();
            $startEvent->setDateTime($startDateTime->format(DateTime::RFC3339));
            $startEvent->setTimeZone('Africa/Tunis');
            $event->setStart($startEvent);

            $endEvent = new \Google_Service_Calendar_EventDateTime();
            $endEvent->setDateTime($endDateTime->format(DateTime::RFC3339));
            $endEvent->setTimeZone('Africa/Tunis');
            $event->setEnd($endEvent);

            // Sauvegarder les modifications
            $updatedEvent = $service->events->update('primary', $id, $event);

            Log::info('Event updated successfully:', ['id' => $updatedEvent->id]);

            return response()->json([
                'success' => true,
                'id' => $updatedEvent->id,
                'title' => $updatedEvent->summary,
                'start' => $updatedEvent->start->dateTime ?? $updatedEvent->start->date,
                'end' => $updatedEvent->end->dateTime ?? $updatedEvent->end->date,
            ]);
        } catch (\Exception $e) {
            Log::error('Calendar update error: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un événement
     */
    public function destroy($id)
    {
        try {
            $service = new Calendar($this->getClient());
            $service->events->delete('primary', $id);

            return response()->json([
                'success' => true,
                'message' => 'Event deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Calendar destroy error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete event: ' . $e->getMessage()
            ], 500);
        }
    }
}