let responseWrapper = document.querySelector(".response-wrapper");
let responseMsg = document.querySelector(".response-msg");
let exitBtn = document.querySelector("#response-exit-btn");
let form = document.querySelector(".reservation-form");
let errorsMsg = document.querySelector(".errors-msg");
let whatNext = document.querySelector(".what-next");


exitBtn.addEventListener('click', () => {
  responseWrapper.style.display = 'none';
});

form.addEventListener("submit", async (event) => {
  console.log("submitting form");
  event.preventDefault();
  let formData = new FormData(event.target);


  try {
    const response = await fetch("reservation.php", {
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
