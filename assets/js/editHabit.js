import * as bootstrap from "bootstrap";
import { toggleWeekDays, toggleTimeEdit } from "./habitFormHelperFunctions/toggleHabitFormFields";
import { showErrorMessagesEdit } from "./habitFormHelperFunctions/showErrorMessages";
import { fetchTodayHabits, checkIfToday, updateStats } from "./habitHelperFunctions";
import { createHabitElement } from "./createElements/createHabitElement";
import { fetchCalendar } from "./googleCallendar/fetchEmbededCalendar";

const editModal = new bootstrap.Modal(document.getElementById("editHabitModal"));

/* Handle clicking the "Edit" button for each habit */
document.getElementById("dashboard-all-list").addEventListener("click", async function (e) {
  const button = e.target.closest(".edit-habit-btn");

  if (button) {
    e.preventDefault();

    const url = button.dataset.url;
    const id = button.dataset.id;
    console.log("Fetching form from:", url);

    try {
      // Fetch the edit form HTML from the server
      const response = await fetch(url);
      const html = await response.text();

      document.querySelector("#editHabitModal .modal-body").innerHTML = html;
      editModal.show();

      initializeEditForm(id);
    } catch (error) {
      console.error("Error fetching form:", error);
      alert("Failed to load form.");
    }
  }
});

/* Initialize the edit form with necessary functionalities */
async function initializeEditForm(id) {
  const form = document.querySelector("#habit-edit-form");
  if (!form) {
    console.error("Form not found!");
    return;
  }

  // Toggle the weekdays and time fields for editing
  toggleWeekDays("edit_");
  toggleTimeEdit("edit_");

  console.log("form action", `/habit/update/${id}`);

  // Handle form submission for updating the habit
  form.addEventListener("submit", async function (e) {
    e.preventDefault();

    const formData = new FormData(form);

    try {
      const response = await fetch(`/habit/update/${id}`, {
        method: "POST",
        body: formData,
        headers: { "X-Requested-With": "XMLHttpRequest" },
      });

      const data = await response.text();
      const jsonData = JSON.parse(data);

      if (jsonData.status === "success") {
        editModal.hide();

        // Replace the updated habit element in the list
        const habitElement = document.getElementById(`habit_${jsonData.habit.id}`);
        if (habitElement) {
          const newHabit = createHabitElement(jsonData.habit);
          habitElement.parentNode.replaceChild(newHabit, habitElement);
        }

        updateStats(jsonData.stats);

        console.log(jsonData.stats);
        console.log(checkIfToday(jsonData.habit));
        // Refresh today's habits if the updated habit is for today
        fetchTodayHabits();
        if (jsonData.habit.googleEventId) fetchCalendar();
      } else {
        showErrorMessagesEdit(jsonData);
      }
    } catch (error) {
      showErrorMessagesEdit({ errors: [error.message] });
    }
  });
}
