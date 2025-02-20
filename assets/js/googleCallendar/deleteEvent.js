import { fetchCalendar } from "./fetchEmbededCalendar";

document.getElementById("dashboard-all-list").addEventListener("click", async function (e) {
  const button = e.target.closest(".remove-from-calendar");

  if (button) {
    const id = button.dataset.id;
    const originalContent = button.innerHTML;
    button.innerHTML = "...";
    const habitStatus = button.closest(".habit-all").querySelector(".added-to-calendar");

    try {
      const response = await fetch(`/calendar/${id}/remove-from-calendar`, {
        method: "DELETE",
        headers: { "X-Requested-With": "XMLHttpRequest" },
      });

      const data = await response.json();

      if (response.ok) {
        const newStatus = document.createElement("button");
        newStatus.className = "add-to-callendar";
        newStatus.dataset.id = id;
        newStatus.innerHTML = `<i class="bi bi-calendar-plus"></i> Add to Calendar`;
        habitStatus.parentNode.replaceChild(newStatus, habitStatus);

        button.remove();

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
