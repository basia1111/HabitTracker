import { formatTime } from "../habitHelperFunctions";

export function createHabitElement(habit) {
  const newHabit = document.createElement("div");
  newHabit.classList.add("habit-all");
  newHabit.id = `habit_${habit.id}`;
  let formattedTime = null;

  if (habit.time && habit.time.date) {
    formattedTime = formatTime(habit.time.date);
  }

  newHabit.innerHTML = `
    <div class="habit-all__color color_${habit.color}"></div>

    <div class="habit-all__info">
        <p class="habit-all__title">${habit.name}</p>

        <div class="habit-all__details">
            ${formattedTime ? `<span class="habit-all__time"><i class="fa-regular fa-clock"></i> ${formattedTime}</span>` : ""}
            
            <span class="habit-all__recurrence">
                <i class="fa-regular fa-calendar"></i>
                ${
                  habit.frequency === "days"
                    ? `<span class="habit-all__days">${habit.weekDays.map((day) => `<span class="habit-all__day">${day}</span>`).join(" ")}</span>`
                    : habit.frequency
                }
            </span>

            <span class="habit-all__streak">
                <i class="fa-regular fa-star"></i> ${habit.streak}
            </span>

            <span class="habit-all__calendar">
                ${
                  habit.googleEventId
                    ? `<span class="added-to-calendar">
                    <span><i class="bi bi-calendar-check"></i> Added to callendar </span>
                </span>`
                    : `<button class="add-to-callendar" data-id="${habit.id}" ><i class="bi bi-calendar-plus"></i> Add to Calendar</button>`
                }
            </span>

    
        </div>
    </div>

    <div class="habit-all__actions">
        ${
          habit.googleEventId ? `<button class="habit-all__action remove-from-calendar"  data-id="${habit.id}" ><i class="bi bi-calendar-x"></i> </button>` : ""
        }
        <button class="habit-all__action habit-all__action--edit edit-habit-btn" 
            data-id="${habit.id}" 
            data-url="/habit/edit/${habit.id}">
            <i class="fa-regular fa-pen-to-square"></i>
        </button>

        <button class="habit-all__action habit-all__action--delete delete-habit-btn" 
            data-id="${habit.id}" 
            data-url="/habit/delete/${habit.id}">
            <i class="fa-regular fa-trash-can"></i>
        </button>
    </div>
  `;

  return newHabit;
}
