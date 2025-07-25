<?php
session_start();

// Verifica se o usuário está logado e se o cargo é GERENTE
if (!isset($_SESSION['cargo']) || $_SESSION['cargo'] !== 'GERENTE') {
    header("Location: ../../login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="main.css" />
     <!-- <link rel="stylesheet" href="../../scss/tables.scss" />-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    />
    <!--<link
      rel="stylesheet"
     href="https://unpkg.com/bootstrap-table@1.21.2/dist/bootstrap-table.min.css" 
    /> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Admin dashboard</title>
  </head>
  <body>
    <aside>
      <div class="logo">
        <p>Inve<span>neer</span></p>
      </div>
      <ul id="menu">
        <li data-section="dashboard" class="active">
          <i class="bi bi-grid"></i> <span>Dashboard</span>
        </li>
        <li data-section="stock">
          <i class="bi bi-box-seam"></i> <span>Estoque</span>
        </li>
        <li data-section="reports">
          <i class="bi bi-clipboard-data"></i> <span>Relatórios</span>
        </li>
        <li data-section="analitycs">
          <i class="bi bi-bar-chart-line"></i> <span>Análises</span>
        </li>
        <li data-section="users">
          <i class="bi bi-people"></i> <span>Usuários</span>
        </li>
        <li data-section="config">
          <i class="bi bi-gear"></i> <span>Configurações</span>
        </li>
        <li>
          <div class="img-user"></div>
          <span class="user-infos">
            <span class="name"></span>
            <span class="privilege"></span>
          </span>
        </li>
      </ul>
    </aside>
    <main>
      <div id="dashboard" class="section active-section">
        <div class="top-box">
          <div class="top-infos">
            <i class="bi bi-archive"></i>
            <span class="info-title">Itens em Estoque</span>
            <span class="info-number">128</span>
          </div>
          <div class="top-infos">
            <i class="bi bi-truck"></i>
            <span class="info-title">Entradas Recentes</span>
            <span class="info-number">17</span>
          </div>
          <div class="top-infos">
            <i class="bi bi-exclamation-triangle"></i>
            <span class="info-title">Produtos com Baixo Estoque</span>
            <span class="info-number">12</span>
          </div>
          <div class="top-infos">
            <i class="bi bi-cash-coin"></i>
            <span class="info-title">Vendas do dia</span>
            <span class="info-number">1600</span>
          </div>
        </div>

        <div class="chart-section">
          <div class="chart-box">
            <h2>Visão Geral de Vendas</h2>
            <canvas id="lineChart"></canvas>

            <button onclick="atualizarDadosLine()">Atualizar Dados</button>
          </div>

          <div class="chart-box">
            <h2>Total de vendas</h2>
            <canvas id="myChart"></canvas>

            <button onclick="atualizarDados()">Atualizar Dados</button>
          </div>
        </div>
        <div class="table-box">
          <table class="table table-striped table-dark table-hover">
            <thead>
              <tr>
                <th scope="col">Data</th>
                <th scope="col">Descrição</th>
                <th scope="col">ID Produto</th>
                <th scope="col">Qtd</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>30/04/2025</td>
                <td>Entrada de estoque</td>
                <td>128594</td>
                <td>150</td>
              </tr>
              <tr>
                <td>30/04/2025</td>
                <td>Saída de estoque</td>
                <td>178563</td>
                <td>12</td>
              </tr>
              <tr>
                <td>30/04/2025</td>
                <td>Saída de estoque</td>
                <td>548267</td>
                <td>10</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div id="stock" class="section">
  <div class="form-shadow" id="form-shadow"></div>

  <!-- Form de adicionar produto -->
  <form class="ad-prod" id="produto-form" action="../../PHP/criar_produto.php" method="post" enctype="multipart/form-data">
  <div class="top-box">
    <span class="title">Adicionar novo produto</span>
    <i class="bi bi-x-lg" onclick="fecharProdutoForm()" style="cursor: pointer;"></i>
  </div>

  <div class="input-box">
    <label for="nome_produto">Nome do produto</label>
    <input type="text" name="nome_produto" id="nome_produto" class="input" required />
  </div>

  <div class="input-box">
    <label for="marca">Marca</label>
    <input type="text" name="marca" id="marca" class="input" required />
  </div>

  <div class="input-box">
    <label for="categoria">Categoria</label>
    <select name="categoria" id="categoria" required>
      <option value="" disabled selected>Selecione uma categoria</option>
      <option value="Informática">Informática</option>
      <option value="Eletrônicos">Eletrônicos</option>
      <option value="Eletrodomésticos">Eletrodomésticos</option>
      <option value="Cozinha">Cozinha</option>
      <option value="Casa">Casa</option>
      <option value="Escritório">Escritório</option>
      <option value="Acessórios">Acessórios</option>
    </select>
  </div>

  <div class="input-box price-box">
    <label for="preco">Preço</label>
    <div class="box">
      <p>R$</p>
      <input type="number" name="preco" id="preco" step="0.01" class="input" required />
    </div>
  </div>

  <div class="input-box">
    <label for="estoque">Quantidade em estoque</label>
    <input type="number" name="estoque" id="estoque" class="input" required />
  </div>

  <button type="submit" class="add">Adicionar</button>
</form>



  <!-- Novo modal para editar produto -->
  <form class="ad-prod" id="editar-produto-form">
  <div class="top-box">
    <span class="title">Editar produto</span>
    <i class="bi bi-x-lg" id="fechar-edit" style="cursor:pointer;"></i>
  </div>

  <div class="input-box">
    <label for="editar_nome_produto">Nome do produto</label>
    <input type="text" id="editar_nome_produto" class="input" required />
  </div>

  <div class="input-box">
    <label for="editar_marca">Marca</label>
    <input type="text" id="editar_marca" class="input" required />
  </div>

  <div class="input-box">
    <label for="editar_categoria">Categoria</label>
    <select id="editar_categoria" required>
      <option value="" disabled>Selecione uma categoria</option>
      <option value="Informática">Informática</option>
      <option value="Eletrônicos">Eletrônicos</option>
      <option value="Eletrodomésticos">Eletrodomésticos</option>
      <option value="Cozinha">Cozinha</option>
      <option value="Casa">Casa</option>
      <option value="Escritório">Escritório</option>
      <option value="Acessórios">Acessórios</option>
    </select>
  </div>

  <div class="input-box price-box">
    <label for="editar_preco">Preço</label>
    <div class="box">
      <p>R$</p>
      <input type="number" id="editar_preco" step="0.01" class="input" required />
    </div>
  </div>

  <div class="input-box">
    <label for="editar_estoque">Quantidade em estoque</label>
    <input type="number" id="editar_estoque" class="input" required />
  </div>

  <button type="button" id="btn-salvar-produto" class="add" onclick="salvarEdicaoProduto()">Salvar</button>
</form>

  <div class="top-box">
    <h2>Gerenciamento de produtos</h2>
    <button onclick="abrirProdutoForm()">
      <i class="bi bi-plus-circle"></i>Novo produto
    </button>
  </div>
  <div class="content">
    <span class="search-user">
      <i class="bi bi-search"></i>
      <input type="search" />
    </span>
    <table class="table table-dark">
      <thead>
        <tr>
          <th scope="col">ID</th>
          <th scope="col">Nome do Produto</th>
          <th scope="col">Marca</th>
          <th scope="col">Categoria</th>
          <th scope="col">Preço</th>
          <th scope="col">Estoque</th>
          <th scope="col">Última Atualização</th>
          <th scope="col">Ações</th>
        </tr>
      </thead>
      <tbody id="produtos-table-body" class="table-group-divider">

      </tbody>
    </table>
  </div>
</div>

      <div id="reports" class="section">
  <div class="top-box">
    <h2>Relatórios</h2>
    <div class="buttons">
      <button><i class="bi bi-printer"></i><span>Imprimir</span></button>
      <button><i class="bi bi-download"></i><span>Exportar</span></button>
    </div>
  </div>

  <div class="filters">
    <h4>Filtros do relatório</h4>
    <div class="filtr">
      <span class="filter-inputs">
        <label for="relType">Tipos de relatório</label>
        <select name="relType" id="relType" class="select">
          <option value="">Selecione...</option>
          <!-- As opções vão ser inseridas dinamicamente via JS -->
        </select>
      </span>
      <span class="filter-inputs">
        <label for="dataInicial">Data inicial</label>
        <input type="date" id="dataInicial" class="input" />
      </span>
      <span class="filter-inputs">
        <label for="dataFinal">Data final</label>
        <input type="date" id="dataFinal" class="input" />
      </span>
      <button id="btnFiltrar" class="ad-prod">
        <i class="bi bi-funnel"></i><span>Filtrar</span>
      </button>

      <!-- Botão Limpar Filtro -->
      <button id="btnLimparFiltro" class="ad-prod" style="margin-left:10px; background-color:#777;">
        <i class="bi bi-x-lg"></i><span>Limpar filtro</span>
      </button>
    </div>
  </div>

  <div class="func-reports-box">
    <h5>Relatórios recentes</h5>
    <table class="table table-dark">
      <thead>
        <tr>
          <th scope="col">Data</th>
          <th scope="col">Nome do relatório</th>
          <th scope="col">Tipo</th>
          <th scope="col">Gerado por</th>
          <th scope="col">Ações</th>
        </tr>
      </thead>
      <tbody class="table-group-divider" id="relatorios-body">
        <!-- As linhas dos relatórios serão inseridas dinamicamente aqui -->
      </tbody>
    </table>
  </div>
</div>

<!-- Modal para visualizar relatório -->
<div id="modal-relatorio" style="display:none; position:fixed; top:10%; left:50%; transform: translateX(-50%); width: 80%; max-height: 70vh; overflow-y: auto; background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.5); z-index: 1000;">
  <button id="btn-fechar-modal" style="float: right; background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
  <h3 id="modal-titulo"></h3>
  <div id="modal-conteudo" style="margin-top: 20px;">
    <!-- Aqui vai o conteúdo do relatório -->
  </div>
</div>
<div id="modal-overlay" style="display:none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999;"></div>





      <section id="analitycs" class="section">
        <div class="top-box">
          <h2>Análises</h2>
          <div class="filter">
            <input type="date" />
            <input type="date" />
            <button><i class="bi bi-funnel"></i><span>Filtrar</span></button>
            <button><i class="bi bi-download"></i><span>Exportar</span></button>
          </div>
        </div>
        <div class="top-infos">
          <div class="info-box">
            <span class="title">Valor em Estoque</span
            ><span class="value">R$ 128.450,00</span
            ><span class="mov"
              ><i class="bi bi-graph-up-arrow"></i>+5,2% em relação ao mês
              anterior</span
            >
          </div>
          <div class="info-box">
            <span class="title">Vendas Mensais</span
            ><span class="value">R$ 45.800,00</span
            ><span class="mov"
              ><i class="bi bi-graph-up-arrow"></i>+12,8% em relação ao mês
              anterior</span
            >
          </div>
          <div class="info-box">
            <span class="title">Giro de Estoque</span
            ><span class="value">3,2x</span
            ><span class="mov negative"
              ><i class="bi bi-graph-down-arrow"></i>-0,5x em relação ao mês
              anterior</span
            >
          </div>
          <div class="info-box">
            <span class="title">Margem de Lucro</span
            ><span class="value">32,5%</span
            ><span class="mov"
              ><i class="bi bi-graph-up-arrow"></i>+1,3% em relação ao mês
              anterior</span
            >
          </div>
        </div>
        <div class="radio-inputs">
          <label class="radio">
            <input checked="" name="auth" type="radio" />
            <span class="radio-item">Análise de Vendas</span>
          </label>
          <label class="radio">
            <input name="auth" type="radio" />
            <span class="radio-item">Análise de Estoque</span>
          </label>
          <label class="radio">
            <input name="auth" type="radio" />
            <span class="radio-item">Previsões</span>
          </label>
        </div>
        <div class="content">
          <div class="left-box">
            <div class="title">
              <h3>Tendência de Vendas</h3>
              <p>Últimos 6 meses</p>
            </div>
            <div class="infos">
              <div style="width: 600px; margin: 40px auto">
                <canvas id="linhaVendasChart"></canvas>
              </div>
            </div>
          </div>
          <div class="right-box">
            <div class="title">
              <h3>Produtos Mais Vendidos</h3>
              <p>Top 5 produtos por quantidade</p>
            </div>
            <div class="infos">
              <canvas id="topProdutosChart"></canvas>
            </div>
          </div>
        </div>
      </section>
      <div id="users" class="section">
        <div class="form-shadow"></div>
        <form
          action="../../PHP/cria_user.php"
          method="post"
          id="ad-func"
        >
          <div class="top-box">
            <span class="title">Adicionar novo usuário</span>
            <i class="bi bi-x-lg" onclick="hideForm()"></i>
          </div>
          <p>
            Preencha os detalhes do funcionário para adicioná-lo ao sistema.
          </p>
          <div class="input-box">
            <label for="name">Nome</label>
            <input type="text" name="nome" class="input" />
          </div>
          <div class="input-box">
            <label for="email">Email</label>
            <input type="email" name="email" class="input" />
          </div>
          <div class="input-box">
            <label for="func">Função</label>
            <select name="cargo" id="func">
              <option value="#" disabled selected>Selecione uma função</option>
              <option value="GERENTE">Gerente</option>
              <option value="SUPERVISOR">Supervisor</option>
              <option value="VENDEDOR">Vendedor</option>
            </select>
          </div>
          <div class="input-box">
            <label for="password">Senha</label>
            <input type="password" name="senha" class="input" />
          </div>
          <button type="submit" class="add">Adicionar</button>
        </form>
        <div class="top-box">
          <h2>Gerenciamento de usuários</h2>
          <button onclick="showForm()">
            <i class="bi bi-plus-circle"></i>Novo funcionário
          </button>
        </div>
        <div class="content">
          <span class="search-user">
            <i class="bi bi-search"></i>
            <input type="search" />
          </span>
          <table class="table table-dark">
            <thead>
              <tr>
                <th scope="col">id</th>
                <th scope="col">Nome</th>
                <th scope="col">Email</th>
                <th scope="col">Cargo</th>
                <th scope="col">Status</th>
                <th scope="col">Último acesso</th>
                <th scope="col">Ações</th>
              </tr>
            </thead>
            <tbody id="usuarios-table-body" class="table-group-divider">
              
            </tbody>
          </table>
        </div>
      </div>
      <div id="config" class="section">
        <h3>Configurações</h3>
        <p>Gerencie suas preferências e configurações do sistema.</p>
        <h5>- Perfil</h5>
        <p>Gerencie suas informações pessoais e preferências de conta.</p>
        <div class="profile-input">
          <label for="name">Nome</label>
          <div class="box">
            <input type="text" name="name" placeholder="Mark" />
            <button>
              <span>Editar</span><i class="bi bi-chevron-right"></i>
            </button>
          </div>
        </div>
        <div class="profile-input">
          <label for="email">Email</label>
          <div class="box">
            <input type="email" name="email" placeholder="mark@inveneer.com" />
            <button>
              <span>Editar</span><i class="bi bi-chevron-right"></i>
            </button>
          </div>
        </div>
        <div class="profile-input">
          <label for="lang">Idioma</label>
          <div class="box">
            <input type="text" name="lang" placeholder="Português (Brasil)" />
            <button>
              <span>Editar</span><i class="bi bi-chevron-right"></i>
            </button>
          </div>
        </div>
        <h5>- Notificações</h5>
        <p>Configure como você deseja receber notificações do sistema.</p>
        <div class="checkbox">
          <div class="text-box">
            <h6>Notificações por email</h6>
            <p>Receba atualizações importantes por email.</p>
          </div>
          <label class="switch">
            <input type="checkbox" />
            <span class="slider"></span>
          </label>
        </div>
        <div class="checkbox">
          <div class="text-box">
            <h6>Alertas de estoque baixo</h6>
            <p>Seja notificado quando produtos estiverem com estoque baixo.</p>
          </div>
          <label class="switch">
            <input type="checkbox" />
            <span class="slider"></span>
          </label>
        </div>
        <div class="checkbox">
          <div class="text-box">
            <h6>Notificações de pedidos</h6>
            <p>Receba notificações sobre novos pedidos e atualizações.</p>
          </div>
          <label class="switch">
            <input type="checkbox" />
            <span class="slider"></span>
          </label>
        </div>
      </div>
    </main>
    <div id="overlay-loading">
    <div class="loader"></div>
    </div>

    </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/bootstrap-table@1.21.2/dist/bootstrap-table.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </body>
  <script src="script.js"></script>
  <script>
  // Chamada automática após o carregamento da página
  document.addEventListener('DOMContentLoaded', function () {
    carregarUsuarios();
    carregarProdutos();
  });
</script>
</html>
