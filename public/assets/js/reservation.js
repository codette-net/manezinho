let responseWrapper = document.querySelector(".response-wrapper");
let responseHeader = responseWrapper.querySelector("h2");
let responseMsg = document.querySelector(".response-msg");
let exitBtn = document.querySelector("#response-exit-btn");
let form = document.querySelector(".reservation-form");
let errorsMsg = document.querySelector(".errors-msg");
let whatNext = document.querySelector(".what-next");
let holidayWarning = document.querySelector("#holiday-warning");
let navInfoHours = document.querySelector("#nav-info-hours");

let today = new Date();
let todayReservations = new Date();
let todayDayNumber = today.getDay();
let todayDay = today.toLocaleString("en-US", {
    timeZone: "Atlantic/Azores",
    weekday: "long",
});
let todayInWords = new Intl.DateTimeFormat("en-US", {
    timeZone: "Atlantic/Azores",
    weekday: "long",
}).format(today);
let todayDate = today.toLocaleString("en-US", {
    timeZone: "Atlantic/Azores",
    day: "2-digit",
});
let todayMonth = today.toLocaleString("en-US", {
    timeZone: "Atlantic/Azores",
    month: "2-digit",
});
let monthInWords = new Intl.DateTimeFormat("en-US", {
    timeZone: "Atlantic/Azores",
    month: "long",
}).format(today);
let todayYear = today.toLocaleString("en-US", {
    timeZone: "Atlantic/Azores",
    year: "numeric",
});
let OPEN_HOURS = null;

let openClosedInfo = document.querySelector(".open-closed-info");

let dateReservation = document.querySelector("#date-reservation");
let holidaysBtn = document.querySelector("#holidays");
let holidaysMonth = document.querySelector("#holidays-month");
// set text to the current month in holidays text
holidaysMonth.textContent = monthInWords;

// set max date for 1 month from today
let maxDate = new Date(today);
maxDate.setMonth(maxDate.getMonth() + 1);
dateReservation.max = maxDate.toISOString().substring(0, 10);

// check if today is not monday , if so, add 2 days to the date reservation
if (todayDay === "Monday") {
    todayReservations.setDate(todayReservations.getDate() + 2);
    dateReservation.value = todayReservations.toISOString().substring(0, 10);
    dateReservation.min = todayReservations.toISOString().substring(0, 10);
} else if (todayDay === "Tuesday") {
    todayReservations.setDate(todayReservations.getDate() + 1);
    dateReservation.value = todayReservations.toISOString().substring(0, 10);
    dateReservation.min = todayReservations.toISOString().substring(0, 10);
} else {
    dateReservation.value = todayReservations.toISOString().substring(0, 10);
    dateReservation.min = todayReservations.toISOString().substring(0, 10);
}

// check if it is not monday or tuesday and if its after 15:00 (azorean time), add 1 day to the date reservation
// if (!todayDay === 'Monday' || !todayDay === 'Tuesday') {
if (!todayDay === "Monday") {
    if (
        today.toLocaleString("en-US", {
            timeZone: "Atlantic/Azores",
            hour: "2-digit",
            hour12: false,
        }) >= 15
    ) {
        todayReservations.setDate(todayReservations.getDate() + 1);
        dateReservation.value = todayReservations
            .toISOString()
            .substring(0, 10);
        dateReservation.min = todayReservations.toISOString().substring(0, 10);
    }
}

console.log(todayDay);
console.log(todayDayNumber);
console.log(todayInWords);
console.log(todayDate);
console.log(todayMonth);
console.log(todayYear);
console.log(todayInWords);
console.log(monthInWords);

async function getHoursData() {
    const url = "assets/js/openhours.json";
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }

        let result = await response.json();
        console.log(result);
        return result;
    } catch (error) {
        console.error(error.message);
        return null;
    }
}

async function loadHours() {
    OPEN_HOURS = await getHoursData();
    OPEN_HOURS.exceptions.sort((a, b) => new Date(a.date) - new Date(b.date));
    let upComingHolidays = checkWeek();
    if (upComingHolidays) {
        let msg = '<ul class="alt">';
        let formatDay;
        upComingHolidays.forEach((holiday) => {
            let [y, m, d] = holiday.date.split("-");
            // format date to noon so there are no timezone quirks
            let dateObj = new Date(Date.UTC(y, m - 1, d, 12, 0, 0));

            let formatted = new Intl.DateTimeFormat("en-US", {
                timeZone: "Atlantic/Azores",
                weekday: "long",
                month: "long",
                day: "numeric",
                year: "numeric",
            }).format(dateObj);

            msg += `<li class="li-flex">${formatted} <span style="color: orangered;">Closed</span></li>`;
        });

        msg += "</ul>";

        holidayWarning.classList.toggle("open");
        holidayWarning
            .querySelector("#holiday-warning-check")
            .addEventListener("click", (e) => {
                e.preventDefault();
                responseHeader.textContent =
                    "Holidays/exceptions for the coming week";
                responseMsg.innerHTML = msg;
                responseWrapper.style.display = "flex";
                holidayWarning.classList.toggle("open");
            });
        if (holidayWarning.classList.contains("open")) {
            holidayWarning
                .querySelector(".close")
                .addEventListener("click", (e) => {
                    e.preventDefault();
                    holidayWarning.classList.toggle("open");
                });
        }
    }
}

loadHours();

function todayOpen() {
    let todayDateFormatted = `${todayYear}-${String(todayMonth).padStart(
        2,
        "0"
    )}-${String(todayDate).padStart(2, "0")}`;
    console.log(todayDateFormatted);
    const exception = OPEN_HOURS.exceptions.find(
        (exception) => exception.date === todayDateFormatted
    );
    let openClosedMsg = "";

    // check data.regular[todayDay] if the status is closed
    console.log(OPEN_HOURS.regular[todayDayNumber].status);

    if (exception || OPEN_HOURS.regular[todayDayNumber].status === "closed") {
        openClosedMsg += `<p class="today-closed">Sorry, today we are closed.</p>`;
    } else {
        openClosedMsg += `<p class="today-open">Today we are open!</p>`;
    }
    openClosedMsg += "</p>";
    return openClosedMsg;
}

function displayHours() {
    if (!OPEN_HOURS || !OPEN_HOURS.regular) return;

    const regular = OPEN_HOURS.regular;

    // Reorder data: Monday → Sunday
    const orderedRegular = [
        regular[1], regular[2], regular[3],
        regular[4], regular[5], regular[6], regular[0]
    ];

    const orderedNames = [
        "Monday", "Tuesday", "Wednesday",
        "Thursday", "Friday", "Saturday", "Sunday"
    ];

    // Map today (JS Sunday=0 → becomes index=6)
    const jsToday = new Date().getDay();
    const todayIndex = (jsToday + 6) % 7;

    let msg = "";
    msg += todayOpen(); // keep your own function

    msg += `<ul class="alt">`;

    orderedRegular.forEach((day, index) => {
        const isToday = index === todayIndex;

        msg += `
        <li class="li-flex ${isToday ? "today" : ""}">
            <span>${orderedNames[index]}</span>
            ${
                day.status === "open"
                    ? `<span class="open">${day.open} - ${day.close}</span>`
                    : `<span class="closed">Closed</span>`
            }
        </li>`;
    });

    msg += `</ul>`;

    responseHeader.textContent = "Opening Hours";
    responseMsg.innerHTML = msg;
    responseWrapper.style.display = "flex";
}




function holidays(week = false, month = true) {
    if (month) {
        // check for exceptions in the coming 31 days
        let monthFromNow = new Date(today);
        monthFromNow.setDate(monthFromNow.getDate() + 31);
        let monthFromNowFormatted = `${monthFromNow.getFullYear()}-${String(
            monthFromNow.getMonth() + 1
        ).padStart(2, "0")}-${String(monthFromNow.getDate()).padStart(2, "0")}`;
        console.log(monthFromNowFormatted);

        let closingDates = OPEN_HOURS.exceptions.filter((exception) => {
            let exceptionDate = new Date(exception.date);
            return exceptionDate >= today && exceptionDate <= monthFromNow;
        });
        return closingDates;
    }
    if (week) {
        let weekFromNow = new Date(today);
        weekFromNow.setDate(weekFromNow.getDate() + 7);
        let weekFromNowFormatted = `${weekFromNow.getFullYear()}-${String(
            weekFromNow.getMonth() + 1
        ).padStart(2, "0")}-${String(weekFromNow.getDate()).padStart(2, "0")}`;
        console.log(weekFromNowFormatted);
        let closingDates = OPEN_HOURS.exceptions.filter((exception) => {
            let exceptionDate = new Date(exception.date);
            return exceptionDate >= today && exceptionDate <= weekFromNow;
        });
        return closingDates;
    }
}

form.addEventListener("submit", async (event) => {
    console.log("submitting form");
    event.preventDefault();
    let formData = new FormData(event.target);

    try {
        const response = await fetch("/reservation", {
            method: "POST",
            body: formData,
        });
        const result = await response.json();
        if(result) {
            console.log(result);
        }
        if (result && !result.errors) {
            console.log("success");
            console.log(result);
            responseWrapper.style.display = "flex";
            responseMsg.innerHTML = result.success;
            form.reset();
        } else if (result.errors) {
            console.log(result.errors);
            errorsopenClosedMsg = "";

            Object.values(result.errors).forEach((error) => {
                errorsopenClosedMsg += error + "\n";
            });
            errorsMsg.classList.add("active");
            console.log(result.errors);
        } else {
            errorsopenClosedMsg = "An unexpected error occurred.";
            errorsMsg.classList.add("active");
            console.log(result);
        }
    } catch (err) {
        console.error(err);
        errorsopenClosedMsg =
            "An error occurred while submitting the form. Please try again.";
        errorsMsg.classList.add("active");
    }
});

function checkWeek() {
    let weekData = holidays((week = true), (month = false));
    return weekData.length > 0 ? weekData : false;
}

// listen for holidays and open modal with holiday hours

holidaysBtn.addEventListener("click", (e) => {
    e.preventDefault();

    responseWrapper.style.display = "flex";
    responseHeader.textContent = `Holidays and exceptions for ${monthInWords}`;
    let msg = "";
    // check if today is open or closed
    msg += todayOpen();
    // check for holidays the comming 31 days
    let holidaysData = holidays();

    // check if there are holidays and if so add them to the list.
    if (holidaysData) {
        msg += '<ul class="alt">';
        let formatDay;
        console.log(holidaysData);

        holidaysData.forEach((holiday) => {
            const [y, m, d] = holiday.date.split("-");
            // format date to noon so there are no timezone quirks
            const dateObj = new Date(Date.UTC(y, m - 1, d, 12, 0, 0));

            const formatted = new Intl.DateTimeFormat("en-US", {
                timeZone: "Atlantic/Azores",
                weekday: "long",
                month: "long",
                day: "numeric",
                year: "numeric",
            }).format(dateObj);

            msg += `<li class="li-flex">${formatted} <span style="color: orangered;">Closed</span></li>`;
        });

        msg += "</ul>";
    }

    responseMsg.innerHTML = msg;
});

navInfoHours.addEventListener('click', (e) => {
  e.preventDefault();
  displayHours()
})


exitBtn.addEventListener("click", () => {
    responseWrapper.style.display = "none";
});

// close modal when clicking outside of it or with the escape key
window.addEventListener("click", (e) => {
    if (e.target === responseWrapper) {
        responseWrapper.style.display = "none";
    }
});

window.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
        responseWrapper.style.display = "none";
    }
});
