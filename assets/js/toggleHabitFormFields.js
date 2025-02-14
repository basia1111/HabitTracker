export function toggleWeekDays(prefix = "") {
  console.log(`${prefix}habit_form_frequency`);
  const frequencyField = document.getElementById(
    `${prefix}habit_form_frequency`
  );
  const weekDaysField = document.getElementById(`${prefix}habit_form_weekDays`);
  const selectedDays = document.getElementById(
    `${prefix}habit_form_frequency_3`
  );

  if (selectedDays.checked) {
    weekDaysField.style.display = "flex";
  } else {
    weekDaysField.style.display = "none";
  }

  frequencyField.addEventListener("change", function () {
    if (selectedDays.checked) {
      weekDaysField.style.display = "flex";
    } else {
      weekDaysField.style.display = "none";
    }
  });
}

export function toggleTime(prefix = "") {
  const toggleTimeField = document.getElementById(
    `${prefix}habit_form_hasTime`
  );
  const timeField = document.getElementById(`${prefix}habit_form_time`);

  if (toggleTimeField.checked) {
    timeField.style.display = "block";
  } else {
    timeField.style.display = "none";
  }

  toggleTimeField.addEventListener("change", function () {
    if (toggleTimeField.checked) {
      timeField.style.display = "block";
    } else {
      timeField.style.display = "none";
    }

    console.log(toggleTimeField.value);
  });
}

export function toggleTimeEdit(prefix = "") {
  const toggleTimeField = document.getElementById(
    `${prefix}habit_form_hasTime`
  );
  const timeField = document.getElementById(`${prefix}habit_form_time`);

  if (timeField.value.length !== 0) {
    timeField.style.display = "block";
    toggleTimeField.checked = true;
  } else {
    timeField.style.display = "none";
  }

  toggleTimeField.addEventListener("change", function () {
    if (toggleTimeField.checked) {
      timeField.style.display = "block";
    } else {
      timeField.style.display = "none";
    }

    console.log(toggleTimeField.value);
  });
}
