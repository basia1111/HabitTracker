import { createHabitElement } from "./createElements/createHabitElement";
import { showErrorMessagesCreate } from "./habitFormHelperFunctions/showErrorMessages";
import { toggleWeekDays, toggleTime } from "./habitFormHelperFunctions/toggleHabitFormFields";
import { fetchTodayHabits, checkIfToday, updateStats } from "./habitHelperFunctions";

const createForm = document.getElementById("habit-create-form");
const habitList = document.getElementById("dashboard-all-list");

// Initialize form field toggles
toggleWeekDays();
toggleTime();

// Handle form submission
createForm.addEventListener("submit", async (event) => {
  event.preventDefault();
  const formData = new FormData(event.target);

  try {
    const response = await fetch("/habit/create", { method: "POST", body: formData });
    const data = await response.json();

    if (data.status === "success" && data.habit) {
      document.getElementById("closeCreateHabitModal").click();

      // Create the new habit element and add it to the list
      const newHabit = createHabitElement(data.habit);
      habitList.prepend(newHabit);

      updateStats(data.stats);

      // If the habit is for today, refresh today's habits list
      console.log(checkIfToday(data.habit));
      if (checkIfToday(data.habit)) {
        fetchTodayHabits();
      }
    } else {
      showErrorMessagesCreate(data);
    }
  } catch (error) {
    showErrorMessagesCreate({ errors: [error.message] });
  }
});
