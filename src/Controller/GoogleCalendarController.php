<?php
namespace App\Controller;

use App\Entity\Habit;
use App\Service\GoogleCalendarService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GoogleCalendarController extends AbstractController
{
    private GoogleCalendarService $googleCalendarService;

    public function __construct(GoogleCalendarService $googleCalendarService) 
    {
        $this->googleCalendarService = $googleCalendarService;
    }

    /**
     * Add event to calendar
     */
    #[Route('/calendar/{id}/add-to-calendar', name: 'add_habit_to_calendar', methods: ['POST'])]
    public function addToCalendar(
        Habit $habit
    ): JsonResponse {
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(
                ['error' => 'User not logged in'],
                403
            );
        }
        if (!$user->getGoogleCalendarId()) {
            return new JsonResponse(
                ['error' => 'Google Calendar not connected'],
                404
            );
        }
        if ($habit->getGoogleEventId()) {
            return new JsonResponse(
                ['error' => 'Habit already added to calendar'],
                404
            );
        }

        $result = $this->googleCalendarService->addHabitToCalendar($user, $habit);

        if ($result['success']) {
            return new JsonResponse(
                ['message' => 'Habit added to calendar'],
                200
            );
        }

        return new JsonResponse(
            ['error' => $result['error']],
            500
        );
    }

    /**
     * Remove events from calendar
     */
    #[Route('/calendar/{id}/remove-from-calendar', name: 'remove_habit_from_calendar', methods: ['DELETE'])]
    public function removeFromCalendar(
        Habit $habit
    ): JsonResponse {
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(
                ['error' => 'User not logged in'],
                403
            );
        }

        if (!$user->getGoogleCalendarId()) {
            return new JsonResponse(
                ['error' => 'Google Calendar not connected'],
                404
            );
        }

        if (!$habit->getGoogleEventId()) {
            return new JsonResponse(
                ['error' => 'Habit not found in calendar'],
                404
            );
        }

        $result = $this->googleCalendarService->removeFromCalendar($user, $habit);

        if ($result['success']) {
            return new JsonResponse(
                ['message' => 'Habit removed from calendar'],
                200
            );
        }

        return new JsonResponse(
            ['error' => $result['error']],
            500
        );
    }

    /**
     * Fetch calendar view
     */
    #[Route('/calendar/view', name: 'calendar_view')]
    public function viewCalendar(): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        
        $embedUrl = $this->googleCalendarService->getCalendarEmbedUrl($user);
        
        
    if (!$embedUrl) {
        return new JsonResponse([
            'success' => false,
            'error' => 'Calendar not available'
        ], 403);
    }

    return new JsonResponse([
        'success' => true,
        'url' => $embedUrl
    ]);
    }
}