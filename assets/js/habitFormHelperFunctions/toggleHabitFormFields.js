// Function to toggle the visibility of the week days field based on the frequency selection
export function toggleWeekDays(prefix = "") {
  console.log(`${prefix}habit_form_frequency`);

  const frequencyField = document.getElementById(`${prefix}habit_form_frequency`);
  const weekDaysField = document.getElementById(`${prefix}habit_form_weekDays`);
  const selectedDays = document.getElementById(`${prefix}habit_form_frequency_3`);

  // Initially check if the "days" option is selected, if so show the week days field
  if (selectedDays.checked) {
    weekDaysField.style.display = "flex";
  } else {
    weekDaysField.style.display = "none";
  }

  // Add an event listener to the frequency field to toggle the week days field visibility
  frequencyField.addEventListener("change", function () {
    if (selectedDays.checked) {
      weekDaysField.style.display = "flex";
    } else {
      weekDaysField.style.display = "none";
    }
  });
}

// Function to toggle the visibility of the time input field based on the "has time" checkbox
export function toggleTime(prefix = "") {
  const toggleTimeField = document.getElementById(`${prefix}habit_form_hasTime`);
  const timeField = document.getElementById(`${prefix}habit_form_time`);

  // Initially check if the "has time" checkbox is checked, if so show the time field
  if (toggleTimeField.checked) {
    timeField.style.display = "block";
  } else {
    timeField.style.display = "none";
  }

  // Add an event listener to the checkbox to toggle the time field visibility
  toggleTimeField.addEventListener("change", function () {
    if (toggleTimeField.checked) {
      timeField.style.display = "block";
    } else {
      timeField.style.display = "none";
    }
  });
}

// Function to toggle the visibility of the time input field based on the "has time" checkbox
export function toggleTimeEdit(prefix = "") {
  const toggleTimeField = document.getElementById(`${prefix}habit_form_hasTime`);
  const timeField = document.getElementById(`${prefix}habit_form_time`);

  // If there's a time value present, show the time field and check the checkbox
  if (timeField.value.length !== 0) {
    timeField.style.display = "block";
    toggleTimeField.checked = true;
  } else {
    timeField.style.display = "none";
  }

  // Add an event listener to the checkbox to toggle the time field visibility
  toggleTimeField.addEventListener("change", function () {
    if (toggleTimeField.checked) {
      timeField.style.display = "block";
    } else {
      timeField.style.display = "none";
    }
  });
}
