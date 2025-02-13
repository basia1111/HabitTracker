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
