export function createHabitElement(habit) {
  const newHabit = document.createElement("div");
  newHabit.classList.add("habit", "all");
  newHabit.id = `habit_${habit.id}`;
  newHabit.innerHTML = `
      <div class="habit__color color_${habit.color}"></div>
      <div class="habit__data">
        <p class="habit__data--title">${habit.name}</p>
        <p class="habit__data--category">Category</p>
        <div class="habit__data--wrapper">
          ${
            habit.time
              ? `<span class="habit__data--time"><i class="fa-regular fa-clock"></i> ${habit.time}</span>`
              : ""
          }
          <span class="habit__data--reocurance">
            <i class="fa-regular fa-calendar"></i> 
            ${
              habit.frequency === "days"
                ? habit.weekDays
                    .map(
                      (day) => `<span class="habit__data--time">${day}</span>`
                    )
                    .join("")
                : habit.frequency
            }
          </span>
          <span class="habit__data--streak">
            <i class="fa-regular fa-star"></i> ${habit.streak}
          </span>
        </div>
      </div>
      <div class="habit__actions">
        <a class='habit__action' href='#'><i class='fa-regular fa-pen-to-square'></i></a>
        <a class='habit__action' href='#'><i class='fa-regular fa-trash-can'></i></a>
      </div>
    `;
  return newHabit;
}

function showErrorMessages(data) {
  const errorMessages = data.errors || [data.message];
  document.getElementById("createHabitError").innerHTML = errorMessages
    .map((error) => `<div class="alert alert-danger">${error}</div>`)
    .join("");
}
