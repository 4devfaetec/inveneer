const form = document.querySelector("#login-form");
const formBlur = document.querySelector("#form-blur");

function showForm() {
  form.style.top = "20%";
  formBlur.style.visibility = "visible";
}

function hideForm() {
  form.style.top = "-600px";
  formBlur.style.visibility = "hidden";
}
