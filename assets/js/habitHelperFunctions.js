import { HABIT_CATEGORIES } from "./habitCategories";

// Function to format time (returns hours:minutes)
export function formatTime(time) {
  const habitTime = time;
  const timePart = habitTime.split(" ")[1];
  const [hours, minutes] = timePart.split(":");
  return `${hours}:${minutes}`;
}

// Function to check if a habit is due today based on its frequency
export function checkIfToday(habit) {
  const today = new Date().getDay();
  const weekDayMap = {
    0: "sun",
    1: "mon",
    2: "tue",
    3: "wed",
    4: "thu",
    5: "fri",
    6: "sat",
  };

  const currentWeekDay = weekDayMap[today];

  switch (habit.frequency) {
    case "daily":
      return true;

    case "weekdays":
      return today >= 1 && today <= 5;
    case "weekends":
      return today === 0 || today === 6;

    case "days":
      return habit.weekDays.includes(currentWeekDay);

    default:
      return false;
  }
}

// Function to fetch today's habits and update the DOM with the fetched data
export async function fetchTodayHabits() {
  try {
    const response = await fetch("/api/today-habits");

    if (!response.ok) {
      throw new Error("Failed to fetch habits");
    }

    const data = await response.json();

    if (data.error) {
      console.error(data.error);
      return;
    }

    const container = document.getElementById("dashboard-today-wrapper");
    container.innerHTML = data.html;
  } catch (error) {
    console.error("Error fetching habits:", error);
  }
}
