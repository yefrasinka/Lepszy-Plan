document.addEventListener("DOMContentLoaded", () => {
    const favButton = document.getElementById("fav");

    // Przełącznik ulubionych (serduszko)
    favButton.addEventListener("click", () => {
        if (favButton.textContent === "♡") {
            favButton.textContent = "❤️";
        } else {
            favButton.textContent = "♡";
        }
    });

// obsługa przycisku dodania do ulubionych (♥)
    function toggleFavorite() {
        const currentPlan = window.location.href;
        let favorites = JSON.parse(localStorage.getItem("favorites") || "[]");
        if (favorites.includes(currentPlan)) {
            favorites = favorites.filter(plan => plan !== currentPlan);
        } else {
            favorites.push(currentPlan);
        }
        localStorage.setItem("favorites", JSON.stringify(favorites));
        alert(favorites.includes(currentPlan) ? "Dodano do ulubionych!" : "Usunięto z ulubionych!");
    }

    document.getElementById("fav").addEventListener("click", toggleFavorite);

});

document.addEventListener("DOMContentLoaded", () => {
    const fields = [
        document.getElementById("wykladowca"),
        document.getElementById("sala"),
        document.getElementById("przedmiot"),
        document.getElementById("grupa"),
        document.getElementById("album")
    ];

    // Alert przy braku filtrów do wyszukiwania
    function checkIfEmpty() {
        const areFieldsEmpty = fields.every(field => field.value.trim() === "");

        if (areFieldsEmpty) {
            alert("Wszystkie pola są puste!");
        }
    }

    const searchButton = document.getElementById("szukaj");

    // zapis filtrów w URL
    function saveFiltersToURL() {
        const filters = {
            wykladowca: document.getElementById("wykladowca").value,
            sala: document.getElementById("sala").value,
            przedmiot: document.getElementById("przedmiot").value,
            grupa: document.getElementById("grupa").value,
            album: document.getElementById("album").value,
            wyklad: document.getElementById("wyklad").checked,
            lektorat: document.getElementById("lektorat").checked,
            audytoria: document.getElementById("audytoria").checked,
            laboratoria: document.getElementById("laboratoria").checked,
        };
        const query = new URLSearchParams(filters).toString();
        window.history.replaceState(null, null, "?" + query);
    }
    searchButton.addEventListener("click", checkIfEmpty);

});

document.addEventListener("DOMContentLoaded", () => {
    const themeToggle = document.getElementById("theme-toggle");
    const body = document.body;
    const calendarGrid = document.getElementById("calendar-grid");
    const viewSelector = document.getElementById("view");
    const timeLabels = Array.from({ length: 14 }, (_, i) => `${7 + i}:00`);

    // Zmiana tematu
    themeToggle.addEventListener("click", () => {
        body.classList.toggle("dark-theme");
        body.classList.toggle("light-theme");
        themeToggle.textContent = body.classList.contains("dark-theme") ? "🌙" : "☀️";
    });

    // Renderowanie widoku
    viewSelector.addEventListener("change", renderCalendar);

    function renderCalendar() {
        const view = viewSelector.value;
        calendarGrid.innerHTML = "";

        if (view === "dzien") {
            renderDayView();
        } else if (view === "tydzien") {
            renderWeekView();
        } else if (view === "miesiac"){
            renderMonthView();
        } else if (view === "semestr"){
            renderSemesterView();
        }

    }

    function renderDayView() {
        calendarGrid.style.gridTemplateColumns = "1fr";
        calendarGrid.style.gridTemplateRows = `repeat(${timeLabels.length}, 1fr)`;

        timeLabels.forEach((time) => {
            const timeCell = document.createElement("div");
            timeCell.textContent = time;
            calendarGrid.appendChild(timeCell);
        });
    }

    function renderWeekView() {
        calendarGrid.style.gridTemplateColumns = "repeat(8, 1fr)";
        calendarGrid.style.gridTemplateRows = `repeat(${timeLabels.length}, 1fr)`;

        const daysOfWeek = [" ", "Pn", "Wt", "Śr", "Cz", "Pt", "Sb", "Nd"];

        daysOfWeek.forEach((day) => {
            const dayCell = document.createElement("div");
            dayCell.textContent = day;
            calendarGrid.appendChild(dayCell);
        });

        for (let i = 0; i < timeLabels.length * 8; i++) {
            const cell = document.createElement("div");
            cell.textContent = i % 8 === 0 ? timeLabels[Math.floor(i / 7)] : "";
            calendarGrid.appendChild(cell);
        }
    }

    function renderMonthView() {
        calendarGrid.style.gridTemplateColumns = "repeat(7, 1fr)";
        calendarGrid.style.gridTemplateRows = "repeat(6, 1fr)";

        const daysOfWeek = ["Pn", "Wt", "Śr", "Cz", "Pt", "Sb", "Nd"];
        daysOfWeek.forEach((day) => {
            const dayCell = document.createElement("div");
            dayCell.textContent = day;
            calendarGrid.appendChild(dayCell);
        });

        for (let i = 1; i <= 42; i++) {
            const dayCell = document.createElement("div");
            dayCell.textContent = i <= 31 ? i : "";
            calendarGrid.appendChild(dayCell);
        }
    }

   
    viewSelector.addEventListener("change", renderCalendar);
    renderCalendar();
});


document.addEventListener("DOMContentLoaded", () => {
    const filters = document.querySelectorAll("#wykladowca, #sala, #przedmiot, #grupa, #album");
    const clearButton = document.getElementById("wyczysc");

    // Funkcja do czyszczenia filtrów
    clearButton.addEventListener("click", () => {
        filters.forEach(filter => filter.value = "");
    });

// ładowanie filtrów z URL
function loadFiltersFromURL() {
    const params = new URLSearchParams(window.location.search);
    document.getElementById("wykladowca").value = params.get("wykladowca") || "";
    document.getElementById("sala").value = params.get("sala") || "";
    document.getElementById("przedmiot").value = params.get("przedmiot") || "";
    document.getElementById("grupa").value = params.get("grupa") || "";
    document.getElementById("album").value = params.get("album") || "";

    document.getElementById("wyklad").checked = params.get("wyklad") === "true";
    document.getElementById("lektorat").checked = params.get("lektorat") === "true";
    document.getElementById("audytoria").checked = params.get("audytoria") === "true";
    document.getElementById("laboratoria").checked = params.get("laboratoria") === "true";
}

document.addEventListener("DOMContentLoaded", loadFiltersFromURL);
});

