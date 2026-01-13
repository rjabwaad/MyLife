# 🌸 MyLife - Your Personal Life Companion 💖

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind-CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

*A beautiful, all-in-one personal life management application built with love* 💕

[Features](#-features) • [Installation](#-installation) • [Tech Stack](#-tech-stack) • [API Keys](#-api-keys-setup)

</div>

---

## ✨ About MyLife

**MyLife** is your personal digital companion designed to help you organize, track, and beautify every aspect of your daily life. From managing your finances to tracking your mood, from planning your day to staying connected with your faith - MyLife has got you covered! 🌟

Built with a lovely pink aesthetic and intuitive design, MyLife makes personal management not just easy, but delightful! 🎀

---

## 🎯 Features

### 💰 Finance Tracker
Track your financial life with ease and style!
- **📈 Income Tracking** - Record all your revenue sources (salary, freelance, investments)
- **💸 Expense Management** - Monitor your spending with categories
- **💳 Debt Tracker** - Keep track of debts with payment progress bars
- **🎁 Wishlist** - Save items you want to buy with priority levels
- **📊 Real-time Statistics** - Daily and monthly financial insights
- **💵 Balance Calculator** - Automatic monthly balance calculation

### 📅 Smart Calendar
Never miss an important event again!
- **Full Calendar View** - Month, week, and day views
- **Event Management** - Create, edit, and delete events with ease
- **Color-coded Events** - Visual organization at a glance
- **Drag & Drop** - Reschedule events effortlessly
- **French Localization** - Beautiful French interface

### 🌤️ Weather Widget
Stay prepared for any weather!
- **Real-time Weather** - Current conditions for any city
- **Detailed Info** - Temperature, humidity, wind speed, and feels-like
- **Beautiful Display** - Elegant weather cards with icons
- **City Search** - Check weather anywhere in the world

### 🕌 Prayer Times
Stay connected with your faith!
- **5 Daily Prayers** - Fajr, Dhuhr, Asr, Maghrib, Isha
- **Location-based** - Accurate times for your city
- **Beautiful Display** - Elegant prayer time cards
- **Next Prayer Indicator** - Know what's coming next

### 💭 Mood Analyzer
Understand your emotions better!
- **AI-Powered Analysis** - Using OpenAI's advanced models
- **Emotion Detection** - Identify happiness, sadness, anger, fear, and more
- **Confidence Scores** - Know how accurate the analysis is
- **Beautiful Emojis** - Visual representation of your mood
- **Personalized Advice** - Get suggestions based on your emotions

### 🔐 Secure Authentication
Your data, protected!
- **Google OAuth** - Quick and secure login
- **User Profiles** - Personalized experience
- **Session Management** - Safe and reliable

---

## 🚀 Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 5.7+ or MariaDB
- Node.js & NPM (optional, for asset compilation)

### Step-by-Step Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/rjabwaad/MyLife.git
   cd MyLife
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Set up environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure your `.env` file**
   ```env
   DB_DATABASE=mylife
   DB_USERNAME=your_username
   DB_PASSWORD=your_password

   OPENAI_API_KEY=your_openai_key
   GOOGLE_CLIENT_ID=your_google_client_id
   GOOGLE_CLIENT_SECRET=your_google_client_secret
   GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
   WEATHER_API_KEY=your_weather_api_key
   ```

5. **Create database**
   ```bash
   mysql -u root -p
   CREATE DATABASE mylife;
   exit;
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

8. **Visit your app**
   Open your browser and go to `http://localhost:8000` 🎉

---

## 🔑 API Keys Setup

### OpenAI API (for Mood Analyzer)
1. Go to [OpenAI Platform](https://platform.openai.com/)
2. Create an account or sign in
3. Navigate to API Keys section
4. Create a new secret key
5. Add to `.env`: `OPENAI_API_KEY=sk-...`

### Google OAuth (for Authentication)
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project
3. Enable Google+ API
4. Create OAuth 2.0 credentials
5. Add authorized redirect URI: `http://localhost:8000/auth/google/callback`
6. Add credentials to `.env`

### WeatherAPI (for Weather Widget)
1. Go to [WeatherAPI](https://www.weatherapi.com/)
2. Sign up for a free account
3. Get your API key from the dashboard
4. Add to `.env`: `WEATHER_API_KEY=your_key`

---

## 🛠️ Tech Stack

### Backend
- **Laravel 11.x** - The PHP framework for web artisans
- **PHP 8.2+** - Modern PHP with all the latest features
- **MySQL** - Reliable database management

### Frontend
- **Tailwind CSS** - Utility-first CSS framework
- **FullCalendar** - Beautiful interactive calendars
- **Vanilla JavaScript** - Fast and lightweight

### APIs & Services
- **OpenAI API** - Advanced AI for mood analysis
- **Google OAuth** - Secure authentication
- **WeatherAPI** - Real-time weather data
- **Aladhan API** - Accurate Islamic prayer times

---

## 📁 Project Structure

```
MyLife/
├── app/
│   ├── Http/Controllers/
│   │   ├── FinanceController.php      # 💰 Finance management
│   │   ├── CalendarEventController.php # 📅 Calendar events
│   │   ├── EmotionController.php       # 💭 Mood analysis
│   │   ├── WeatherController.php       # 🌤️ Weather data
│   │   └── prayerController.php        # 🕌 Prayer times
│   └── Models/
│       ├── Income.php                   # Income model
│       ├── Expense.php                  # Expense model
│       ├── Debt.php                     # Debt model
│       └── Wishlist.php                 # Wishlist model
├── database/migrations/                 # Database structure
├── resources/views/
│   ├── home.blade.php                   # Main dashboard
│   └── finance.blade.php                # Finance tracker page
└── routes/web.php                       # Application routes
```

---

## 🎨 Features Showcase

### Finance Tracker Dashboard
- Beautiful cards showing daily and monthly statistics
- Real-time balance calculation
- Color-coded income (green) and expenses (red)
- Interactive modals for adding data
- Responsive design for mobile and desktop

### Calendar Integration
- Drag and drop events
- Multiple view options (month, week, day, list)
- Color-coded events for easy identification
- Quick event creation with click

### Smart Widgets
- Live weather updates with beautiful icons
- Prayer times with next prayer highlighting
- Mood analyzer with emoji representations
- All widgets update in real-time

---

## 🌟 Why MyLife?

- **All-in-One Solution** - Everything you need in one beautiful app
- **Beautiful Design** - Lovely pink aesthetic that's easy on the eyes
- **User-Friendly** - Intuitive interface that anyone can use
- **Secure** - Your data is protected with modern security practices
- **Free & Open Source** - Use it, modify it, make it yours!
- **Active Development** - Constantly improving and adding features

---

## 📝 To-Do / Future Features

- [ ] Mobile app version (React Native)
- [ ] Dark mode toggle
- [ ] Export financial data to PDF/Excel
- [ ] Budget planning and alerts
- [ ] Habit tracker
- [ ] Goal setting and tracking
- [ ] Multi-language support
- [ ] Data backup and restore

---

## 🤝 Contributing

Contributions are welcome! Feel free to:
- Report bugs
- Suggest new features
- Submit pull requests
- Improve documentation

---

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).

---

## 💖 Made with Love

Created with 💕 by someone who believes life management should be beautiful and fun!

If you find this project helpful, give it a ⭐ on GitHub!

---

## 📧 Contact

For questions, suggestions, or just to say hi:
- GitHub: [@rjabwaad](https://github.com/rjabwaad)
- Project Link: [https://github.com/rjabwaad/MyLife](https://github.com/rjabwaad/MyLife)

---

<div align="center">

**Happy Life Managing! 🌸✨**

Made with ❤️ using Laravel

</div>
```
