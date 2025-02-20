import { fetchCalendar } from "./fetchEmbededCalendar";

document.getElementById("dashboard-all-list").addEventListener("click", async function (e) {
  const button = e.target.closest(".add-to-callendar");

  if (button) {
    const id = button.dataset.id;
    const originalContent = button.innerHTML;
    button.innerHTML = "Loading...";
    const habitActions = button.closest(".habit-all").querySelector(".habit-all__actions");

    try {
      const response = await fetch(`/calendar/${id}/add-to-calendar`, {
        method: "POST",
        headers: { "X-Requested-With": "XMLHttpRequest" },
      });

      const data = await response.json();

      if (response.ok) {
        const newStatus = document.createElement("span");
        newStatus.className = "added-to-calendar";
        newStatus.innerHTML = `<i class="bi bi-calendar-check"></i>`;
        button.parentNode.replaceChild(newStatus, button);

        const newAction = document.createElement("button");
        newAction.className = "habit-all__action remove-from-calendar";
        newAction.dataset.id = id;
        newAction.innerHTML = `<i class="bi bi-calendar-x"></i>`;

        habitActions.prepend(newAction);

        fetchCalendar();
      } else {
        button.innerHTML = data.error || "Error occurred";
      }
    } catch (error) {
      button.innerHTML = originalContent;
      console.error("Error:", error);
    }
  }
});
