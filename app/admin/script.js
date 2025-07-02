const overlay = document.getElementById("overlay-loading");

function mostrarOverlay() {
  overlay.classList.add("active");
  document.body.style.overflow = "hidden";
}

function esconderOverlay() {
  overlay.classList.remove("active");
  document.body.style.overflow = "";
}

const themeToggler = document.querySelector(".theme-toggler");
document.body.classList.toggle("dark-mode");

const menuItems = document.querySelectorAll("#menu li");
const sections = document.querySelectorAll(".section");

document.addEventListener("DOMContentLoaded", () => {
  const formUsuario = document.getElementById("ad-func");
  const formProduto = document.getElementById("produto-form");

  async function enviarFormulario(form) {
  mostrarOverlay();

  // Delay para visualizar animação
  await new Promise((resolve) => setTimeout(resolve, 3000));

  const formData = new FormData(form);

  fetch(form.action, {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      esconderOverlay();

      if (data.success || data.status === "ok") {
        Swal.fire({
          icon: "success",
          title:
            form.id === "ad-func"
              ? "Usuário cadastrado!"
              : "Produto cadastrado!",
          text: data.message || "Cadastro realizado com sucesso!",
          background: "#121212",
          color: "#fff",
          confirmButtonColor: "#2b2f3d",
        });

        form.reset();
        if (typeof hideForm === "function") hideForm();

        // Atualiza a lista correspondente imediatamente
        if (form.id === "ad-func") {
          carregarUsuarios();
        } else if (form.id === "produto-form") {
          carregarProdutos();
        }
      } else {
        Swal.fire({
          icon: "error",
          title: "Erro ao cadastrar!",
          text: data.message || "Erro desconhecido.",
          background: "#121212",
          color: "#fff",
          confirmButtonColor: "#2b2f3d",
        });
      }
    })
    .catch(() => {
      esconderOverlay();
      Swal.fire({
        icon: "error",
        title: "Erro na conexão!",
        text: "Não foi possível processar a solicitação.",
        background: "#121212",
        color: "#fff",
        confirmButtonColor: "#2b2f3d",
      });
    });
}


  if (formUsuario) {
    formUsuario.addEventListener("submit", (e) => {
      e.preventDefault();
      enviarFormulario(formUsuario);
    });
  }

  if (formProduto) {
    formProduto.addEventListener("submit", (e) => {
      e.preventDefault();
      enviarFormulario(formProduto);
    });
  }
});

function atualizarUltimoAcesso() {
  return fetch("../../PHP/ultimo_acesso.php")
    .then((response) => response.text())
    .then((data) => console.log(data))
    .catch((err) => console.error("Erro ao atualizar último acesso:", err));
}

function carregarUsuarios() {
  return fetch("../../PHP/listar_usuarios.php")
    .then((response) => response.json())
    .then((data) => {
      const tbody = document.querySelector("#usuarios-table-body");
      tbody.innerHTML = "";
      data.forEach((usuario) => {
        tbody.innerHTML += `
          <tr>
            <th scope="row">${usuario.id}</th>
            <td>${usuario.nome}</td>
            <td>${usuario.email}</td>
            <td>${usuario.cargo}</td>
            <td>${usuario.status}</td>
            <td>${usuario.ultimo_acesso}</td>
            <td><i class="bi bi-pencil-square"></i></td>
          </tr>
        `;
      });
    })
    .catch((error) => console.error("Erro ao carregar usuários:", error));
}

function carregarProdutos() {
  return fetch("../../PHP/listar_produtos.php")
    .then((response) => {
      if (!response.ok) throw new Error("Erro na resposta do servidor: " + response.status);
      return response.json();
    })
    .then((data) => {
      const tbody = document.querySelector("#produtos-table-body");
      tbody.innerHTML = "";
      data.forEach((produto) => {
        tbody.innerHTML += `
          <tr>
            <th scope="row">${produto.id}</th>
            <td>${produto.nome}</td>
            <td>${produto.categoria}</td>
            <td>R$ ${parseFloat(produto.preco).toFixed(2)}</td>
            <td>${produto.estoque}</td>
            <td>${formatarData(produto.ultima_atualizacao)}</td>
            <td><i class="bi bi-pencil-square"></i></td>
          </tr>
        `;
      });
    })
    .catch((error) => console.error("Erro ao carregar produtos:", error));
}

function formatarData(dataString) {
  const data = new Date(dataString);
  const dia = String(data.getDate()).padStart(2, "0");
  const mes = String(data.getMonth() + 1).padStart(2, "0");
  const ano = data.getFullYear();
  const horas = String(data.getHours()).padStart(2, "0");
  const minutos = String(data.getMinutes()).padStart(2, "0");
  return `${dia}/${mes}/${ano} às ${horas}:${minutos}`;
}

async function atualizarTudo() {
  try {
    await Promise.all([
      carregarProdutos(),
      carregarUsuarios(),
      atualizarUltimoAcesso()
    ]);
  } catch (err) {
    console.error(err);
  }
}

setInterval(atualizarTudo, 5000);
atualizarTudo();

menuItems.forEach((item) => {
  item.addEventListener("click", () => {
    menuItems.forEach((el) => el.classList.remove("active"));
    item.classList.add("active");
    sections.forEach((sec) => sec.classList.remove("active-section"));
    const target = item.getAttribute("data-section");
    document.getElementById(target).classList.add("active-section");
  });
});


menuItems.forEach((item) => {
  item.addEventListener("click", () => {
    menuItems.forEach((el) => el.classList.remove("active"));
    item.classList.add("active");
    sections.forEach((sec) => sec.classList.remove("active-section"));
    const target = item.getAttribute("data-section");
    document.getElementById(target).classList.add("active-section");
  });
});


menuItems.forEach((item) => {
  item.addEventListener("click", () => {
    // Remove classe active do menu
    menuItems.forEach((el) => el.classList.remove("active"));
    item.classList.add("active");

    // Esconde todas as seções
    sections.forEach((sec) => sec.classList.remove("active-section"));

    // Mostra a seção correspondente
    const target = item.getAttribute("data-section");
    document.getElementById(target).classList.add("active-section");
  });
});


function atualizarUltimoAcesso() {
  return fetch("../../PHP/ultimo_acesso.php")
    .then((response) => response.text())
    .then((data) => console.log(data))
    .catch((err) => console.error("Erro ao atualizar último acesso:", err));
}

function carregarUsuarios() {
  return fetch("../../PHP/listar_usuarios.php")
    .then((response) => response.json())
    .then((data) => {
      const tbody = document.querySelector("#usuarios-table-body");
      tbody.innerHTML = "";
      data.forEach((usuario) => {
        tbody.innerHTML += `
          <tr>
            <th scope="row">${usuario.id}</th>
            <td>${usuario.nome}</td>
            <td>${usuario.email}</td>
            <td>${usuario.cargo}</td>
            <td>${usuario.status}</td>
            <td>${usuario.ultimo_acesso}</td>
            <td><i class="bi bi-pencil-square"></i></td>
          </tr>
        `;
      });
    })
    .catch((error) => console.error("Erro ao carregar usuários:", error));
}

function carregarProdutos() {
  return fetch("../../PHP/listar_produtos.php")
    .then((response) => {
      if (!response.ok) throw new Error("Erro na resposta do servidor: " + response.status);
      return response.json();
    })
    .then((data) => {
      const tbody = document.querySelector("#produtos-table-body");
      tbody.innerHTML = "";
      data.forEach((produto) => {
        tbody.innerHTML += `
          <tr>
            <th scope="row">${produto.id}</th>
            <td>${produto.nome}</td>
            <td>${produto.categoria}</td>
            <td>R$ ${parseFloat(produto.preco).toFixed(2)}</td>
            <td>${produto.estoque}</td>
            <td>${formatarData(produto.ultima_atualizacao)}</td>
            <td><i class="bi bi-pencil-square"></i></td>
          </tr>
        `;
      });
    })
    .catch((error) => console.error("Erro ao carregar produtos:", error));
}

function formatarData(dataString) {
  const data = new Date(dataString);
  const dia = String(data.getDate()).padStart(2, "0");
  const mes = String(data.getMonth() + 1).padStart(2, "0");
  const ano = data.getFullYear();
  const horas = String(data.getHours()).padStart(2, "0");
  const minutos = String(data.getMinutes()).padStart(2, "0");
  return `${dia}/${mes}/${ano} às ${horas}:${minutos}`;
}

async function atualizarTudo() {
  try {
    await Promise.all([carregarProdutos(), carregarUsuarios(), atualizarUltimoAcesso()]);
  } catch (err) {
    console.error(err);
  }
}

setInterval(atualizarTudo, 5000);
atualizarTudo();

menuItems.forEach((item) => {
  item.addEventListener("click", () => {
    menuItems.forEach((el) => el.classList.remove("active"));
    item.classList.add("active");

    sections.forEach((sec) => sec.classList.remove("active-section"));

    const target = item.getAttribute("data-section");
    document.getElementById(target).classList.add("active-section");
  });
});




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

const dadosIniciais = {
  labels: ["Janeiro", "Fevereiro", "Março", "Abril"],
  datasets: [
    {
      label: "Vendas (em R$)",
      data: [1200, 1900, 3000, 500],
      backgroundColor: "rgb(216, 211, 211)",
      borderColor: "rgb(0, 0, 0)",
      borderWidth: 1.5,
    },
  ],
};

// Configuração do gráfico
const config = {
  type: "bar",
  data: dadosIniciais,
  options: {
    responsive: true,
    scales: {
      y: {
        beginAtZero: true,
      },
    },
  },
};

// Inicializa o gráfico
const ctx = document.getElementById("myChart").getContext("2d");
const myChart = new Chart(ctx, config);

// Função para atualizar os dados dinamicamente
function atualizarDados() {
  // Gera novos valores aleatórios para os dados
  myChart.data.datasets[0].data = gerarNumerosAleatorios(4, 500, 5000);
  myChart.update(); // Atualiza o gráfico com os novos dados
}

// Gera números aleatórios (exemplo simples)
function gerarNumerosAleatorios(qtd, min, max) {
  const valores = [];
  for (let i = 0; i < qtd; i++) {
    valores.push(Math.floor(Math.random() * (max - min + 1)) + min);
  }
  return valores;
}

const dadosIniciaisLine = {
  labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
  datasets: [
    {
      label: "Vendas (em R$)",
      data: [1200, 1500, 1800, 2200, 1700, 2500],
      fill: false, // Não preenche a área abaixo da linha
      borderColor: "rgba(75, 192, 192, 1)", // Cor da linha
      tension: 0.1, // Curvatura da linha
    },
  ],
};

// Configuração do gráfico de linha
const configLine = {
  type: "line", // Tipo do gráfico
  data: dadosIniciais,
  options: {
    responsive: true,
    scales: {
      x: {
        title: {
          display: true,
          text: "Meses",
        },
      },
      y: {
        title: {
          display: true,
          text: "Valor (R$)",
        },
        beginAtZero: true, // Começar o eixo Y do gráfico no zero
      },
    },
  },
};

// Inicializa o gráfico
const ctx2 = document.getElementById("lineChart").getContext("2d");
const lineChart = new Chart(ctx2, configLine);

// Função para atualizar os dados do gráfico dinamicamente
function atualizarDadosLine() {
  // Novos dados aleatórios para o gráfico
  lineChart.data.datasets[0].data = gerarNumerosAleatorios(6, 1000, 3000);
  lineChart.update(); // Atualiza o gráfico
}

// Função para gerar números aleatórios (exemplo simples)
function gerarNumerosAleatorios(qtd, min, max) {
  const valores = [];
  for (let i = 0; i < qtd; i++) {
    valores.push(Math.floor(Math.random() * (max - min + 1)) + min);
  }
  return valores;
}

const formAdd = document.querySelector("#ad-func");
const formShadow = document.querySelector(".form-shadow");

function showForm() {
  formAdd.style.top = "30%";
  formAdd.style.visibility = "visible";
  formShadow.style.visibility = "visible";
  document.body.style.overflow = 'hidden';
  document.lastModified.style.visibility = "hidden";
}

function hideForm() {
  formAdd.style.top = "-1000px";
  formAdd.style.visibility = "hidden";
  formShadow.style.visibility = "hidden";
  document.body.style.overflow = '';
}

const stockAdd = document.querySelector(".ad-prod");

function showStockForm() {
  stockAdd.style.top = "30%";
  stockAdd.style.visibility = "visible";
  formShadow.style.visibility = "visible";
  document.body.style.overflow = 'hidden';
  document.aside.style.visibility = "hidden";
}

function hideStockForm() {
  stockAdd.style.top = "-1000px";
  stockAdd.style.visibility = "hidden";
  formShadow.style.visibility = "hidden";
  document.body.style.overflow = '';
}

const dadosLinhaVendas = {
  labels: ["Dez", "Jan", "Fev", "Mar", "Abr", "Mai"],
  datasets: [
    {
      label: "Tendência de Vendas",
      data: [8000, 12000, 18000, 24000, 30000, 36000],
      borderColor: "#3e95cd",
      backgroundColor: "rgba(62, 149, 205, 0.2)",
      fill: true,
      tension: 0.4,
      pointBackgroundColor: "#2e7cc7",
      pointRadius: 4,
    },
  ],
};

const configLinhaVendas = {
  type: "line",
  data: dadosLinhaVendas,
  options: {
    responsive: true,
    plugins: {
      title: {
        display: true,
        text: "Tendência de Vendas - Últimos 6 Meses",
        font: {
          size: 18,
        },
      },
    },
    scales: {
      y: {
        beginAtZero: true,
        max: 40000,
        ticks: {
          callback: function (value) {
            return value / 1000 + "k";
          },
        },
      },
    },
  },
};

// Inicialize o novo gráfico
const ctxLinhaVendas = document
  .getElementById("linhaVendasChart")
  .getContext("2d");
const linhaVendasChart = new Chart(ctxLinhaVendas, configLinhaVendas);

const dadosTopProdutos = {
  labels: [
    "Smartphone XYZ",
    "Notebook Ultra",
    'Monitor 24"',
    "Teclado Mecânico",
    "Mouse Gamer",
  ],
  datasets: [
    {
      label: "Quantidade Vendida",
      data: [95, 85, 60, 40, 35],
      backgroundColor: [
        "#3b82f6",
        "#10b981",
        "#f59e0b",
        "#8b5cf6",
        "#ef4444",
      ],
      borderColor: "#000",
      borderWidth: 1.5,
    },
  ],
};

const configTopProdutos = {
  type: "bar",
  data: dadosTopProdutos,
  options: {
    responsive: true,
    indexAxis: "x", // barras verticais
    plugins: {
      title: {
        display: true,
        text: "Produtos Mais Vendidos - Top 5 por Quantidade",
        font: {
          size: 18,
        },
      },
    },
    scales: {
      y: {
        beginAtZero: true,
        max: 100,
        ticks: {
          stepSize: 25,
        },
        title: {
          display: true,
          text: "Quantidade",
        },
      },
    },
  },
};

// Inicialização do gráfico
const ctxTopProdutos = document
  .getElementById("topProdutosChart")
  .getContext("2d");
const topProdutosChart = new Chart(ctxTopProdutos, configTopProdutos);