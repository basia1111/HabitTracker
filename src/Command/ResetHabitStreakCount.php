<?php

namespace App\Command;

use App\Interface\HabitServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResetHabitStreakCount extends Command
{
    public function __construct(
        private HabitServiceInterface $habitServiceInterface
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('app:reset-habit-streaks')
            ->setDescription('Reset streaks for habits that missed their last expected completion');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $yesterday = (new \DateTime('yesterday'))->format('Y-m-d');
            $habits = $this->habitServiceInterface->getAllHabits();
            $resetCount = 0;

            foreach ($habits as $habit) {
                $shouldHaveCompletedYesterday = false;

                switch ($habit->getFrequency()) {
                    case 'daily':
                        $shouldHaveCompletedYesterday = true;
                        break;
                    case 'weekdays':
                        $yesterdayDayOfWeek = (int)(new \DateTime('yesterday'))->format('N');
                        $shouldHaveCompletedYesterday = $yesterdayDayOfWeek <= 5;
                        break;
                    case 'weekends':
                        $yesterdayDayOfWeek = (int)(new \DateTime('yesterday'))->format('N');
                        $shouldHaveCompletedYesterday = $yesterdayDayOfWeek >= 6;
                        break;
                    case 'days':
                        $yesterdayDayName = strtolower((new \DateTime('yesterday'))->format('D'));
                        $weekDays = $habit->getWeekDays() ?? [];
                        $shouldHaveCompletedYesterday = in_array($yesterdayDayName, $weekDays);
                        break;
                }

                if ($shouldHaveCompletedYesterday && !in_array($yesterday, $habit->getCompletions())) {
                    $habit->setStreak(0);
                    $this->habitServiceInterface->save($habit);
                    $resetCount++;
                    $output->writeln(sprintf('Reset streak for habit: %s', $habit->getName()));
                }
            }

            $output->writeln(sprintf("Reset %d habit streaks", $resetCount));
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Error: %s</error>', $e->getMessage()));
            return Command::FAILURE;
        }
    }
}