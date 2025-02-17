import { formatTime } from "../habitHelperFunctions";

export function createHabitElement(habit) {
  const newHabit = document.createElement("div");
  newHabit.classList.add("habit", "all");
  newHabit.id = `habit_${habit.id}`;
  let formattedTime = null;

  if (habit.time && habit.time.date) {
    formattedTime = formatTime(habit.time.date);
  }

  newHabit.innerHTML = `
      <div class="habit__color color_${habit.color}"></div>
      <div class="habit__data">
        <p class="habit__data--title">${habit.name}</p>
        <p class="habit__data--category">Category</p>
        <div class="habit__data--wrapper">
          ${
            formattedTime
              ? `<span class="habit__data--time"><i class="fa-regular fa-clock"></i> ${formattedTime}</span>`
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
               <button class="habit__action edit-habit-btn" 
                data-id="${habit.id}" 
                data-url="/habit/edit/${habit.id}">
            <i class="fa-regular fa-pen-to-square"></i>
            </button>
            <button class="habit__action" href="{{path('app_main')}}">
                <i class="fa-regular fa-trash-can"></i>
            </button>
      </div>
    `;
  return newHabit;
}
