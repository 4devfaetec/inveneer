const themeToggler = document.querySelector(".theme-toggler");

themeToggler.addEventListener("click", () => {
  document.body.classList.toggle("dark-mode");
  themeToggler.querySelector("span:nth-child(1").classList.toggle("active");
  themeToggler.querySelector("span:nth-child(2").classList.toggle("active");
});

document.body.classList.toggle("dark-mode");

// alert("Funciona")