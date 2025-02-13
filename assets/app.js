import "bootstrap/dist/js/bootstrap.bundle.min.js";
import "bootstrap/dist/css/bootstrap.min.css";
import * as bootstrap from "bootstrap";
import "./js/createHabitElement";
import "./js/handleCreateForm";
import "./toogleWeekDays";

console.log("Bootstrap JS and CSS are loaded.");

document.addEventListener("DOMContentLoaded", function () {
  document
    .querySelectorAll('[data-bs-toggle="popover"]')
    .forEach((popoverTriggerEl) => {
      new bootstrap.Popover(popoverTriggerEl, {
        html: true,
        sanitize: false,
      });
    });

  const frequencyField = document.getElementById("habit_form_frequency");
  const weekDaysField = document.getElementById("habit_form_weekDays");
  const selectedDays = document.getElementById("habit_form_frequency_3");

  weekDaysField.style.display = "none";

  frequencyField.addEventListener("change", function () {
    if (selectedDays.checked) {
      weekDaysField.style.display = "flex";
    } else {
      weekDaysField.style.display = "none";
    }
  });

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

        const newHabit = document.createElement("div");
        newHabit.classList.add("habit", "all");
        newHabit.id = `habit_${data.habit.id}`;
        newHabit.innerHTML = `
          <div class="habit__color color_${data.habit.color}"></div>
          <div class="habit__data">
            <p class="habit__data--title">${data.habit.name}</p>
            <p class="habit__data--category">Category</p>
            <div class="habit__data--wrapper">
              ${
                data.habit.time
                  ? `<span class="habit__data--time"><i class="fa-regular fa-clock"></i> ${data.habit.time}</span>`
                  : ""
              }
              <span class="habit__data--reocurance">
                <i class="fa-regular fa-calendar"></i> 
                ${
                  data.habit.frequency === "days"
                    ? data.habit.weekDays
                        .map(
                          (day) =>
                            `<span class="habit__data--time">${day}</span>`
                        )
                        .join("")
                    : data.habit.frequency
                }
              </span>
              <span class="habit__data--streak"><i class="fa-regular fa-star"></i> ${
                data.habit.streak
              }</span>
            </div>
          </div>
          <div class="habit__actions">
            <a class='habit__action' href='#'><i class='fa-regular fa-pen-to-square'></i></a>
            <a class='habit__action' href='#'><i class='fa-regular fa-trash-can'></i></a>
          </div>
        `;
        habitList.prepend(newHabit);
      } else {
        // Display errors in the modal
        const errorMessages = data.errors || [data.message];
        document.getElementById("createHabitError").innerHTML = errorMessages
          .map((error) => `<div class="alert alert-danger">${error}</div>`)
          .join("");
      }
    } catch (error) {
      document.getElementById("createHabitError").innerText = error;
    }
  });
});
