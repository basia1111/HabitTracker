import "bootstrap/dist/js/bootstrap.bundle.min.js";
import "bootstrap/dist/css/bootstrap.min.css";
import * as bootstrap from "bootstrap";
import "./js/createHabit";
import "./js/editHabit";
import "./js/toggleHabitFormFields";
import "./js/createHabitElement";
import "./js/showErrorMessages";

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
});
