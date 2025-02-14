export function showErrorMessagesCreate(data) {
  const errorMessages = data.errors || [data.message];
  document.getElementById("createHabitError").innerHTML = errorMessages
    .map((error) => `<div class="alert alert-danger">${error}</div>`)
    .join("");
}
export function showErrorMessagesEdit(data) {
  const errorMessages = data.errors || [data.message];
  document.getElementById("habit-edit-errors").innerHTML = errorMessages
    .map((error) => `<div class="alert alert-danger">${error}</div>`)
    .join("");
}
