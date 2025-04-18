const form = document.querySelector("#login-form");
const formBlur = document.querySelector("#form-blur");

function showForm() {
  form.style.top = "20%";
  formBlur.style.visibility = "visible";
  const darkModeBtn = document.querySelector(".switch")
  darkModeBtn.style.visibility = "hidden";
}

function hideForm() {
  form.style.top = "-600px";
  formBlur.style.visibility = "hidden";
  const darkModeBtn = document.querySelector(".switch")
  darkModeBtn.style.visibility = "visible";
}
