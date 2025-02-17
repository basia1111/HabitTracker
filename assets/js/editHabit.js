import * as bootstrap from "bootstrap";
import { toggleWeekDays, toggleTimeEdit } from "./toggleHabitFormFields";
import { showErrorMessagesEdit } from "./showErrorMessages";

const editModal = new bootstrap.Modal(
  document.getElementById("editHabitModal")
);

/*get onclick edit form for each habit*/
document
  .getElementById("dashboard-all-list")
  .addEventListener("click", function (e) {
    const button = e.target.closest(".edit-habit-btn");

    if (button) {
      e.preventDefault();

      const url = button.dataset.url;
      const id = button.dataset.id;
      console.log("Fetching form from:", url);

      fetch(url)
        .then((response) => response.text())
        .then((html) => {
          document.querySelector("#editHabitModal .modal-body").innerHTML =
            html;
          editModal.show();
          initializeEditForm(id);
        })
        .catch((error) => {
          console.error("Error fetching form:", error);
          alert("Failed to load form.");
        });
    }
  });

/*send edit form*/
function initializeEditForm(id) {
  const form = document.querySelector("#habit-edit-form");
  if (!form) {
    console.error("Form not found!");
    return;
  }
  toggleWeekDays("edit_");
  toggleTimeEdit("edit_");

  console.log("form action", `/habit/update/${id}`);
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(form);

    fetch(`/habit/update/${id}`, {
      method: "POST",
      body: formData,
      headers: { "X-Requested-With": "XMLHttpRequest" },
    })
      .then((response) => response.text())
      .then((data) => {
        return JSON.parse(data);
      })
      .then((data) => {
        if (data.status === "success") {
          editModal.hide();
          location.reload();
        } else {
          showErrorMessagesEdit(data);
        }
      })
      .catch((error) => {
        showErrorMessagesEdit({ errors: [error.message] });
      });
  });
}
