<?php


// src/Controller/GoogleCalendarController.php
namespace App\Controller;

use App\Service\GoogleCalendarService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoogleCalendarController extends AbstractController
{
    #[Route('/connect-google-calendar', name: 'connect_google_calendar')]
    public function connectCalendar(
        GoogleCalendarService $calendarService,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'User not logged in'], 401);
        }

        //Create the calendar
        $result = $calendarService->createHabitCalendar($user);

        if ($result['success']) {
            // Store the calendar ID
            $user->setGoogleCalendarId($result['calendar_id']);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->json(['message' => 'Calendar created successfully']);
        }

        return $this->json(['error' => $result['error']], 500);
    }
}