async function getWeather() {
  try {
    const response = await fetch("/api/weather");
    const data = await response.json();

    if (data.status === "success") {
      console.log(data);

      document.getElementById("dashboard-header__city").innerHTML = `<i class="bi bi-geo-alt"></i> ${data.city}`;
      document.getElementById("dashboard-header__weather").innerHTML = `<i class="bi ${WEATHER_ICONS[data.icon]}"></i> ${data.temp} °C`;
    } else {
      console.log(data.message);
    }
  } catch (error) {
    console.log(error);
  }
}
getWeather();
