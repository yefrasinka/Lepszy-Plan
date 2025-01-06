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
        } else {
            renderMonthView();
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

    renderCalendar();
});

document.addEventListener("DOMContentLoaded", () => {
    const filters = document.querySelectorAll("#wykladowca, #sala, #przedmiot, #grupa, #album");
    const clearButton = document.getElementById("wyczysc");

    // Funkcja do czyszczenia filtrów
    clearButton.addEventListener("click", () => {
        filters.forEach(filter => filter.value = "");
    });
});