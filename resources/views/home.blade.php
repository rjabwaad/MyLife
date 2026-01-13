<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Home</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
</head>
<body class="bg-pink-50 min-h-screen font-sans">

<div class="max-w-7xl mx-auto p-6">

    <!-- Welcome Message -->
    <h1 class="text-3xl md:text-5xl font-bold text-center text-pink-600 mb-6 animate-fade-in">
        🌸 Welcome, Lovely! Let's plan your day 💖
    </h1>

    <!-- Weather Widget -->
    <div class="bg-gradient-to-r from-pink-100 to-purple-100 rounded-2xl shadow-lg p-6 mb-6">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Weather Search Form -->
            <form method="GET" action="/weather-view" class="flex-1 w-full md:w-auto">
                <div class="flex gap-2">
                    <input
                        type="text"
                        name="city"
                        placeholder="🔍 Rechercher une ville..."
                        value="{{ request('city', 'Tunis') }}"
                        class="flex-1 px-4 py-3 border-2 border-pink-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                    >
                    <button
                        type="submit"
                        class="bg-pink-500 text-white px-6 py-3 rounded-lg hover:bg-pink-600 transition font-semibold shadow-md hover:shadow-lg transform hover:scale-105"
                    >
                        🌤️ Météo
                    </button>
                </div>
            </form>

            <!-- Weather Display -->
            @if(isset($weather) && $weather)
                <div class="flex-1 bg-white rounded-xl shadow-md p-4 w-full md:w-auto">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-pink-600">
                                📍 {{ $weather['location']['name'] }}
                            </h3>
                            <p class="text-gray-600 text-sm">
                                {{ $weather['location']['country'] }}
                            </p>
                            <p class="text-gray-500 text-xs mt-1">
                                ⏰ {{ \Carbon\Carbon::parse($weather['location']['localtime'])->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div class="text-center">
                            <div class="text-5xl font-bold text-pink-600">
                                {{ round($weather['current']['temp_c']) }}°
                            </div>
                            <p class="text-gray-600 text-sm mt-1">
                                {{ $weather['current']['condition']['text'] }}
                            </p>
                        </div>
                    </div>

                    <!-- Additional Weather Info -->
                    <div class="grid grid-cols-3 gap-2 mt-4 pt-4 border-t border-pink-200">
                        <div class="text-center">
                            <p class="text-xs text-gray-500">💨 Vent</p>
                            <p class="font-semibold text-pink-600">{{ $weather['current']['wind_kph'] }} km/h</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500">💧 Humidité</p>
                            <p class="font-semibold text-pink-600">{{ $weather['current']['humidity'] }}%</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500">🌡️ Ressenti</p>
                            <p class="font-semibold text-pink-600">{{ round($weather['current']['feelslike_c']) }}°</p>
                        </div>
                    </div>
                </div>
            @elseif(isset($weatherError))
                <div class="flex-1 bg-red-100 border border-red-300 rounded-xl p-4 w-full md:w-auto">
                    <p class="text-red-600 font-semibold">❌ {{ $weatherError }}</p>
                </div>
            @else
                <div class="flex-1 bg-pink-50 border-2 border-dashed border-pink-300 rounded-xl p-4 w-full md:w-auto text-center">
                    <p class="text-pink-600 font-semibold">🌤️ Recherchez une ville pour voir la météo</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Prayer Times Widget -->
    <div class="bg-gradient-to-r from-purple-100 to-pink-100 rounded-2xl shadow-lg p-6 mb-6">
        <div class="flex flex-col gap-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-purple-600 flex items-center gap-2">
                    🕌 Horaires de Prière
                </h2>
                <div class="flex gap-2 items-center">
                    <select
                        id="prayerCity"
                        class="px-4 py-2 border-2 border-purple-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition bg-white"
                    >
                        <option value="Tunis,Tunisia">🇹🇳 Tunis</option>
                        <option value="Paris,France">🇫🇷 Paris</option>
                        <option value="Cairo,Egypt">🇪🇬 Cairo</option>
                        <option value="Mecca,Saudi Arabia">🇸🇦 Mecca</option>
                        <option value="Medina,Saudi Arabia">🇸🇦 Medina</option>
                        <option value="Istanbul,Turkey">🇹🇷 Istanbul</option>
                        <option value="Dubai,UAE">🇦🇪 Dubai</option>
                        <option value="London,UK">🇬🇧 London</option>
                    </select>
                    <button
                        onclick="loadPrayerTimes()"
                        class="bg-purple-500 text-white px-6 py-2 rounded-lg hover:bg-purple-600 transition font-semibold shadow-md hover:shadow-lg transform hover:scale-105"
                    >
                        🕌 Charger
                    </button>
                </div>
            </div>

            <!-- Prayer Times Display -->
            <div id="prayerTimesResult" class="hidden">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <!-- City and Date Info -->
                    <div class="text-center mb-6 pb-4 border-b-2 border-purple-200">
                        <h3 id="prayerCityName" class="text-xl font-bold text-purple-600 mb-1"></h3>
                        <p id="prayerDate" class="text-gray-600 text-sm"></p>
                    </div>

                    <!-- Prayer Times Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <!-- Fajr -->
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 text-center border-2 border-blue-200 hover:shadow-lg transition">
                            <div class="text-3xl mb-2">🌅</div>
                            <h4 class="font-bold text-blue-700 mb-1">Fajr</h4>
                            <p id="fajrTime" class="text-2xl font-bold text-blue-900">--:--</p>
                        </div>

                        <!-- Dhuhr -->
                        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-4 text-center border-2 border-yellow-200 hover:shadow-lg transition">
                            <div class="text-3xl mb-2">☀️</div>
                            <h4 class="font-bold text-yellow-700 mb-1">Dhuhr</h4>
                            <p id="dhuhrTime" class="text-2xl font-bold text-yellow-900">--:--</p>
                        </div>

                        <!-- Asr -->
                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-4 text-center border-2 border-orange-200 hover:shadow-lg transition">
                            <div class="text-3xl mb-2">🌤️</div>
                            <h4 class="font-bold text-orange-700 mb-1">Asr</h4>
                            <p id="asrTime" class="text-2xl font-bold text-orange-900">--:--</p>
                        </div>

                        <!-- Maghrib -->
                        <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-lg p-4 text-center border-2 border-pink-200 hover:shadow-lg transition">
                            <div class="text-3xl mb-2">🌆</div>
                            <h4 class="font-bold text-pink-700 mb-1">Maghrib</h4>
                            <p id="maghribTime" class="text-2xl font-bold text-pink-900">--:--</p>
                        </div>

                        <!-- Isha -->
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 text-center border-2 border-purple-200 hover:shadow-lg transition">
                            <div class="text-3xl mb-2">🌙</div>
                            <h4 class="font-bold text-purple-700 mb-1">Isha</h4>
                            <p id="ishaTime" class="text-2xl font-bold text-purple-900">--:--</p>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="mt-6 pt-4 border-t-2 border-purple-200">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-center text-sm">
                            <div>
                                <p class="text-gray-600">Sunrise</p>
                                <p id="sunriseTime" class="font-bold text-purple-600">--:--</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Midnight</p>
                                <p id="midnightTime" class="font-bold text-purple-600">--:--</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Imsak</p>
                                <p id="imsakTime" class="font-bold text-purple-600">--:--</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div id="prayerLoading" class="hidden">
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto mb-4"></div>
                    <p class="text-purple-600 font-semibold">Chargement des horaires de prière...</p>
                </div>
            </div>

            <!-- Error State -->
            <div id="prayerError" class="hidden">
                <div class="bg-red-100 border-2 border-red-300 rounded-xl p-4">
                    <p class="text-red-600 font-semibold text-center">❌ Erreur lors du chargement des horaires de prière</p>
                </div>
            </div>

            <!-- Default State -->
            <div id="prayerDefault" class="bg-purple-50 border-2 border-dashed border-purple-300 rounded-xl p-6 text-center">
                <p class="text-purple-600 font-semibold">🕌 Sélectionnez une ville et cliquez sur "Charger" pour voir les horaires de prière</p>
            </div>
        </div>
    </div>

    <script>
        function loadPrayerTimes() {
            const val = document.getElementById('prayerCity').value;
            const [city, country] = val.split(',');

            // Hide all states
            document.getElementById('prayerTimesResult').classList.add('hidden');
            document.getElementById('prayerError').classList.add('hidden');
            document.getElementById('prayerDefault').classList.add('hidden');

            // Show loading
            document.getElementById('prayerLoading').classList.remove('hidden');

            fetch(`/prayer-times?city=${city}&country=${country}`)
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(data => {
                    // Hide loading
                    document.getElementById('prayerLoading').classList.add('hidden');

                    if (data && data.data && data.data.timings) {
                        const t = data.data.timings;
                        const date = data.data.date;

                        // Update city name and date
                        document.getElementById('prayerCityName').textContent = `📍 ${city}, ${country}`;
                        document.getElementById('prayerDate').textContent = `${date.readable} - ${date.hijri.day} ${date.hijri.month.en} ${date.hijri.year} H`;

                        // Update prayer times
                        document.getElementById('fajrTime').textContent = t.Fajr;
                        document.getElementById('dhuhrTime').textContent = t.Dhuhr;
                        document.getElementById('asrTime').textContent = t.Asr;
                        document.getElementById('maghribTime').textContent = t.Maghrib;
                        document.getElementById('ishaTime').textContent = t.Isha;

                        // Update additional times
                        document.getElementById('sunriseTime').textContent = t.Sunrise || '--:--';
                        document.getElementById('midnightTime').textContent = t.Midnight || '--:--';
                        document.getElementById('imsakTime').textContent = t.Imsak || '--:--';

                        // Show result
                        document.getElementById('prayerTimesResult').classList.remove('hidden');

                        // Highlight current prayer time
                        highlightCurrentPrayer(t);
                    } else {
                        throw new Error('Invalid data format');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('prayerLoading').classList.add('hidden');
                    document.getElementById('prayerError').classList.remove('hidden');
                });
        }

        function highlightCurrentPrayer(timings) {
            const now = new Date();
            const currentTime = now.getHours() * 60 + now.getMinutes();

            const prayers = [
                { name: 'fajr', time: timings.Fajr },
                { name: 'dhuhr', time: timings.Dhuhr },
                { name: 'asr', time: timings.Asr },
                { name: 'maghrib', time: timings.Maghrib },
                { name: 'isha', time: timings.Isha }
            ];

            prayers.forEach(prayer => {
                const [hours, minutes] = prayer.time.split(':').map(Number);
                const prayerTime = hours * 60 + minutes;
                const element = document.getElementById(`${prayer.name}Time`).parentElement;

                // Remove any existing highlight
                element.classList.remove('ring-4', 'ring-green-400', 'scale-105');

                // Add highlight if this is the current or next prayer
                if (currentTime >= prayerTime - 30 && currentTime <= prayerTime + 30) {
                    element.classList.add('ring-4', 'ring-green-400', 'scale-105');
                }
            });
        }

        // Auto-load prayer times for default city on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Optionally auto-load for Tunis
            // loadPrayerTimes();
        });
    </script>
    <!-- End Prayer Widget -->

    <!-- Mood Analyzer Widget -->
    <div class="bg-gradient-to-r from-pink-100 to-rose-100 rounded-2xl shadow-lg p-6 mb-6">
        <div class="flex flex-col gap-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-pink-600 flex items-center gap-2">
                    💭 Analyseur d'Humeur
                </h2>
                <div class="text-sm text-gray-600">
                    Powered by AI 🤖
                </div>
            </div>

            <!-- Input Area -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <label for="moodNote" class="block text-sm font-semibold text-gray-700 mb-2">
                    ✍️ Comment vous sentez-vous aujourd'hui ?
                </label>
                <textarea
                    id="moodNote"
                    rows="4"
                    placeholder="Écrivez vos pensées, vos sentiments, votre journée... L'IA analysera votre humeur ! 😊"
                    class="w-full px-4 py-3 border-2 border-pink-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition resize-none"
                ></textarea>

                <div class="flex items-center justify-between mt-4">
                    <p class="text-xs text-gray-500">
                        💡 Astuce : Plus vous écrivez, plus l'analyse sera précise
                    </p>
                    <button
                        onclick="analyzeMood()"
                        class="bg-pink-500 text-white px-6 py-3 rounded-lg hover:bg-pink-600 transition font-semibold shadow-md hover:shadow-lg transform hover:scale-105 flex items-center gap-2"
                    >
                        <span>🔍</span>
                        <span>Analyser</span>
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div id="moodLoading" class="hidden">
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-pink-600 mx-auto mb-4"></div>
                    <p class="text-pink-600 font-semibold">Analyse de votre humeur en cours...</p>
                    <p class="text-gray-500 text-sm mt-2">L'IA traite vos émotions 🧠✨</p>
                </div>
            </div>

            <!-- Error State -->
            <div id="moodError" class="hidden">
                <div class="bg-red-100 border-2 border-red-300 rounded-xl p-4">
                    <p class="text-red-600 font-semibold text-center">❌ Erreur lors de l'analyse</p>
                    <p class="text-red-500 text-sm text-center mt-1">Vérifiez votre clé API HuggingFace</p>
                </div>
            </div>

            <!-- Result Display -->
            <div id="moodResult" class="hidden">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <!-- Emotion Header -->
                    <div class="text-center mb-6">
                        <div id="emotionEmoji" class="text-6xl mb-3">😊</div>
                        <h3 id="emotionLabel" class="text-3xl font-bold text-pink-600 mb-2">Happy</h3>
                        <div class="flex items-center justify-center gap-2">
                            <span class="text-gray-600">Confiance:</span>
                            <span id="emotionConfidence" class="text-2xl font-bold text-pink-600">95%</span>
                        </div>
                    </div>

                    <!-- Confidence Bar -->
                    <div class="mb-6">
                        <div class="bg-gray-200 rounded-full h-4 overflow-hidden">
                            <div id="confidenceBar" class="h-full bg-gradient-to-r from-pink-400 to-pink-600 transition-all duration-1000" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Emotion Description -->
                    <div class="bg-pink-50 rounded-lg p-4 border-2 border-pink-200">
                        <p id="emotionDescription" class="text-gray-700 text-center"></p>
                    </div>

                    <!-- All Emotions Breakdown -->
                    <div class="mt-6">
                        <h4 class="font-semibold text-gray-700 mb-3 text-center">📊 Détails de l'analyse</h4>
                        <div id="allEmotions" class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <!-- Will be populated by JS -->
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="mt-6 text-center">
                        <button
                            onclick="resetMoodAnalyzer()"
                            class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition font-semibold"
                        >
                            🔄 Nouvelle analyse
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Emotion configurations with emojis and descriptions
        const emotionConfig = {
            'joy': {
                emoji: '😊',
                color: 'from-yellow-400 to-yellow-600',
                bgColor: 'bg-yellow-50',
                borderColor: 'border-yellow-200',
                textColor: 'text-yellow-600',
                description: 'Vous ressentez de la joie et du bonheur ! Continuez à profiter de ce moment positif. ✨'
            },
            'sadness': {
                emoji: '😢',
                color: 'from-blue-400 to-blue-600',
                bgColor: 'bg-blue-50',
                borderColor: 'border-blue-200',
                textColor: 'text-blue-600',
                description: 'Vous semblez triste. N\'oubliez pas que les moments difficiles passent. Prenez soin de vous. 💙'
            },
            'anger': {
                emoji: '😠',
                color: 'from-red-400 to-red-600',
                bgColor: 'bg-red-50',
                borderColor: 'border-red-200',
                textColor: 'text-red-600',
                description: 'Vous ressentez de la colère. Prenez une pause, respirez profondément. Tout va s\'arranger. 🌬️'
            },
            'fear': {
                emoji: '😨',
                color: 'from-purple-400 to-purple-600',
                bgColor: 'bg-purple-50',
                borderColor: 'border-purple-200',
                textColor: 'text-purple-600',
                description: 'Vous ressentez de la peur ou de l\'anxiété. Rappelez-vous que vous êtes plus fort que vous ne le pensez. 💪'
            },
            'surprise': {
                emoji: '😲',
                color: 'from-orange-400 to-orange-600',
                bgColor: 'bg-orange-50',
                borderColor: 'border-orange-200',
                textColor: 'text-orange-600',
                description: 'Vous êtes surpris ! La vie est pleine de moments inattendus. Profitez-en ! 🎉'
            },
            'disgust': {
                emoji: '🤢',
                color: 'from-green-400 to-green-600',
                bgColor: 'bg-green-50',
                borderColor: 'border-green-200',
                textColor: 'text-green-600',
                description: 'Vous ressentez du dégoût. C\'est normal d\'avoir des réactions fortes parfois. 🌿'
            },
            'neutral': {
                emoji: '😐',
                color: 'from-gray-400 to-gray-600',
                bgColor: 'bg-gray-50',
                borderColor: 'border-gray-200',
                textColor: 'text-gray-600',
                description: 'Vous êtes dans un état neutre et calme. Parfois, la tranquillité est ce dont nous avons besoin. 🧘'
            }
        };

        function analyzeMood() {
            const text = document.getElementById('moodNote').value.trim();

            if (!text) {
                alert('⚠️ Veuillez écrire quelque chose avant d\'analyser !');
                return;
            }

            // Hide all states
            document.getElementById('moodResult').classList.add('hidden');
            document.getElementById('moodError').classList.add('hidden');

            // Show loading
            document.getElementById('moodLoading').classList.remove('hidden');

            fetch('/emotion/analyze', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ text: text })
            })
            .then(res => res.json().then(data => ({ status: res.status, data })))
            .then(({ status, data }) => {
                // Hide loading
                document.getElementById('moodLoading').classList.add('hidden');

                // Check if model is loading
                if (status === 503 && data.error === 'Model is loading') {
                    const errorDiv = document.getElementById('moodError');
                    errorDiv.innerHTML = `
                        <div class="bg-yellow-100 border-2 border-yellow-300 rounded-xl p-4">
                            <p class="text-yellow-700 font-semibold text-center">⏳ Le modèle IA se charge...</p>
                            <p class="text-yellow-600 text-sm text-center mt-2">
                                Veuillez réessayer dans ${data.estimated_time || 20} secondes
                            </p>
                            <div class="text-center mt-3">
                                <button
                                    onclick="setTimeout(() => analyzeMood(), ${(data.estimated_time || 20) * 1000}); this.disabled=true; this.textContent='⏳ Réessai automatique...';"
                                    class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition font-semibold"
                                >
                                    🔄 Réessayer automatiquement
                                </button>
                            </div>
                        </div>
                    `;
                    errorDiv.classList.remove('hidden');
                    return;
                }

                // Check for other errors
                if (status !== 200 || data.error) {
                    const errorDiv = document.getElementById('moodError');
                    errorDiv.innerHTML = `
                        <div class="bg-red-100 border-2 border-red-300 rounded-xl p-4">
                            <p class="text-red-600 font-semibold text-center">❌ ${data.error || 'Erreur lors de l\'analyse'}</p>
                            <p class="text-red-500 text-sm text-center mt-1">
                                ${data.message || 'Vérifiez votre clé API HuggingFace'}
                            </p>
                            ${data.details ? `<p class="text-red-400 text-xs text-center mt-2">${data.details}</p>` : ''}
                        </div>
                    `;
                    errorDiv.classList.remove('hidden');
                    return;
                }

                if (data && data.emotion) {
                    displayMoodResult(data);
                } else {
                    throw new Error('Invalid data format');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('moodLoading').classList.add('hidden');
                const errorDiv = document.getElementById('moodError');
                errorDiv.innerHTML = `
                    <div class="bg-red-100 border-2 border-red-300 rounded-xl p-4">
                        <p class="text-red-600 font-semibold text-center">❌ Erreur de connexion</p>
                        <p class="text-red-500 text-sm text-center mt-1">
                            ${error.message}
                        </p>
                    </div>
                `;
                errorDiv.classList.remove('hidden');
            });
        }

        function displayMoodResult(data) {
            const emotion = data.emotion.toLowerCase();
            const confidence = Math.round(data.confidence * 100);
            const config = emotionConfig[emotion] || emotionConfig['neutral'];

            // Update emoji
            document.getElementById('emotionEmoji').textContent = config.emoji;

            // Update label
            document.getElementById('emotionLabel').textContent = capitalizeFirst(emotion);
            document.getElementById('emotionLabel').className = `text-3xl font-bold mb-2 ${config.textColor}`;

            // Update confidence
            document.getElementById('emotionConfidence').textContent = `${confidence}%`;
            document.getElementById('emotionConfidence').className = `text-2xl font-bold ${config.textColor}`;

            // Update confidence bar
            const bar = document.getElementById('confidenceBar');
            bar.className = `h-full bg-gradient-to-r ${config.color} transition-all duration-1000`;
            setTimeout(() => {
                bar.style.width = `${confidence}%`;
            }, 100);

            // Update description
            const descBox = document.getElementById('emotionDescription').parentElement;
            descBox.className = `rounded-lg p-4 border-2 ${config.bgColor} ${config.borderColor}`;
            document.getElementById('emotionDescription').textContent = config.description;

            // Show result
            document.getElementById('moodResult').classList.remove('hidden');

            // Scroll to result
            document.getElementById('moodResult').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function resetMoodAnalyzer() {
            document.getElementById('moodNote').value = '';
            document.getElementById('moodResult').classList.add('hidden');
            document.getElementById('moodNote').focus();
        }

        function capitalizeFirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
    </script>
    <!-- End Mood Analyzer Widget -->

    <!-- FullCalendar Container -->
    <div id="calendar" class="bg-white rounded-xl shadow p-4"></div>

    <!-- Sections Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
        <a href="{{ route('finance.index') }}" class="block bg-pink-200 p-6 rounded-xl shadow-lg hover:scale-105 hover:shadow-2xl transition transform duration-300 text-center font-semibold">💰 Finance Tracker</a>
        <a href="/sickness-care" class="block bg-yellow-200 p-6 rounded-xl shadow-lg hover:scale-105 hover:shadow-2xl transition transform duration-300 text-center font-semibold">Sickness Care</a>
        <a href="/cleaning-routine" class="block bg-green-200 p-6 rounded-xl shadow-lg hover:scale-105 hover:shadow-2xl transition transform duration-300 text-center font-semibold">Cleaning Routine</a>
        <a href="/muslimah" class="block bg-blue-200 p-6 rounded-xl shadow-lg hover:scale-105 hover:shadow-2xl transition transform duration-300 text-center font-semibold">Muslimah</a>
        <a href="/hobbies" class="block bg-purple-200 p-6 rounded-xl shadow-lg hover:scale-105 hover:shadow-2xl transition transform duration-300 text-center font-semibold">Hobbies</a>
        <a href="/internship" class="block bg-pink-300 p-6 rounded-xl shadow-lg hover:scale-105 hover:shadow-2xl transition transform duration-300 text-center font-semibold">Internship</a>
        <a href="/learning-tracker" class="block bg-yellow-300 p-6 rounded-xl shadow-lg hover:scale-105 hover:shadow-2xl transition transform duration-300 text-center font-semibold">Learning Tracker</a>
        <a href="/routine-trackers" class="block bg-green-300 p-6 rounded-xl shadow-lg hover:scale-105 hover:shadow-2xl transition transform duration-300 text-center font-semibold">Routine Trackers</a>
    </div>
</div>

<!-- Modal pour créer/éditer un événement -->
<div id="eventModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4">
        <h2 id="modalTitle" class="text-2xl font-bold text-pink-600 mb-6">Nouvel Événement</h2>

        <form id="eventForm">
            <input type="hidden" id="eventId">

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Titre *</label>
                <input type="text" id="eventTitle" required
                    class="w-full px-4 py-2 border border-pink-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Description</label>
                <textarea id="eventDescription" rows="3"
                    class="w-full px-4 py-2 border border-pink-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Date de début *</label>
                <input type="datetime-local" id="eventStart" required
                    class="w-full px-4 py-2 border border-pink-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Date de fin *</label>
                <input type="datetime-local" id="eventEnd" required
                    class="w-full px-4 py-2 border border-pink-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 bg-pink-500 text-white py-2 px-4 rounded-lg hover:bg-pink-600 transition font-semibold">
                    Enregistrer
                </button>
                <button type="button" onclick="closeModal()"
                    class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition font-semibold">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script>
let calendar;

// Ouvrir le modal
function openModal(eventData = null) {
    const modal = document.getElementById('eventModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('eventForm');

    if (eventData) {
        // Mode édition
        modalTitle.textContent = 'Modifier l\'événement';
        document.getElementById('eventId').value = eventData.id;
        document.getElementById('eventTitle').value = eventData.title;
        document.getElementById('eventDescription').value = eventData.extendedProps?.description || '';
        document.getElementById('eventStart').value = formatDateForInput(eventData.start);
        document.getElementById('eventEnd').value = formatDateForInput(eventData.end || eventData.start);
    } else {
        // Mode création
        modalTitle.textContent = 'Nouvel Événement';
        form.reset();
        document.getElementById('eventId').value = '';
    }

    modal.classList.remove('hidden');
}

// Fermer le modal
function closeModal() {
    document.getElementById('eventModal').classList.add('hidden');
    document.getElementById('eventForm').reset();
}

// Formater la date pour l'input datetime-local
function formatDateForInput(date) {
    if (!date) return '';
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

// Initialiser FullCalendar
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');

    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        editable: true,
        selectable: true,
        height: 700,
        locale: 'fr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
            today: 'Aujourd\'hui',
            month: 'Mois',
            week: 'Semaine',
            day: 'Jour',
            list: 'Liste'
        },
        // Afficher l'heure dans les événements
        displayEventTime: true,
        displayEventEnd: true,
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },
        // Format de l'heure dans la vue liste
        listDayFormat: {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        },
        // Charger les événements
        events: @json($events),
        // Personnaliser l'affichage des événements
        eventDidMount: function(info) {
            // Ajouter une tooltip avec les détails
            if (info.event.extendedProps.description) {
                info.el.title = info.event.extendedProps.description;
            }

            // Appliquer la couleur si elle existe
            if (info.event.backgroundColor) {
                info.el.style.backgroundColor = info.event.backgroundColor;
                info.el.style.borderColor = info.event.backgroundColor;
            }
        },

        // Clic sur une date pour créer un événement
        dateClick: function(info) {
            let title = prompt('📝 Titre de l\'événement:');
            if (title) {
                fetch('/calendar/events', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        title: title,
                        start: info.dateStr + 'T09:00:00',
                        end: info.dateStr + 'T10:00:00'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        calendar.addEvent({
                            id: data.id,
                            title: data.title,
                            start: data.start,
                            end: data.end
                        });
                        alert('✅ Événement créé avec succès!');
                    } else {
                        alert('❌ Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('❌ Erreur lors de la création de l\'événement');
                });
            }
        },

        // Clic sur un événement pour l'éditer ou le supprimer
        eventClick: function(info) {
            if (confirm('Que voulez-vous faire?\n\nOK = Modifier\nAnnuler = Supprimer')) {
                openModal(info.event);
            } else {
                if (confirm('Êtes-vous sûr de vouloir supprimer cet événement?')) {
                    deleteEvent(info.event.id);
                }
            }
        },

        // Déplacer/redimensionner un événement
        eventChange: function(info) {
            updateEvent(info.event);
        }
    });

    calendar.render();

    // Gérer la soumission du formulaire
    document.getElementById('eventForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const eventId = document.getElementById('eventId').value;
        const eventData = {
            title: document.getElementById('eventTitle').value,
            description: document.getElementById('eventDescription').value,
            start: document.getElementById('eventStart').value,
            end: document.getElementById('eventEnd').value
        };

        if (eventId) {
            updateEvent(eventId, eventData);
        } else {
            createEvent(eventData);
        }
    });
});

// Créer un événement
function createEvent(eventData) {
    fetch('/calendar/events', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(eventData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            calendar.addEvent({
                id: data.id,
                title: data.title,
                start: data.start,
                end: data.end
            });
            closeModal();
            alert('Événement créé avec succès!');
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de la création de l\'événement');
    });
}

// Mettre à jour un événement
function updateEvent(eventIdOrObject, eventData = null) {
    let eventId, data;

    if (typeof eventIdOrObject === 'object') {
        // Appelé depuis eventChange
        eventId = eventIdOrObject.id;
        data = {
            title: eventIdOrObject.title,
            start: eventIdOrObject.start.toISOString(),
            end: eventIdOrObject.end ? eventIdOrObject.end.toISOString() : eventIdOrObject.start.toISOString()
        };
    } else {
        // Appelé depuis le formulaire
        eventId = eventIdOrObject;
        data = eventData;
    }

    fetch('/calendar/events/' + eventId, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (eventData) {
                // Mise à jour depuis le formulaire
                const event = calendar.getEventById(eventId);
                if (event) {
                    event.setProp('title', data.title);
                    event.setStart(data.start);
                    event.setEnd(data.end);
                }
                closeModal();
                alert('Événement mis à jour avec succès!');
            }
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de la mise à jour de l\'événement');
    });
}

// Supprimer un événement
function deleteEvent(eventId) {
    fetch('/calendar/events/' + eventId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const event = calendar.getEventById(eventId);
            if (event) {
                event.remove();
            }
            alert('Événement supprimé avec succès!');
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de la suppression de l\'événement');
    });
}
</script>

<style>
@keyframes fadeIn {
    0% { opacity: 0; transform: translateY(-10px); }
    100% { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 1s ease-in-out;
}
</style>
</body>
</html>
