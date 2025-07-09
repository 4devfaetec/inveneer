// Alternar tema
const themeToggler = document.querySelector(".theme-toggler");
themeToggler?.addEventListener("click", () => {
  document.body.classList.toggle("dark-mode");
});

// Controle do aside
const menuItems = document.querySelectorAll("#menu li");
const sections = document.querySelectorAll(".section");

menuItems.forEach((item) => {
  item.addEventListener("click", () => {
    menuItems.forEach((el) => el.classList.remove("active"));
    item.classList.add("active");
    sections.forEach((sec) => sec.classList.remove("active-section"));
    const target = item.getAttribute("data-section");
    if (target) {
      const sectionToShow = document.getElementById(target);
      if (sectionToShow) {
        sectionToShow.classList.add("active-section");
      }
    }
  });
});


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



async function carregarProdutos() {
  try {
    const response = await fetch("../../PHP/listar_produtos.php");
    if (!response.ok) throw new Error("Erro na resposta do servidor: " + response.status);

    const produtos = await response.json();

    const tbody = document.querySelector("#produtos-table-body");
    tbody.innerHTML = "";  // limpa linhas antigas

    const estoqueMinimo = 10;

    produtos.forEach(produto => {
      const tr = document.createElement("tr");

      // Para evitar problemas com aspas, vamos escapar o JSON para o onclick
      const produtoJsonEscapado = JSON.stringify(produto).replace(/"/g, "&quot;");

      tr.innerHTML = `
        <th scope="row">${produto.id}</th>
        <td>${produto.nome}</td>
        <td>${produto.marca || "-"}</td>
        <td>${produto.categoria}</td>
        <td>R$ ${parseFloat(produto.preco).toFixed(2)}</td>
        <td>
          ${produto.estoque} 
          ${produto.estoque < estoqueMinimo ? 
            '<i class="bi bi-exclamation-triangle-fill text-warning" title="Estoque baixo"></i>' : ''}
        </td>
        <td>${formatarData(produto.ultima_atualizacao)}</td>
        <td>
          <i class="bi bi-pencil-square" style="cursor:pointer;" onclick="abrirEditarProduto(${produtoJsonEscapado})"></i>
        </td>
      `;

      tbody.appendChild(tr);
    });

    return produtos;
  } catch (error) {
    console.error("Erro ao carregar produtos:", error);
    return [];
  }
}


// Chame a função para carregar os produtos quando a página carregar
document.addEventListener("DOMContentLoaded", () => {
  carregarProdutos();
});





// Função sleep para delay
function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

const overlay = document.getElementById("overlay-loading");

function mostrarOverlay() {
  overlay.classList.add("active");
  document.body.style.overflow = "hidden";
}

async function esconderOverlay() {
  await sleep(2000);
  overlay.classList.remove("active");
  document.body.style.overflow = "";
}

let usuarioLogado = null;

fetch('../../PHP/dados_usuario.php')
  .then(res => res.json())
  .then(data => {
    if (data.erro) {
      console.error(data.erro);
      return;
    }
    usuarioLogado = data;
    const imgUser = document.querySelector(".img-user");
    if (imgUser) imgUser.textContent = obterIniciais(data.nome);
    const nameEl = document.querySelector(".name");
    if (nameEl) nameEl.textContent = data.nome;
    const privilegeEl = document.querySelector(".privilege");
    if (privilegeEl) privilegeEl.textContent = data.tipo === 'ADMIN' ? 'Administrador' : data.cargo;
  })
  .catch(console.error);

function obterIniciais(nome) {
  const nomes = nome.trim().split(" ");
  let iniciais = nomes[0][0].toUpperCase();
  if (nomes.length > 1) {
    iniciais += nomes[nomes.length - 1][0].toUpperCase();
  }
  return iniciais;
}

const formRelatorio = document.getElementById("form-relatorio-funcionario");

const iniciaisMap = {
  "Relatório de Estoque": "E",
  "Resumo de Produtos por Categoria": "C",
  "Resumo de Produtos por Marca": "M",
  "Produtos Atualizados Hoje": "A",
  "Resumo Geral de Usuários": "U",
  "Relatório de Usuários por Cargo": "G"
};

formRelatorio?.addEventListener("submit", async (e) => {
  e.preventDefault();

  let filtros = {};

  const filtrosText = document.getElementById("filtros_json")?.value.trim();
  if (filtrosText) {
    filtros["Adicional: "] = filtrosText;
  }

  // Pega título atual
  const titulo = tituloRelatorioSelecionado();

  // Escolhe os checkboxes corretos
  let checkboxes = [];
  if (titulo === "Relatório de Estoque" || titulo === "Produtos Atualizados Hoje") {
    checkboxes = document.querySelectorAll("#checkboxes-container input[type='checkbox'][name='produtos']:checked");
  } else if (titulo === "Resumo de Produtos por Categoria") {
    checkboxes = document.querySelectorAll("#checkboxes-container input[type='checkbox'][name='categorias']:checked");
  } else if (titulo === "Resumo de Produtos por Marca") {
    checkboxes = document.querySelectorAll("#checkboxes-container input[type='checkbox'][name='marcas']:checked");
  } else if (titulo === "Resumo Geral de Usuários") {
    checkboxes = document.querySelectorAll("#checkboxes-container input[type='checkbox'][name='usuarios']:checked");
  } else if (titulo === "Relatório de Usuários por Cargo") {
    checkboxes = document.querySelectorAll("#checkboxes-container input[type='checkbox'][name='cargos']:checked");
  }

  if (checkboxes.length === 0) {
    Swal.fire({
      icon: "warning",
      title: "Selecione ao menos um item",
      text: "Para enviar o relatório, selecione pelo menos um item.",
      background: "#fff",
      color: "#000",
      confirmButtonColor: "#2b2f3d",
    });
    return;
  }

  if (titulo === "Relatório de Estoque" || titulo === "Produtos Atualizados Hoje") {
    let produtosSelecionados = [];
    let novoEstoque = {};

    checkboxes.forEach(cb => {
      const produtoId = cb.value;
      produtosSelecionados.push(produtoId);

      const inputNovoEstoque = document.querySelector(`input[name="novo_estoque_${produtoId}"]`);
      if (inputNovoEstoque && inputNovoEstoque.value !== "") {
        novoEstoque[produtoId] = parseInt(inputNovoEstoque.value);
      }
    });

    filtros["produtos"] = produtosSelecionados;

    if (Object.keys(novoEstoque).length > 0) {
      filtros["novo_estoque"] = novoEstoque;
    }

    if (titulo === "Produtos Atualizados Hoje") {
      filtros = { atualizados_hoje: true };
    }

  } else if (titulo === "Resumo de Produtos por Categoria") {
    const categoriasSelecionadas = Array.from(checkboxes).map(cb => cb.value);
    filtros["categorias"] = categoriasSelecionadas;

  } else if (titulo === "Resumo de Produtos por Marca") {
    const marcasSelecionadas = Array.from(checkboxes).map(cb => cb.value);
    filtros["marcas"] = marcasSelecionadas;

  } else if (titulo === "Resumo Geral de Usuários") {
    const usuariosSelecionados = Array.from(checkboxes).map(cb => cb.value);
    filtros["usuarios"] = usuariosSelecionados;

  } else if (titulo === "Relatório de Usuários por Cargo") {
    const cargosSelecionados = Array.from(checkboxes).map(cb => cb.value);
    filtros["cargos"] = cargosSelecionados;
  }

  mostrarOverlay();

  try {
    const response = await fetch("../../PHP/enviar_relatorio.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ 
        filtros, 
        gerado_por: usuarioLogado ? usuarioLogado.nome : "Desconhecido",
        nome_relatorio: titulo,
        tipo_relatorio: tipoRelatorioSelecionado()  // <-- Aqui a mudança!
      }),
    });

    const textoResposta = await response.text();
    console.log("Resposta do servidor (texto cru):", textoResposta);

    const data = JSON.parse(textoResposta);

    await esconderOverlay();

    if (data.success) {
      Swal.fire({
        icon: "success",
        title: "Relatório enviado!",
        text: data.message || "Seu pedido de relatório foi enviado com sucesso.",
        background: "#fff",
        color: "#000",
        confirmButtonColor: "#2b2f3d",
      });
      fecharFormularioRelatorio();
      formRelatorio.reset();
    } else {
      Swal.fire({
        icon: "error",
        title: "Erro ao enviar relatório",
        text: data.message || "Ocorreu um erro ao enviar o relatório.",
        background: "#fff",
        color: "#000",
        confirmButtonColor: "#2b2f3d",
      });
    }
  } catch (error) {
    await esconderOverlay();
    console.error("Erro ao processar a resposta:", error);
    Swal.fire({
      icon: "error",
      title: "Erro na conexão",
      text: "Não foi possível enviar o relatório.",
      background: "#fff",
      color: "#000",
      confirmButtonColor: "#2b2f3d",
    });
  }
});

function tipoRelatorioSelecionado() {
  switch (tituloRelatorioSelecionado()) {
    case "Relatório de Estoque":
      return "Estoque";
    case "Resumo de Produtos por Categoria":
      return "Categoria";
    case "Resumo de Produtos por Marca":
      return "Marca";
    case "Produtos Atualizados Hoje":
      return "Atualizados Hoje";
    case "Resumo Geral de Usuários":
      return "Usuários";
    case "Relatório de Usuários por Cargo":
      return "Cargo";
    default:
      return "Personalizado";
  }
}


function formatarData(dataStr) {
  const d = new Date(dataStr);
  return d.toLocaleDateString("pt-BR", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit"
  });
}

async function carregarCheckboxes(titulo) {
  const container = document.getElementById("checkboxes-container");
  container.innerHTML = "";

  const filtrosDiv = document.getElementById("filtros_json_div");

  if (["Relatório de Estoque", "Resumo de Produtos por Categoria", "Resumo de Produtos por Marca", "Produtos Atualizados Hoje"].includes(titulo)) {
    const produtos = await carregarProdutos();

    if (titulo === "Produtos Atualizados Hoje") {
      if (filtrosDiv) filtrosDiv.style.display = "none";

      const hoje = new Date();
      const produtosHoje = produtos.filter(p => {
        if (!p.ultima_atualizacao) return false;
        const dataAtualizacao = new Date(p.ultima_atualizacao);
        return (
          dataAtualizacao.getDate() === hoje.getDate() &&
          dataAtualizacao.getMonth() === hoje.getMonth() &&
          dataAtualizacao.getFullYear() === hoje.getFullYear()
        );
      });

      if (produtosHoje.length === 0) {
        container.innerHTML = "<p>Nenhum produto atualizado hoje.</p>";
      } else {
        produtosHoje.forEach(p => {
          const div = document.createElement("div");
          div.innerHTML = `<label><input type="checkbox" value="${p.id}" name="produtos"> ${p.nome} (${p.categoria}) - Atualizado em ${formatarData(p.ultima_atualizacao)}</label>`;
          container.appendChild(div);
        });
      }
      return;
    }

    if (filtrosDiv) filtrosDiv.style.display = "block";

    if (titulo === "Relatório de Estoque") {
      produtos.forEach(p => {
        const div = document.createElement("div");
        div.classList.add("checkbox-estoque-item");
        div.innerHTML = `
          <label>
            <input type="checkbox" value="${p.id}" name="produtos">
            ${p.nome} (${p.categoria}) - Estoque atual: <strong>${p.estoque}</strong>
          </label>
          <input type="number" name="novo_estoque_${p.id}" placeholder="Novo estoque" min="0" disabled style="margin-left:10px; width: 90px; border: 1px solid; padding: 3px; border-radius: 5px;">
        `;

        const checkbox = div.querySelector('input[type="checkbox"]');
        const inputNovoEstoque = div.querySelector(`input[name="novo_estoque_${p.id}"]`);

        checkbox.addEventListener("change", () => {
          inputNovoEstoque.disabled = !checkbox.checked;
          if (!checkbox.checked) inputNovoEstoque.value = "";
        });

        container.appendChild(div);
      });
    }

    if (titulo === "Resumo de Produtos por Categoria") {
      const categorias = [...new Set(produtos.map(p => p.categoria))];
      categorias.forEach(c => {
        const div = document.createElement("div");
        div.innerHTML = `<label><input type="checkbox" value="${c}" name="categorias"> ${c}</label>`;
        container.appendChild(div);
      });
    }

    if (titulo === "Resumo de Produtos por Marca") {
      const marcas = [...new Set(produtos.map(p => p.marca))];
      marcas.forEach(m => {
        const div = document.createElement("div");
        div.innerHTML = `<label><input type="checkbox" value="${m}" name="marcas"> ${m}</label>`;
        container.appendChild(div);
      });
    }
  }

  if (["Resumo Geral de Usuários", "Relatório de Usuários por Cargo"].includes(titulo)) {
    if (filtrosDiv) filtrosDiv.style.display = "block";

    const usuarios = await carregarUsuarios();

    if (titulo === "Resumo Geral de Usuários") {
      usuarios.forEach(u => {
        const div = document.createElement("div");
        div.innerHTML = `<label><input type="checkbox" value="${u.id}" name="usuarios"> ${u.nome} (${u.cargo})</label>`;
        container.appendChild(div);
      });
    }

    if (titulo === "Relatório de Usuários por Cargo") {
      const cargos = [...new Set(usuarios.map(u => u.cargo))];
      cargos.forEach(c => {
        const div = document.createElement("div");
        div.innerHTML = `<label><input type="checkbox" value="${c}" name="cargos"> ${c}</label>`;
        container.appendChild(div);
      });
    }
  }
}

window.abrirFormularioRelatorio = function(titulo) {
  const overlay = document.getElementById("form-relatorio-shadow");
  const form = document.getElementById("form-relatorio-funcionario");
  const tituloEl = document.getElementById("titulo-relatorio");
  const labelFiltros = document.querySelector("label[for='filtros_json']");
  const textareaFiltros = document.getElementById("filtros_json");
  const filtrosDiv = document.getElementById("filtros_json_div");

  if (tituloEl) tituloEl.textContent = titulo;

  const filtrosExemplos = {
    "Relatório de Estoque": 'Ex: {"produto":"Smartphone","categoria":"Eletrônicos"}',
    "Resumo de Produtos por Categoria": 'Ex: {"categoria":"Eletrônicos"}',
    "Resumo de Produtos por Marca": 'Ex: {"marca":"Samsung"}',
    "Produtos Atualizados Hoje": '',
    "Resumo Geral de Usuários": 'Ex: {"cargo":"Vendedor"}',
    "Relatório de Usuários por Cargo": 'Ex: {"cargo":"Administrador"}'
  };

  if (labelFiltros && textareaFiltros) {
    labelFiltros.textContent = `Filtros para ${titulo} (opcional)`;
    textareaFiltros.placeholder = filtrosExemplos[titulo] || '';
    textareaFiltros.value = "";
  }

  if (filtrosDiv) {
    filtrosDiv.style.display = (titulo === "Produtos Atualizados Hoje") ? "none" : "block";
  }

  carregarCheckboxes(titulo);

  overlay.classList.add("active");
  form.classList.add("active");

  form.style.position = "fixed";
  form.style.top = "20vh";
  form.style.left = "50%";
  form.style.transform = "translateX(-50%)";
  form.style.bottom = "";
};

window.fecharFormularioRelatorio = function() {
  const overlay = document.getElementById("form-relatorio-shadow");
  const form = document.getElementById("form-relatorio-funcionario");
  overlay.classList.remove("active");
  form.classList.remove("active");
};

function tituloRelatorioSelecionado() {
  const tituloEl = document.getElementById("titulo-relatorio");
  return tituloEl ? tituloEl.textContent.trim() : "";
}
