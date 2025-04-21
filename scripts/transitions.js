const form = document.querySelector("#login-form");
const formBlur = document.querySelector("#form-blur");

function showForm() {
  form.style.top = "20%";
  form.style.visibility = "visible";
  formBlur.style.visibility = "visible";
  const darkModeBtn = document.querySelector(".switch")
  darkModeBtn.style.visibility = "hidden";
  document.body.style.overflow = 'hidden';
}

function hideForm() {
  form.style.top = "-1000px";
  form.style.visibility = "hidden";
  formBlur.style.visibility = "hidden";
  const darkModeBtn = document.querySelector(".switch")
  darkModeBtn.style.visibility = "visible";
  document.body.style.overflow = '';
}
