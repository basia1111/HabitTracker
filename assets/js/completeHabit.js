import { updateStats } from "./habitHelperFunctions";

document.getElementById("dashboard-today-wrapper").addEventListener("click", async (e) => {
  const checkbox = e.target.closest(".habit-today__checkbox");

  console.log(checkbox);
  if (checkbox) {
    const todayHabitElement = checkbox.closest(".habit-today");
    const habitId = checkbox.dataset.id;

    console.log(todayHabitElement);
    try {
      const response = await fetch(`/habit/complete/${habitId}`, {
        method: "POST",
        headers: { "X-Requested-With": "XMLHttpRequest", "Content-Type": "application/json" },
      });

      const data = await response.json();

      if (data.status === "success") {
        // Update streak display
        console.log(todayHabitElement);
        todayHabitElement.querySelector(".habit-today__data--streak").innerHTML = `<i class="fa-regular fa-star"></i> ${data.streak}`;
        document.querySelector(`#habit_${habitId} .habit-all__streak`).innerHTML = `<i class="fa-regular fa-star"></i> ${data.streak}`;

        console.log(todayHabitElement);

        // Toggle checked style
        if (data.completed) {
          todayHabitElement.classList.add("habit-today--completed");
        } else {
          todayHabitElement.classList.remove("habit-today--completed");
        }

        updateStats(data.stats);
      } else {
        console.error("Failed to update habit:", data.message);
      }
    } catch (error) {
      console.error("Error completing habit:", error);
    }
  }
});
