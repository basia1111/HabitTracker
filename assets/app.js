import "bootstrap/dist/js/bootstrap.bundle.min.js";
import "bootstrap/dist/css/bootstrap.min.css";
import * as bootstrap from "bootstrap";

import "./js/createHabit";
import "./js/editHabit";
import "./js/deleteHabit";
import "./js/completeHabit";

import { WEATHER_ICONS } from "./js/habitCategories";

console.log("Bootstrap JS and CSS are loaded.");

/*async function getUserCity() {
  try {
    const response = await fetch("https://geolocation-db.com/json/");
    const data = await response.json();
    console.log(data.city);

    getCityWeather(data.city);
  } catch (error) {
    console.error("Error:", error);
  }
}

async function getCityWeather(city) {
  try {
    const response = await fetch(`https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=b4c10b7d850692fc030b29071c317607`);
    const data = await response.json();
    console.log(data.weather[0].icon);
    console.log(data.main.temp);
  } catch (error) {
    console.error("Error:", error);
  }
}

getUserCity();
*/

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
