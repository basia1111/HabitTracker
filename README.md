# Habit Tracker - PHP Symfony, Google Calendar Integration
This is a **Habit Tracker** web application built using **PHP Symfony**, **JavaScript**, **MySQL**, **Bootstrap**, **SCSS**, **Docker**, and integrated with **Google Calendar** and **Weather Data**. The app allows users to create and manage their daily habits while syncing them with Google Calendar, as well as view real-time weather information based on their geolocation.

<table>
  <tr>
    <td><img src="https://github.com/user-attachments/assets/6fc0157b-cb75-475b-b442-fcb1331e430a" alt="Screenshot 1" width="100%"></td>
    <td><img src="https://github.com/user-attachments/assets/4b9ad233-c713-42be-82d6-7d7092eaa29f" alt="Screenshot 2" width="100%"></td>
  </tr>
  <tr>
    <td><img src="https://github.com/user-attachments/assets/dfe9f515-5bd9-4ab5-afd2-18b9cf29a471" alt="Screenshot 3" width="100%"></td>
    <td><img src="https://github.com/user-attachments/assets/fc6e6edb-5360-4fed-988e-943047081701" alt="Screenshot 4" width="100%"></td>
  </tr>
</table>


## Usage

   - For testing purposes, you can log in with the following **test account** credentials:
     - **Email**: `habito.test.user@gmail.com`
     - **Password**: `Ypy0aalZ9HI3`
   - This account is already connected to Google, so you can use it to check the full functionality, including habit tracking and Google Calendar integration.

## Features

- **User Authentication:**
  - Users can log in using **Google OAuth** or create an account with **credentials** (email & password).
  
- **Habit Creation and Management:**
  - Users can **create habits** within the app, define their frequency (daily, weekly, etc.), and link them to their **Google Calendar**.
  - A user can **edit habits** in the app, and it will automatically update in the connected Google Calendar.
  - **Unlink** habits from Google Calendar while keeping the habit persistent in the app.

- **Google Calendar Integration:**
  - **Add habits to Google Calendar** from the app with the option to choose the **title, color, and frequency**.
  - **Real-time synchronization** ensures any changes made in the app (adding, updating, or deleting habits) reflect in the Google Calendar.
  - **Embedded Google Calendar** to display all user habits, updating in real time as changes are made.

- **Habit Categories & Scheduling:**
  - Habits are categorized into sections based on the time of day: **Morning, Afternoon, Evening, Night**, and **Unorganized**.

- **Habit Completion Tracking:**
  - Users can mark habits as **done** for each day, with visual feedback and stats tracking progress.

- **Weather Integration:**
  - The app fetches **real-time weather data** based on the user’s **geolocation** (using **Geolocation-DB**).
  - **OpenWeather API** is used to fetch the current weather conditions, weather details are shown dynamically to the user, enhancing the habit-tracking experience.
  - 
- **Responsive Design:**
  - The app is fully responsive, using **Bootstrap** and **SCSS** for styling, providing a smooth experience across different devices.

- **No Page Reloads:**
  - The app is built with **AJAX** and **JavaScript**, ensuring **real-time updates** without the need for page reloads.

## Technologies Used

- **Backend:**
  - PHP Symfony Framework
  - MySQL Database

- **Frontend:**
  - JavaScript (AJAX for real-time updates)
  - Bootstrap (for responsive design)
  - SCSS (for custom styling)

- **APIs:**
  - **Google Calendar API** (for calendar integration)
  - **OpenWeather API** (for weather data)
  - **Geolocation-DB** (for location-based weather fetching)

- **Docker:**
  - Docker is used for containerization, ensuring consistent development and production environments.

## Installation

1. **Clone the Repository:**
   ```
   git clone https://github.com/your-repo/habit-tracker.git
   cd habit-tracker
   ```

2. **Set up Docker:**
   - Build and start the Docker containers:
   ```
   docker-compose up --build
   ```

3. **Install Dependencies:**
   - Install PHP dependencies:
   ```
   composer install
   ```
   - Install frontend dependencies:
   ```
   npm install
   ```

4. **Set up Environment Variables:**
   - Copy the `.env.example` file to `.env`:
   ```
   cp .env.example .env
   ```
   - Set up your **Google API credentials**, **OpenWeather API key**, and **Geolocation-DB API key** in the `.env` file.

5. **Create the Database:**
   - Run migrations to create the necessary database tables:
   ```
   php bin/console doctrine:migrations:migrate
   ```

6. **Access the Application:**
   - Open your browser and visit:
   ```
   http://localhost:8080
   ```

## Contributing

1. Fork the repository.
2. Create a new branch (`git checkout -b feature/your-feature`).
3. Make your changes and commit them (`git commit -am 'Add new feature'`).
4. Push to the branch (`git push origin feature/your-feature`).
5. Create a new pull request.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

