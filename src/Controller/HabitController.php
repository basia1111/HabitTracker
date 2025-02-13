<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Habit;
use App\Form\HabitFormType;
use App\Interface\HabitServiceInterface;
use Symfony\Bundle\SecurityBundle\Security;

class HabitController extends AbstractController 
{

    public function __construct(HabitServiceInterface $habitServiceInterface, Security $security){
        $this->habitServiceInterface = $habitServiceInterface;
        $this->security = $security;
    }
    /**
     *  Handles creating habit
     */
    #[Route('/habit/create', name: 'habit_create', methods: ['GET', 'POST'])]
    public function habitCreate(Request $request)
    {
        $habit = new Habit();

        $form = $this->createForm(HabitFormType::class, $habit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $habit->setCreatedAt(new \DateTime());
            $user = $this->security->getUser();
            if ($user) {
                $habit->setUser($user);
            } else {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'User not authenticated.',
                ], 400);
            }
        if(!$habit->getFrequency()){
            return new JsonResponse([
                'status' => 'error',
                'message' => 'You need to select habit frequency.',
            ], 400);
        }
        if($habit->getFrequency() === 'days' && empty($habit->getWeekDays())){
            return new JsonResponse([
                'status' => 'error',
                'message' => 'You need to select specyfic days when frequency is set to selected days.',
            ], 400);
        }

            $this->habitServiceInterface->save($habit);


            return new JsonResponse([
                'status' => 'success',
                'habit' => [
                    'id' => $habit->getId(),
                    'name' => $habit->getName(),
                    'frequency' => $habit->getFrequency(),
                    'weekDays' => $habit->getWeekDays(),
                    'color' => $habit->getColor(),
                    'streak' => $habit->getStreak(),
                    'time' => $habit->getTime(),
                ],
                'message' => 'Habit successfully created!',
            ]);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return new JsonResponse([
            'status' => 'error',
            'errors' => $errors,
        ], 400);
    }
}
