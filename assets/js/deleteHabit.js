import * as bootstrap from "bootstrap";
import { showErrorMessagesDelete } from "./habitFormHelperFunctions/showErrorMessages";
import { fetchTodayHabits, updateStats } from "./habitHelperFunctions";

// Initialize the delete modal
const deleteModal = new bootstrap.Modal(document.getElementById("delete-habit-modal"));

// Handle delete habit button click
document.getElementById("dashboard-all-list").addEventListener("click", function (e) {
  const button = e.target.closest(".delete-habit-btn");

  if (button) {
    const id = button.dataset.id;
    const deleteButton = document.getElementById("delete-habit");

    if (deleteButton.currentDeleteHandler) {
      deleteButton.removeEventListener("click", deleteButton.currentDeleteHandler);
    }

    deleteButton.currentDeleteHandler = function () {
      handleDelete(id);
    };

    deleteButton.addEventListener("click", deleteButton.currentDeleteHandler);
    deleteModal.show();
  }
});

// Handle habit deletion
async function handleDelete(id) {
  try {
    const response = await fetch(`/habit/delete/${id}`, {
      method: "DELETE",
      headers: { "X-Requested-With": "XMLHttpRequest" },
    });

    const data = await response.text(); // Get text response
    const jsonData = JSON.parse(data);

    if (jsonData.status === "success") {
      deleteModal.hide();

      // Remove habit from the list
      const habitElement = document.getElementById(`habit_${id}`);
      if (habitElement) habitElement.remove();
      updateStats(jsonData.stats);

      if (jsonData.stats.totalHabits === 0) {
        document.getElementById("dashboard-all-list").innerHTML = `
        <div id="dashboard-all__empty" class="dashboard-all__empty">
            <div class="dashboard-all__empty-content">
                <i class="bi bi-list-check dashboard-all__empty-icon"></i>
                <h2>Start your journey</h2>
                <p>Create your first habit and begin tracking your progress</p>
                <button type="button" data-bs-toggle="modal" data-bs-target="#createHabitModal">
                    <i class="fa-regular fa-plus"></i>
                    New Habit
                </button>
            </div>
        </div>
        `;
      }

      // If habit was today, refresh today’s habits
      const todayHabitElement = document.getElementById(`today__habit_${id}`);
      if (todayHabitElement) fetchTodayHabits();
    } else {
      showErrorMessagesDelete(jsonData);
    }
  } catch (error) {
    showErrorMessagesDelete({ errors: [error.message] });
  }
}
