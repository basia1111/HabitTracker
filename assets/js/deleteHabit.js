import * as bootstrap from "bootstrap";
import { showErrorMessagesDelete } from "./showErrorMessages";

const deleteModal = new bootstrap.Modal(
  document.getElementById("delete-habit-modal")
);

document
  .getElementById("dashboard-all-list")
  .addEventListener("click", function (e) {
    const button = e.target.closest(".delete-habit-btn");
    console.log("click");

    if (button) {
      const id = button.dataset.id;

      const deleteButton = document.getElementById("delete-habit");

      if (deleteButton.currentDeleteHandler) {
        deleteButton.removeEventListener(
          "click",
          deleteButton.currentDeleteHandler
        );
      }

      deleteButton.currentDeleteHandler = function () {
        handleDelete(id);
      };

      deleteButton.addEventListener("click", deleteButton.currentDeleteHandler);
      deleteModal.show();
    }
  });

function handleDelete(id) {
  fetch(`/habit/delete/${id}`, {
    method: "DELETE",
    headers: { "X-Requested-With": "XMLHttpRequest" },
  })
    .then((response) => response.text())
    .then((data) => {
      try {
        const jsonData = JSON.parse(data);
        if (jsonData.status === "success") {
          deleteModal.hide();
          const habitElement = document.getElementById(`habit_${id}`);
          if (habitElement) {
            habitElement.remove();
          }
        } else {
          showErrorMessagesDelete(jsonData);
        }
      } catch (error) {
        showErrorMessagesDelete({ errors: [error.message] });
      }
    })
    .catch((error) => {
      showErrorMessagesDelete({ errors: [error.message] });
    });
}
