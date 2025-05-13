const themeToggler = document.querySelector(".theme-toggler");
//document.body.classList.toggle("dark-mode");

const menuItems = document.querySelectorAll("#menu li");
const sections = document.querySelectorAll(".section");

menuItems.forEach((item) => {
  item.addEventListener("click", () => {
    // Remover classe 'active' do menu
    menuItems.forEach((el) => el.classList.remove("active"));
    item.classList.add("active");

    // Esconder todas as seções
    sections.forEach((sec) => sec.classList.remove("active-section"));

    // Mostrar a seção correspondente
    const target = item.getAttribute("data-section");
    document.getElementById(target).classList.add("active-section");
  });
});
