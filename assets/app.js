import "bootstrap/dist/js/bootstrap.bundle.min.js";
import "bootstrap/dist/css/bootstrap.min.css";
import * as bootstrap from "bootstrap";

import "./js/createHabit";
import "./js/editHabit";
import "./js/deleteHabit";
import "./js/completeHabit";

import "./js/getWeather";

import "./js/googleCallendar/createEvent";
import "./js/googleCallendar/deleteEvent";
import "./js/googleCallendar/fetchEmbededCalendar";

import { WEATHER_ICONS } from "./js/habitCategories";

console.log("Bootstrap JS and CSS are loaded.");

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
