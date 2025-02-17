<?php

namespace App\Controller;

use App\Form\HabitFormType;
use App\Entity\Habit;
use App\Interface\HabitServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    private HabitServiceInterface $habitService;

    public function __construct(HabitServiceInterface $habitService)
    {
        $this->habitService = $habitService;
    }

    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        $user = $this->getUser(); 

        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to view habits.');
        }

        $habit = new Habit();
        $form = $this->createForm(HabitFormType::class, $habit);

        $habits = $this->habitService->findAll($user);
        $todayHabits = $this->habitService->getTodayHabits($user);

        return $this->render('dashboard/index.html.twig', [
            'habits' => $habits,
            'todayHabits' => $todayHabits,
            'createHabitForm' => $form->createView(),
        ]);
    }

    #[Route('/api/today-habits', name: 'api_today_habits', methods: ['GET'])]
    public function getTodayHabits(): Response
    {
        $user = $this->getUser();
    
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $todayHabits = $this->habitService->getTodayHabits($user);
    
        $htmlContent = $this->renderView('dashboard/_dashboard-today-habits.html.twig', [
            'todayHabits' => $todayHabits,
        ]);
    
        return $this->json(['html' => $htmlContent]);
    }
    
}
