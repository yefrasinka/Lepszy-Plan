document.addEventListener("DOMContentLoaded", () => {
    const favButton = document.getElementById("fav");

    // Toggle favorite button (heart)
    favButton.addEventListener("click", () => {
        favButton.textContent = favButton.textContent === "♡" ? "❤️" : "♡";
    });

    // Initialize FullCalendar
    const calendarEl = document.getElementById("calendar");
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridWeek',
        headerToolbar: false,
        locale: 'pl', 
        firstDay: 1,
        events: [
            {
                title: 'Wykład - Matematyka',
                start: '2025-01-12T10:00:00',
                end: '2025-01-12T12:00:00',
                color: 'blue'
            },
            {
                title: 'Laboratorium - Fizyka',
                start: '2025-01-09T14:00:00',
                end: '2025-01-09T16:00:00',
                color: 'green'
            }
        ],
        eventClick: function(info) {
            // Display event details in a modal
            const modal = document.createElement('div');
            modal.className = 'event-modal active';
            modal.innerHTML = `
                <h3>${info.event.title}</h3>
                <p><strong>Start:</strong> ${info.event.start.toLocaleString()}</p>
                <p><strong>Koniec:</strong> ${info.event.end.toLocaleString()}</p>
                <button class="close-button">Zamknij</button>
            `;

            document.body.appendChild(modal);

            // Close button logic
            modal.querySelector('.close-button').addEventListener('click', () => {
                modal.remove();
            });
        }
    });

    calendar.render();

    // View change logic
    const viewSelector = document.getElementById("view");
    viewSelector.addEventListener("change", () => {
        calendar.changeView(viewSelector.value);
    });

    // Calendar navigation buttons
    document.getElementById("today").addEventListener("click", () => calendar.today());
    document.getElementById("prev").addEventListener("click", () => calendar.prev());
    document.getElementById("next").addEventListener("click", () => calendar.next());

    // Theme toggle
    const themeToggle = document.getElementById("theme-toggle");
    themeToggle.addEventListener("click", () => {
        document.body.classList.toggle("dark-theme");
        document.body.classList.toggle("light-theme");
        themeToggle.textContent = document.body.classList.contains("dark-theme") ? "🌙" : "☀️";
    });
});

/* document.addEventListener("DOMContentLoaded", () => {
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

});*/


/* document.addEventListener("DOMContentLoaded", () => {
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
}); */