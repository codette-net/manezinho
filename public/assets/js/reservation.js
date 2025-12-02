let responseWrapper = document.querySelector(".response-wrapper");
let responseMsg = document.querySelector(".response-msg");
let exitBtn = document.querySelector("#response-exit-btn");
let form = document.querySelector(".reservation-form");
let errorsMsg = document.querySelector(".errors-msg");
let whatNext = document.querySelector(".what-next");

let today = new Date();
let todayReservations = new Date();
let todayDayNumber = today.getDay();
let todayDay = today.toLocaleString('en-US', { timeZone: 'Atlantic/Azores', weekday: 'long' });
let todayInWords = new Intl.DateTimeFormat('en-US', { timeZone: 'Atlantic/Azores', weekday: 'long' }).format(today);
let todayDate = today.toLocaleString('en-US', { timeZone: 'Atlantic/Azores', day: '2-digit' });
let todayMonth = today.toLocaleString('en-US', { timeZone: 'Atlantic/Azores', month: '2-digit' });
let monthInWords = new Intl.DateTimeFormat('en-US', { timeZone: 'Atlantic/Azores', month: 'long' }).format(today);
let todayYear = today.toLocaleString('en-US', { timeZone: 'Atlantic/Azores', year: 'numeric' });
let openClosedInfo = document.querySelector(".open-closed-info");

let dateReservation = document.querySelector("#date-reservation");
let holidaysBtn = document.querySelector('#holidays');
let holidaysMonth = document.querySelector('#holidays-month');
// set text to the current month in holidays text
holidaysMonth.textContent = monthInWords;

// listen for holidays and open modal with holiday hours

holidaysBtn.addEventListener('click' , (e) => {
  e.preventDefault();

    responseWrapper.style.display = "flex";
    responseWrapper.querySelector('h2').textContent = `Holidays and exceptions for ${monthInWords}`
      responseMsg.innerHTML = '<p>hoi</p>';
})

exitBtn.addEventListener('click', () => {
  responseWrapper.style.display = 'none';
});

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

    if (result && !result.errors) {
      console.log("success");
      console.log(result);
      responseWrapper.style.display = "flex";
      responseMsg.innerHTML = result.success;
      form.reset();
    } else if (result.errors) {
      console.log(result.errors);
      errorsMsg.textContent = "";

      Object.values(result.errors).forEach((error) => {
        errorsMsg.textContent += error + "\n";
      });
      errorsMsg.classList.add("active");
      console.log(result.errors);
    } else {
      errorsMsg.textContent = "An unexpected error occurred.";
      errorsMsg.classList.add("active");
      console.log(result);
    }
  } catch (err) {
    console.error(err);
    errorsMsg.textContent =
      "An error occurred while submitting the form. Please try again.";
    errorsMsg.classList.add("active");
  }
});

// set max date for 1 month from today
let maxDate = new Date(today);
maxDate.setMonth(maxDate.getMonth() + 1);
dateReservation.max = maxDate.toISOString().substring(0, 10);

// check if today is not monday , if so, add 2 days to the date reservation
if (todayDay === 'Monday') {
  todayReservations.setDate(todayReservations.getDate() + 2);
  dateReservation.value = todayReservations.toISOString().substring(0, 10);
  dateReservation.min = todayReservations.toISOString().substring(0, 10);
}
 else if (todayDay === 'Tuesday') {
  todayReservations.setDate(todayReservations.getDate() + 1);
  dateReservation.value = todayReservations.toISOString().substring(0, 10);
  dateReservation.min = todayReservations.toISOString().substring(0, 10);
}
 else {
  dateReservation.value = todayReservations.toISOString().substring(0, 10);
  dateReservation.min = todayReservations.toISOString().substring(0, 10);
}

// check if it is not monday or tuesday and if its after 15:00 (azorean time), add 1 day to the date reservation
// if (!todayDay === 'Monday' || !todayDay === 'Tuesday') {
if (!todayDay === 'Monday' ) {
  if (today.toLocaleString('en-US', { timeZone: 'Atlantic/Azores', hour: '2-digit', hour12: false }) >= 15) {
    todayReservations.setDate(todayReservations.getDate() + 1);
    dateReservation.value = todayReservations.toISOString().substring(0, 10);
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

// check in openhours.json if today is open or closed

fetch("assets/js/openhours.json")
  .then((response) => response.json())
  .then((data) => {
    console.log(data);
    // check in the exceptions array if the date of today is in there and if the status is closed (date format in array is yyyy-mm-dd)
    // date of today in the yyyy-mm-dd format
    const todayDateFormatted = `${todayYear}-${String(todayMonth + 1).padStart(2, '0')}-${String(todayDate).padStart(2, '0')}`;
    console.log(todayDateFormatted);
    const exception = data.exceptions.find((exception) => exception.date === todayDateFormatted);
    let msg = document.createElement("span");
    
    // check data.regular[todayDay] if the status is closed
    console.log(data.regular[todayDayNumber].status);

    if (exception || data.regular[todayDayNumber].status === "closed") {
      msg.textContent = `Sorry, today we are closed.`
      msg.classList.add("today-closed");
      openClosedInfo.appendChild(msg);
    } else {
      msg.textContent = `Today we are open!`
      msg.classList.add("today-open");
      openClosedInfo.appendChild(msg);
    }


    // check for exceptions in the coming 2 weeks
    const twoWeeksFromNow = new Date(today);
    twoWeeksFromNow.setDate(twoWeeksFromNow.getDate() + 14);
    const twoWeeksFromNowFormatted = `${twoWeeksFromNow.getFullYear()}-${String(twoWeeksFromNow.getMonth() + 1).padStart(2, '0')}-${String(twoWeeksFromNow.getDate()).padStart(2, '0')}`;
    console.log(twoWeeksFromNowFormatted);

    const closingDates = data.exceptions.filter((exception) => {
      const exceptionDate = new Date(exception.date);
      return exceptionDate >= today && exceptionDate <= twoWeeksFromNow;
    });
    console.log(closingDates);
    // write an UL list with the closing dates and the status 'Monday 1st of January 2021: closed'
    if (closingDates.length > 0) {
      let closingDate = document.createElement("p");


      if(closingDates.length > 1) {
        const closingDatesList = document.createElement("ul");
        closingDates.forEach((exception) => {
          const listItem = document.createElement("li");
          listItem.textContent = `${new Intl.DateTimeFormat('en-US', { weekday: 'long' }).format(new Date(exception.date))} ${new Date(exception.date).getDate()} ${new Intl.DateTimeFormat('en-US', { month: 'long' }).format(new Date(exception.date))} ${new Date(exception.date).getFullYear()}: ${exception.status}`;
          closingDatesList.appendChild(listItem);
        });
        document.querySelector(".closing-dates").appendChild(closingDatesList);
        let closedMsg = "<span class='holidays'>Upcoming holidays, please check our open hours.</span>";
        openClosedInfo.insertAdjacentHTML("beforeend", closedMsg);
      } 
      else {
        let exception = closingDates[0];
        closingDate.textContent = `${new Intl.DateTimeFormat('en-US', { weekday: 'long' }).format(new Date(exception.date))} ${new Date(exception.date).getDate()} ${new Intl.DateTimeFormat('en-US', { month: 'long' }).format(new Date(exception.date))} ${new Date(exception.date).getFullYear()}: ${exception.status}`;
        openClosedInfo.appendChild(closingDate);
      }
      }

  });