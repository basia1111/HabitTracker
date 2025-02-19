<?php

namespace App\Controller;

use App\Form\HabitFormType;
use App\Entity\Habit;
use App\Interface\HabitServiceInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    private HabitServiceInterface $habitService;
    private HttpClientInterface $httpClient;
    private string $weatherApiKey;

    public function __construct(HabitServiceInterface $habitService, HttpClientInterface $httpClient)
    {
        $this->habitService = $habitService;
        $this->httpClient = $httpClient;
        $this->weatherApiKey = $_ENV['WEATHER_API_KEY']; 
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

        $totalHabits = count($habits);
        $totalTodayHabits = 0;
        foreach ($todayHabits as $categoryHabits) {
            $totalTodayHabits += count($categoryHabits);
        }

        $longestStreak = 0;
        $completedHabits = 0;

        foreach ($habits as $habit){
            if($habit->getStreak() > $longestStreak){
                $longestStreak = $habit->getStreak();
            }
        }

        foreach($todayHabits as $todayHabitCategory){
            foreach($todayHabitCategory as $habit){
                if($habit['is_completed']){
                    $completedHabits++;
                }
            }
        }

        return $this->render('dashboard/index.html.twig', [
            'habits' => $habits,
            'todayHabits' => $todayHabits,
            'createHabitForm' => $form->createView(),
            'totalHabits' => $totalHabits,
            'totalTodayHabits' =>  $totalTodayHabits,
            'longestStreak' => $longestStreak,
            'completedHabits' =>  $completedHabits,
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
    
        return new JsonResponse(['html' => $htmlContent]);
    }

    #[Route('/api/weather', name: 'api_weather', methods:'GET')]
    public function getWeather(): Response
    {
        try{
        $locationResponse = $this->httpClient->request('GET', "https://geolocation-db.com/json/");
        $locationData = $locationResponse->toArray();
        $city = $locationData['city'] ?? 'unknown';

        $weatherResponse = $this->httpClient->request('GET', "https://api.openweathermap.org/data/2.5/weather?q=".$city."&appid=".$this->weatherApiKey);
        $weatherData = $weatherResponse->toArray();
        $weather_description = $weatherData['weather'][0]['description'];
        $weather_temperature =  round($weatherData['main']['temp'] -  273.15, 2);
        $weather_icon =  $weatherData['weather'][0]['icon'];

        return new JsonResponse([
            'status' => 'success',
            'city' => $city,
            'desc' =>  $weather_description,
            'icon' => $weather_icon,
            'temp' => $weather_temperature 
        ], 200);

        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Data not avaliable'
            ], 500);
        }
    }   
}
