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
      deleteModal.show();
      const id = button.dataset.id;
      const deleteButton = document.getElementById("delete-habit");

      const newDeleteButton = deleteButton.cloneNode(true);
      deleteButton.parentNode.replaceChild(newDeleteButton, deleteButton);

      newDeleteButton.addEventListener("click", () => {
        fetch(`/habit/delete/${id}`, {
          method: "DELETE",
          headers: { "X-Requested-With": "XMLHttpRequest" },
        })
          .then((response) => response.text())
          .then((data) => {
            console.log(JSON.parse(data));
            return JSON.parse(data);
          })
          .then((data) => {
            console.log(data.status);
            if (data.status === "success") {
              deleteModal.hide();
              document.getElementById(`habit_${id}`).remove();
            } else {
              showErrorMessagesDelete(data);
            }
          })
          .catch((error) => {
            showErrorMessagesDelete({ errors: [error.message] });
          });
      });
    }
  });
