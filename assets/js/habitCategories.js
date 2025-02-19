export const HABIT_CATEGORIES = {
  morning: {
    icon: "bi-sun",
    title: "Morning",
    startTime: "05:00",
    endTime: "11:59",
  },
  afternoon: {
    icon: "bi-brightness-alt-high",
    title: "Afternoon",
    startTime: "12:00",
    endTime: "17:59",
  },
  evening: {
    icon: "bi-moon",
    title: "Evening",
    startTime: "18:00",
    endTime: "23:59",
  },
  night: {
    icon: "bi-moon-stars",
    title: "Night",
    startTime: "00:00:",
    endTime: "04:59",
  },
  unscheduled: {
    icon: "bi-clock",
    title: "Unscheduled",
    startTime: null,
    endTime: null,
  },
};

export const WEATHER_ICONS = {
  "01d": "bi-sun", // clear sky (day)
  "01n": "bi-moon", // clear sky (night)

  "02d": "bi-cloud-sun", // few clouds (day)
  "02n": "bi-cloud-moon", // few clouds (night)

  "03d": "bi-cloud", // scattered clouds (day)
  "03n": "bi-cloud", // scattered clouds (night)

  "04d": "bi-clouds", // broken clouds (day)
  "04n": "bi-clouds", // broken clouds (night)

  "09d": "bi-cloud-rain-heavy", // shower rain (day)
  "09n": "bi-cloud-rain-heavy", // shower rain (night)
  "10d": "bi-cloud-sun-rain", // rain (day)
  "10n": "bi-cloud-moon-rain", // rain (night)

  "11d": "bi-cloud-lightning", // thunderstorm (day)
  "11n": "bi-cloud-lightning", // thunderstorm (night)

  "13d": "bi-snow", // snow (day)
  "13n": "bi-snow", // snow (night)

  "50d": "bi-cloud-haze", // mist (day)
  "50n": "bi-cloud-haze", // mist (night)
};
