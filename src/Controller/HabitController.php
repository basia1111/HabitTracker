<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Habit;
use App\Form\EditHabitFormType;
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

            $user = $this->security->getUser();
            if ($user) {
                $habit->setUser($user);
            } else {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'User not authenticated.',
                ], 400);
            }

            $habit->setCreatedAt(new \DateTime());

            $hasTime = $form->get('hasTime')->getData(); 

            if (!$hasTime) {
               $habit->setTime(null);
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

    /**
     * Get edit form for habit
     */
    #[Route('/habit/edit/{id}', name: 'habit_edit_form', methods: ['GET'])]
    public function getHabitEditForm(Habit $habit): Response
    {
        if($response = $this->checkHabitOwnership($habit)){
            return $response;
        }

        $form = $this->createForm(EditHabitFormType::class, $habit);

        return $this->render('dashboard/modals/_edit-habit-modal-content.html.twig', [
            'editHabitForm' => $form->createView(),
            'habit' => $habit,
        ]);
    }

    /**
     * Update habit
     */
    #[Route('/habit/update/{id}', name: 'habit_update', methods: ['POST'])]
    public function habitUpdate(Request $request, Habit $habit): JsonResponse
    {
        try {
            $form = $this->createForm(EditHabitFormType::class, $habit);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                if($response = $this->checkHabitOwnership($habit)){
                    return $response;
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
                        'message' => 'You need to select specific days when frequency is set to selected days.',
                    ], 400);
                }

                $hasTime = $form->get('hasTime')->getData(); // Get checkbox value

                if (!$hasTime) {
                $habit->setTime(null);
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
                        'time' => $habit->getTime() ? $habit->getTime()->format('H:i') : null,
                    ],
                    'message' => 'Habit successfully updated!',
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
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'An error occurred while updating the habit.',
            ], 500);
        }
    }

    /**
     * Delete habit
     */

    #[Route('habit/delete/{id}', name: "habit_delete", methods: ['DELETE'])]
    public function habitDelete(Request $request, Habit $habit){
        if($response = $this->checkHabitOwnership($habit)){
            return $response;
        }
        try{
            $this->habitServiceInterface->delete($habit);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Habit deleted succesfully',
            ], 200);

        } catch (\Exception $e){
            return new JsonResponse([
                'status' => 'error',
                'message' => 'An error occurred while deleting the habit.',
            ], 500);
        }
    }


    private function checkHabitOwnership(Habit $habit):?JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'User not authenticated.',
            ], 403);
        } 
         
        if($user->getId() !== $habit->getUser()->getId()) {
            return new JsonResponse([
                'status' => 'error',
                'message' =>  'You do not have permission to change this habit.',
            ], 403);
        }

        return null;

    }
}