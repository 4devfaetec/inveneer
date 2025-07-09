const overlay = document.getElementById("overlay-loading");
const formShadow = document.querySelector("#stock .form-shadow");
const editarForm = document.getElementById("editar-produto-form");

let produtoAtualId = null;

// Variável global para guardar filtros ativos
let filtrosAtivos = {};

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

function abrirEditarProduto(produto) {
  produtoAtualId = produto.id;
  document.getElementById("editar_nome_produto").value = produto.nome;
  document.getElementById("editar_marca").value = produto.marca || "";
  document.getElementById("editar_categoria").value = produto.categoria;
  document.getElementById("editar_preco").value = produto.preco;
  document.getElementById("editar_estoque").value = produto.estoque;

  editarForm.classList.add("active");
  formShadow.classList.add("active");
  document.body.style.overflow = "hidden";
}

function fecharEditarProduto() {
  editarForm.classList.remove("active");
  formShadow.classList.remove("active");
  produtoAtualId = null;

  const btnSalvar = document.getElementById("btn-salvar-produto");
  if (btnSalvar) btnSalvar.blur();

  const onTransitionEnd = (event) => {
    if (event.propertyName === "top" || event.propertyName === "opacity") {
      document.body.style.overflow = "";
      editarForm.removeEventListener("transitionend", onTransitionEnd);
    }
  };
  editarForm.addEventListener("transitionend", onTransitionEnd);
}

function abrirProdutoForm() {
  const form = document.getElementById("produto-form");
  form.classList.add("active");
  formShadow.classList.add("active");
  document.body.style.overflow = "hidden";
}

function fecharProdutoForm() {
  const form = document.getElementById("produto-form");
  form.classList.remove("active");
  formShadow.classList.remove("active");

  const onTransitionEnd = (event) => {
    if (event.propertyName === "top" || event.propertyName === "opacity") {
      document.body.style.overflow = "";
      form.removeEventListener("transitionend", onTransitionEnd);
    }
  };
  form.addEventListener("transitionend", onTransitionEnd);
}

// Função para formatar data no padrão brasileiro com hora
function formatarData(dataString) {
  const data = new Date(dataString);
  const dia = String(data.getDate()).padStart(2, "0");
  const mes = String(data.getMonth() + 1).padStart(2, "0");
  const ano = data.getFullYear();
  const horas = String(data.getHours()).padStart(2, "0");
  const minutos = String(data.getMinutes()).padStart(2, "0");
  return `${dia}/${mes}/${ano} às ${horas}:${minutos}`;
}

// Carrega tipos de relatório no select dinamicamente
function carregarTiposRelatorio() {
  const selectRelType = document.getElementById("relType");

  fetch("../../PHP/listar_tipos_relatorios.php")
    .then(res => res.json())
    .then(data => {
      if (data.success && Array.isArray(data.tipos)) {
        selectRelType.innerHTML = '<option value="">Selecione...</option>';

        data.tipos.forEach(tipo => {
          const option = document.createElement("option");
          option.value = tipo;
          option.textContent = tipo;
          selectRelType.appendChild(option);
        });

        // Se tiver filtro ativo, mantém selecionado o tipo
        if (filtrosAtivos.tipo) {
          selectRelType.value = filtrosAtivos.tipo;
        }
      } else {
        console.error("Erro ao carregar tipos de relatório");
      }
    })
    .catch(err => console.error("Erro ao buscar tipos:", err));
}

// Carrega relatórios, pode passar filtro opcional {tipo, dataInicial, dataFinal}
function carregarRelatorios(filtros = {}) {
  const params = new URLSearchParams();

  if (filtros.tipo) params.append("tipo", filtros.tipo);
  if (filtros.dataInicial) params.append("data_inicial", filtros.dataInicial);
  if (filtros.dataFinal) params.append("data_final", filtros.dataFinal);

  fetch(`../../PHP/listar_relatorios.php?${params.toString()}`)
    .then(res => res.json())
    .then(data => {
      if (!data.success || !data.relatorios) {
        console.error("Erro ao carregar relatórios");
        return;
      }

      const tbody = document.getElementById("relatorios-body");
      tbody.innerHTML = "";

      data.relatorios.forEach((rel) => {
        const dataFormatada = formatarData(rel.data);

        const tr = document.createElement("tr");
        tr.innerHTML = `
          <th scope="row">${dataFormatada}</th>
          <td>${rel.nome}</td>
          <td>${rel.tipo}</td>
          <td>${rel.gerado_por}</td>
          <td>
            <i class="bi bi-eye" style="cursor:pointer" onclick="verRelatorio('${rel.id}')"></i>
            <i class="bi bi-download" style="cursor:pointer; margin-left:10px"></i>
          </td>
        `;
        tbody.appendChild(tr);
      });
    })
    .catch(err => {
      console.error(err);
      alert("Erro ao carregar os relatórios.");
    });
}

// Função para abrir modal com relatório pelo id
function verRelatorio(id) {
  fetch(`../../PHP/obter_relatorio.php?id=${encodeURIComponent(id)}`)
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        alert("Erro ao carregar relatório");
        return;
      }
      // Coloca o título e o conteúdo HTML recebido
      document.getElementById("modal-titulo").textContent = data.relatorio.nome || "Relatório";
      document.getElementById("modal-conteudo").innerHTML = data.relatorio.conteudo_html || "<p>Sem conteúdo.</p>";

      // Exibe o modal
      document.getElementById("modal-relatorio").style.display = "block";
      document.getElementById("modal-overlay").style.display = "block";
      document.body.style.overflow = "hidden";
    })
    .catch(() => alert("Erro ao carregar relatório"));
}


// Funções para fechar modal de relatório
document.getElementById("btn-fechar-modal").addEventListener("click", () => {
  document.getElementById("modal-relatorio").style.display = "none";
  document.getElementById("modal-overlay").style.display = "none";
  document.body.style.overflow = "";
});
document.getElementById("modal-overlay").addEventListener("click", () => {
  document.getElementById("modal-relatorio").style.display = "none";
  document.getElementById("modal-overlay").style.display = "none";
  document.body.style.overflow = "";
});

document.addEventListener("DOMContentLoaded", () => {
  const btnFiltrar = document.getElementById("btnFiltrar");
  if (btnFiltrar) {
    btnFiltrar.addEventListener("click", () => {
      filtrosAtivos.tipo = document.getElementById("relType").value;
      filtrosAtivos.dataInicial = document.getElementById("dataInicial").value;
      filtrosAtivos.dataFinal = document.getElementById("dataFinal").value;

      carregarRelatorios(filtrosAtivos);
    });
  }

  const btnLimparFiltro = document.getElementById("btnLimparFiltro");
  if (btnLimparFiltro) {
    btnLimparFiltro.addEventListener("click", () => {
      document.getElementById("relType").value = "";
      document.getElementById("dataInicial").value = "";
      document.getElementById("dataFinal").value = "";

      filtrosAtivos = {};

      carregarRelatorios();
    });
  }

  carregarTiposRelatorio();
  carregarRelatorios(filtrosAtivos);
});

// --- Continua seu código para produtos, usuários, etc ---

async function salvarEdicaoProduto() {
  const nome = document.getElementById("editar_nome_produto").value;
  const marca = document.getElementById("editar_marca").value;
  const categoria = document.getElementById("editar_categoria").value;
  const preco = document.getElementById("editar_preco").value;
  const estoque = document.getElementById("editar_estoque").value;

  if (!produtoAtualId) {
    alert("Nenhum produto selecionado para edição.");
    return;
  }

  mostrarOverlay();
  await new Promise((resolve) => setTimeout(resolve, 2000));

  fetch("../../PHP/atualizar_produto.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id: produtoAtualId, nome_produto: nome, marca, categoria, preco, estoque }),
  })
    .then((res) => res.json())
    .then((data) => {
      esconderOverlay();
      if (data.success || data.status === "ok") {
        Swal.fire({
          icon: "success",
          title: "Produto atualizado!",
          text: data.message || "Alterações salvas com sucesso!",
          background: "#121212",
          color: "#fff",
          confirmButtonColor: "#2b2f3d",
        });
        fecharEditarProduto();
        carregarProdutos();
      } else {
        Swal.fire({
          icon: "error",
          title: "Erro ao salvar!",
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
      const estoqueMinimo = 10;

      data.forEach((produto) => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <th scope="row">${produto.id}</th>
          <td>${produto.nome}</td>
          <td>${produto.marca || "-"}</td>
          <td>${produto.categoria}</td>
          <td>R$ ${parseFloat(produto.preco).toFixed(2)}</td>
          <td>${produto.estoque}${produto.estoque < estoqueMinimo ? ' <i class="bi bi-exclamation-triangle-fill text-warning" title="Estoque baixo"></i>' : ""}</td>
          <td>${formatarData(produto.ultima_atualizacao)}</td>
          <td><i class="bi bi-pencil-square" style="cursor: pointer;" onclick="abrirEditarProduto(${JSON.stringify(produto).replace(/"/g, '&quot;')})"></i></td>
        `;
        tbody.appendChild(tr);
      });
    })
    .catch((error) => console.error("Erro ao carregar produtos:", error));
}

async function atualizarTudo() {
  try {
    await Promise.all([
      carregarProdutos(),
      carregarUsuarios(),
      carregarRelatorios(filtrosAtivos),
      atualizarUltimoAcesso(),
    ]);
  } catch (err) {
    console.error(err);
  }
}

setInterval(atualizarTudo, 5000);
atualizarTudo();

document.addEventListener("DOMContentLoaded", () => {
  const fecharBtn = document.getElementById("fechar-edit");
  if (fecharBtn) fecharBtn.addEventListener("click", fecharEditarProduto);

  if (formShadow) {
    formShadow.addEventListener("click", (e) => {
      if (e.target === formShadow) {
        fecharEditarProduto();
        fecharProdutoForm();
      }
    });
  }

  const formUsuario = document.getElementById("ad-func");
  const formProduto = document.getElementById("produto-form");

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

menuItems.forEach((item) => {
  item.addEventListener("click", () => {
    menuItems.forEach((el) => el.classList.remove("active"));
    item.classList.add("active");
    sections.forEach((sec) => sec.classList.remove("active-section"));

    const target = item.getAttribute("data-section");
    document.getElementById(target).classList.add("active-section");
  });
});

function obterIniciais(nome) {
  const nomes = nome.trim().split(" ");
  let iniciais = nomes[0][0].toUpperCase();
  if (nomes.length > 1) {
    iniciais += nomes[nomes.length - 1][0].toUpperCase();
  }
  return iniciais;
}

fetch('../../PHP/dados_usuario.php')
  .then(res => res.json())
  .then(data => {
    if (data.erro) {
      console.error(data.erro);
      return;
    }
    document.querySelector(".img-user").textContent = obterIniciais(data.nome);
    document.querySelector(".name").textContent = data.nome;
    document.querySelector(".privilege").textContent = data.tipo === 'ADMIN' ? 'Administrador' : data.cargo;
  })
  .catch(console.error);




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

function showForm() {
  formAdd.style.top = "30%";
  formAdd.style.visibility = "visible";
  formShadow.style.visibility = "visible";
  document.body.style.overflow = "hidden";
  document.lastModified.style.visibility = "hidden";
}

function hideForm() {
  formAdd.style.top = "-1000px";
  formAdd.style.visibility = "hidden";
  formShadow.style.visibility = "hidden";
  document.body.style.overflow = "";
}

const stockAdd = document.querySelector(".ad-prod");

function showStockForm() {
  stockAdd.style.top = "30%";
  stockAdd.style.visibility = "visible";
  formShadow.style.visibility = "visible";
  document.body.style.overflow = "hidden";
  document.aside.style.visibility = "hidden";
}

function hideStockForm() {
  stockAdd.style.top = "-1000px";
  stockAdd.style.visibility = "hidden";
  formShadow.style.visibility = "hidden";
  document.body.style.overflow = "";
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
      backgroundColor: ["#3b82f6", "#10b981", "#f59e0b", "#8b5cf6", "#ef4444"],
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
