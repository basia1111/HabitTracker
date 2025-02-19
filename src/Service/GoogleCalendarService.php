<?php

namespace App\Service;

use App\Entity\User;
use Google\Client as GoogleClient;
use Google\Service\Calendar;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GoogleCalendarService
{
    private $params;
    private $client;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
        $this->client = new GoogleClient();
        $this->client->setClientId($this->params->get('google.client_id'));
        $this->client->setClientSecret($this->params->get('google.client_secret'));
    }

    public function createHabitCalendar(User $user)
    {
        // Set up client with user's access token
        $this->client->setAccessToken($user->getGoogleAccessToken());

        // Check if token has expired
        if ($this->client->isAccessTokenExpired()) {
            $this->client->fetchAccessTokenWithRefreshToken($user->getGoogleRefreshToken());
            // Update user's tokens in database
            $tokens = $this->client->getAccessToken();
            $user->setGoogleAccessToken($tokens['access_token']);
            if (isset($tokens['refresh_token'])) {
                $user->setGoogleRefreshToken($tokens['refresh_token']);
            }
        }

        $service = new Calendar($this->client);

        // Create a new calendar for habits
        $calendar = new \Google\Service\Calendar\Calendar();
        $calendar->setSummary('My Habits Tracker');
        $calendar->setTimeZone('UTC');
        
        try {
            $createdCalendar = $service->calendars->insert($calendar);
            return [
                'success' => true,
                'calendar_id' => $createdCalendar->getId()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}