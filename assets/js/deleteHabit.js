import * as bootstrap from "bootstrap";
import { showErrorMessagesDelete } from "./habitFormHelperFunctions/showErrorMessages";
import { fetchTodayHabits } from "./habitHelperFunctions";

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
