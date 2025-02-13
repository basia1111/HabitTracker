import { createHabitElement } from "./createHabitElement";
import { showErrorMessages } from "./showErrorMessages";

const createForm = document.getElementById("habit-create-form");
const habitList = document.getElementById("dashboard-all-list");

createForm.addEventListener("submit", async (event) => {
  event.preventDefault();
  const formData = new FormData(event.target);

  try {
    const response = await fetch("/habit/create", {
      method: "POST",
      body: formData,
    });

    const data = await response.json();

    if (data.status === "success" && data.habit) {
      const closeButton = document.getElementById("closeCreateHabitModal");
      closeButton.click();

      const newHabit = createHabitElement(data.habit);
      habitList.prepend(newHabit);
    } else {
      showErrorMessages(data);
    }
  } catch (error) {
    showErrorMessages({ errors: [error.message] });
  }
});
