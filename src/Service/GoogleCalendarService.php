<?php
namespace App\Service;

use App\Entity\User;
use App\Entity\Habit;
use App\Interface\HabitServiceInterface;
use Google\Client as GoogleClient;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Psr\Log\LoggerInterface;

class GoogleCalendarService
{
    private $client;
    private HabitServiceInterface $habitServiceInterface;
    private LoggerInterface $logger;

    public function __construct(HabitServiceInterface $habitServiceInterface, LoggerInterface $logger)
    {
        $this->habitServiceInterface = $habitServiceInterface;
        $this->client = new GoogleClient();
        $this->client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
        $this->client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
        $this->logger = $logger;
    }

    /**
     * Create calendar
     */
    public function createHabitCalendar(User $user)
    {
        $this->setupClient($user);
        $service = new Calendar($this->client);
        
        // Create a new calendar for habits
        $calendar = new \Google\Service\Calendar\Calendar();
        $calendar->setSummary('My Habits Tracker');
        $calendar->setTimeZone('UTC');
        
        try {
            $createdCalendar = $service->calendars->insert($calendar);

             // Set calendar to be publicly readable
            $rule = new \Google\Service\Calendar\AclRule();
            $scope = new \Google\Service\Calendar\AclRuleScope();
            
            $scope->setType('default');
            $rule->setScope($scope);
            $rule->setRole('reader');
            
            $service->acl->insert($createdCalendar->getId(), $rule);

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

    /**
     * Add event to calendar
     */
    public function addHabitToCalendar(User $user, Habit $habit)
    {
        $this->setupClient($user);
        $service = new Calendar($this->client);

        try {
            if (!$this->isCalendarValid($user)) {
                return [
                    'success' => false,
                    'error' => 'Failed to validate or create calendar'
                ];
            }

            $event = new Event();
            $event->setSummary($habit->getName());

            $start = new EventDateTime();
            $userTimezone = 'Europe/Warsaw'; // Explicitly set local timezone

            if ($habit->getTime() instanceof \DateTime) {
                $timeFromHabit = $habit->getTime();
                $startTime = new \DateTime('today', new \DateTimeZone($userTimezone));
                $startTime->setTime(
                    (int)$timeFromHabit->format('H'),
                    (int)$timeFromHabit->format('i'),
                    (int)$timeFromHabit->format('s')
                );
            } else {
                $startTime = new \DateTime('today 09:00', new \DateTimeZone($userTimezone));
            }

            $start->setTimeZone($userTimezone);
            $start->setDateTime($startTime->format('Y-m-d\TH:i:s' . $startTime->format('P')));
            $event->setStart($start);

            $end = new EventDateTime();
            $end->setTimeZone($userTimezone);
            $end->setDateTime($startTime->format('Y-m-d\TH:i:s' . $startTime->format('P')));
            $event->setEnd($end);


            $recurrence = $this->habitServiceInterface->createGoogleRecurrenceRule($habit);
            if ($recurrence) {
                $event->setRecurrence([$recurrence]);
            }
    
            $color = $habit->getColor();
            if ($color > 0 && $color <= 11) {
                $event->setColorId((string)$color);
            }
    
            $createdEvent = $service->events->insert(
                $user->getGoogleCalendarId(), 
                $event
            );
    
            $habit->setGoogleEventId($createdEvent->getId());
            $this->habitServiceInterface->save($habit);
    
            return [
                'success' => true,
                'event_id' => $createdEvent->getId()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Remove event from calendar
     */
    public function removeFromCalendar(User $user, Habit $habit): array
    {
        $this->setupClient($user);
        $service = new Calendar($this->client);

        try {

            if (!$this->isCalendarValid($user)) {
                return [
                    'success' => false,
                    'error' => 'Failed to validate or create calendar'
                ];
            }

            if (!$habit->getGoogleEventId()) {
                return [
                    'success' => false,
                    'error' => 'No Google Calendar event ID found for this habit'
                ];
            }

            $service->events->delete(
                $user->getGoogleCalendarId(),
                $habit->getGoogleEventId()
            );

            // Clear the Google Event ID from the habit
            $habit->setGoogleEventId(null);
            $this->habitServiceInterface->save($habit);

            return [
                'success' => true
            ];
        } catch (\Exception $e) {
            $this->logger->error('Failed to remove Google Calendar event: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

     /**
     * Update event
     */
    public function updateHabitInCalendar(User $user, Habit $habit): array
    {
        $this->setupClient($user);
        $service = new Calendar($this->client);

        try {

            if (!$this->isCalendarValid($user)) {
                return [
                    'success' => false,
                    'error' => 'Failed to validate or create calendar'
                ];
            }

            if (!$habit->getGoogleEventId()) {
                return [
                    'success' => false,
                    'error' => 'No Google Calendar event ID found for this habit'
                ];
            }

            // Get the existing event
            $event = $service->events->get(
                $user->getGoogleCalendarId(),
                $habit->getGoogleEventId()
            );

            // Update event properties
            $event->setSummary($habit->getName());

            // Update time
            $start = new EventDateTime();
            $end = new EventDateTime();
            $userTimezone = 'Europe/Warsaw'; 

            if ($habit->getTime() instanceof \DateTime) {
                $timeFromHabit = $habit->getTime();
                $startTime = new \DateTime('today', new \DateTimeZone($userTimezone));
                $startTime->setTime(
                    (int)$timeFromHabit->format('H'),
                    (int)$timeFromHabit->format('i'),
                    (int)$timeFromHabit->format('s')
                );
            } else {
                $startTime = new \DateTime('today 09:00', new \DateTimeZone($userTimezone));
            }

            $start->setTimeZone($userTimezone);
            $start->setDateTime($startTime->format('Y-m-d\TH:i:s' . $startTime->format('P')));
            $event->setStart($start);

            $end->setTimeZone($userTimezone);
            $end->setDateTime($startTime->format('Y-m-d\TH:i:s' . $startTime->format('P')));
            $event->setEnd($end);

            // Update recurrence
            $recurrence = $this->habitServiceInterface->createGoogleRecurrenceRule($habit);
            if ($recurrence) {
                $event->setRecurrence([$recurrence]);
            }

            // Update color
            $color = $habit->getColor();
            if ($color > 0 && $color <= 11) {
                $event->setColorId((string)$color);
            }

            // Update the event in Google Calendar
            $updatedEvent = $service->events->update(
                $user->getGoogleCalendarId(),
                $habit->getGoogleEventId(),
                $event
            );

            return [
                'success' => true,
                'event_id' => $updatedEvent->getId()
            ];

        } catch (\Exception $e) {
            $this->logger->error('Failed to update Google Calendar event: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Setup client access token
     */
    private function setupClient(User $user): void
    {
        $this->client->setAccessToken($user->getGoogleAccessToken());

        if ($this->client->isAccessTokenExpired()) {
            $this->client->fetchAccessTokenWithRefreshToken($user->getGoogleRefreshToken());
            $tokens = $this->client->getAccessToken();
            $user->setGoogleAccessToken($tokens['access_token']);
            if (isset($tokens['refresh_token'])) {
                $user->setGoogleRefreshToken($tokens['refresh_token']);
            }
        }
    }

    /**
     * Delete all user events
     */
    private function clearUserHabitsGoogleEvents(User $user): void
    {
        $habits = $this->habitServiceInterface->findAll($user);

        foreach ($habits as $habit) {
            if ($habit->getGoogleEventId()) {
                $habit->setGoogleEventId(null);
                $this->habitServiceInterface->save($habit);
            }
        }
    }

     /**
     * Check if user calendar exists and if no, create new one
     */
    private function isCalendarValid(User $user): bool
    {
        try {
            $this->setupClient($user);
            $service = new Calendar($this->client);
            
            $service->calendars->get($user->getGoogleCalendarId());
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Calendar validation error: ' . $e->getMessage());
            if (str_contains($e->getMessage(), 'Not Found') || str_contains($e->getMessage(), '404')) {

                $this->clearUserHabitsGoogleEvents($user);
                $result = $this->createHabitCalendar($user);
                
                if ($result['success']) {
                    $user->setGoogleCalendarId($result['calendar_id']);
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();

                    return true;
                }

                $user->setGoogleCalendarId(null);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
            return false;
        }
    }

     /**
     * Get calendar embed url
     */
    public function getCalendarEmbedUrl(User $user): ?string
    {
        if (!$this->isCalendarValid($user)) {
            return null;
        }
        
        $calendarId = $user->getGoogleCalendarId();
        return "https://calendar.google.com/calendar/embed?src=" . urlencode($calendarId) . "&ctz=Europe/Warsaw";
    }
}