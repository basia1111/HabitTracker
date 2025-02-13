export function showErrorMessages(data) {
  const errorMessages = data.errors || [data.message];
  document.getElementById("createHabitError").innerHTML = errorMessages
    .map((error) => `<div class="alert alert-danger">${error}</div>`)
    .join("");
}
