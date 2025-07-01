function showTypeText() {
  const typedEmail = document.querySelector("#email-input").value;
  const typedPassword = document.querySelector("#password-input").value;
  console.log(typedEmail);
  console.log(typedPassword);
}

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("login-form");

  form.addEventListener("submit", function (e) {
    e.preventDefault(); // previne o envio tradicional

    const formData = new FormData(form);

    fetch("PHP/login.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.status === "ok") {
          Swal.fire({
            icon: "success",
            title: "Login realizado!",
            text: "Bem-vindo ao sistema!",
            showConfirmButton: false,
            timer: 1500,
          }).then(() => {
            window.location.href = data.redirect;
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Erro!",
            text: data.message || "Login inválido.",
          });
        }
      })
      .catch(() => {
        Swal.fire({
          icon: "error",
          title: "Erro!",
          text: "Não foi possível processar a solicitação.",
        });
      });
  });
});
