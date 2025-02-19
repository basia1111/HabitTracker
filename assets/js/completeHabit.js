import { updateStats } from "./habitHelperFunctions";

document.getElementById("dashboard-today-wrapper").addEventListener("click", async (e) => {
  const checkbox = e.target.closest(".habit__checkbox");

  console.log("click");
  if (checkbox) {
    const habitElement = checkbox.closest(".habit");
    const habitId = habitElement.querySelector(".habit__data--streak").dataset.id;

    try {
      const response = await fetch(`/habit/complete/${habitId}`, {
        method: "POST",
        headers: { "X-Requested-With": "XMLHttpRequest", "Content-Type": "application/json" },
      });

      const data = await response.json();

      if (data.status === "success") {
        // Update streak display
        habitElement.querySelector(".habit__data--streak").innerHTML = `<i class="fa-regular fa-star"></i> ${data.streak}`;
        document.querySelector(`#habit_${habitId} .habit__data--streak`).innerHTML = `<i class="fa-regular fa-star"></i> ${data.streak}`;

        // Toggle checked style
        if (data.completed) {
          habitElement.classList.add("habit--completed");
        } else {
          habitElement.classList.remove("habit--completed");
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
