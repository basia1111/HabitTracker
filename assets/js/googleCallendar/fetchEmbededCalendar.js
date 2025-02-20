document.addEventListener("DOMContentLoaded", fetchCalendar);

export async function fetchCalendar() {
  const calendarContainer = document.getElementById("google-calendar-view");

  if (!calendarContainer) return;
  try {
    const response = await fetch("/calendar/view", {
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    });

    const data = await response.json();

    if (data.success) {
      const iframe = document.createElement("iframe");
      iframe.src = data.url;
      iframe.width = "100%";
      iframe.height = "600";
      iframe.style.border = "0";

      calendarContainer.innerHTML = ""; // Clear any loading state
      calendarContainer.appendChild(iframe);
    } else {
      calendarContainer.innerHTML = `
                <div class="calendar-error">
                    <p>${data.error || "Failed to load calendar"}</p>
                </div>
            `;
    }
  } catch (error) {
    console.error("Error loading calendar:", error);
    calendarContainer.innerHTML = `
            <div class="calendar-error">
                <p>Failed to load calendar. Please try again later.</p>
            </div>
        `;
  }
}
