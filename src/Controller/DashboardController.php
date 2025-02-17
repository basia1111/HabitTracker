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
        $habit = new Habit();
        $form = $this->createForm(HabitFormType::class, $habit);

        $habits = $this->habitService->findAll();
        $todayHabits = $this->habitService->getTodayHabits(); 

        return $this->render('dashboard/index.html.twig', [
            'habits' => $habits,
            'todayHabits' => $todayHabits,
            'createHabitForm' => $form ->createView(),
        ]);
    }
}
