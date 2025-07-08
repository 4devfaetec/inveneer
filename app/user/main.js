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
    const sectionToShow = document.getElementById(target);
    if (sectionToShow) {
      sectionToShow.classList.add("active-section");
    }
  });
});

async function carregarProdutos() {
  const res = await fetch("../../PHP/listar_produtos.php");
  if (!res.ok) throw new Error("Erro ao carregar produtos");
  return await res.json();
}

async function carregarUsuarios() {
  const res = await fetch("../../PHP/listar_usuarios.php");
  if (!res.ok) throw new Error("Erro ao carregar usuários");
  return await res.json();
}

async function carregarCheckboxes(titulo) {
  const container = document.getElementById("checkboxes-container");
  container.innerHTML = "";

  if (["Relatório de Estoque", "Resumo de Produtos por Categoria", "Resumo de Produtos por Marca", "Produtos Atualizados Hoje"].includes(titulo)) {
    const produtos = await carregarProdutos();

    if (titulo === "Relatório de Estoque") {
      produtos.forEach(p => {
        const div = document.createElement("div");
        div.innerHTML = `<input type="checkbox" value="${p.id}" name="produtos"> ${p.nome} (${p.categoria})`;
        container.appendChild(div);
      });
    }

    if (titulo === "Resumo de Produtos por Categoria") {
      const categorias = [...new Set(produtos.map(p => p.categoria))];
      categorias.forEach(c => {
        const div = document.createElement("div");
        div.innerHTML = `<input type="checkbox" value="${c}" name="categorias"> ${c}`;
        container.appendChild(div);
      });
    }

    if (titulo === "Resumo de Produtos por Marca") {
      const marcas = [...new Set(produtos.map(p => p.marca))];
      marcas.forEach(m => {
        const div = document.createElement("div");
        div.innerHTML = `<input type="checkbox" value="${m}" name="marcas"> ${m}`;
        container.appendChild(div);
      });
    }
  }

  if (["Resumo Geral de Usuários", "Relatório de Usuários por Cargo"].includes(titulo)) {
    const usuarios = await carregarUsuarios();

    if (titulo === "Resumo Geral de Usuários") {
      usuarios.forEach(u => {
        const div = document.createElement("div");
        div.innerHTML = `<input type="checkbox" value="${u.id}" name="usuarios"> ${u.nome} (${u.cargo})`;
        container.appendChild(div);
      });
    }

    if (titulo === "Relatório de Usuários por Cargo") {
      const cargos = [...new Set(usuarios.map(u => u.cargo))];
      cargos.forEach(c => {
        const div = document.createElement("div");
        div.innerHTML = `<input type="checkbox" value="${c}" name="cargos"> ${c}`;
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

  if (tituloEl) tituloEl.textContent = titulo;
  labelFiltros.textContent = `Filtros para ${titulo} (opcional)`;
  textareaFiltros.placeholder = 'Ex: {"categoria":"Eletrônicos"}';

  carregarCheckboxes(titulo);

  overlay.classList.add("active");
  form.classList.add("active");
};

window.fecharFormularioRelatorio = function() {
  const overlay = document.getElementById("form-relatorio-shadow");
  const form = document.getElementById("form-relatorio-funcionario");
  overlay.classList.remove("active");
  form.classList.remove("active");
};

const overlay = document.getElementById("form-relatorio-shadow");
overlay?.addEventListener("click", fecharFormularioRelatorio);

const formRelatorio = document.getElementById("form-relatorio-funcionario");
formRelatorio?.addEventListener("submit", (e) => {
  e.preventDefault();

  const filtrosText = document.getElementById("filtros_json")?.value || "{}";
  let filtros = {};
  try {
    filtros = JSON.parse(filtrosText);
  } catch {
    alert("JSON de filtros inválido!");
    return;
  }

  const checkboxes = document.querySelectorAll("#checkboxes-container input[type='checkbox']:checked");
  checkboxes.forEach(cb => {
    const name = cb.name;
    if (!filtros[name]) filtros[name] = [];
    filtros[name].push(cb.value);
  });

  console.log("Enviar relatório com filtros:", filtros);

  fecharFormularioRelatorio();
  formRelatorio.reset();
});
