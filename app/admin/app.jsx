import React, { useState } from 'react';

function App() {
  const [secaoAtiva, setSecaoAtiva] = useState('dashboard');

  const secoes = {
    dashboard: <h2>Dashboard</h2>,
    estoque: <h2>Estoque</h2>,
    categorias: <h2>Categorias</h2>,
    relatorios: <h2>Relatórios</h2>,
    analises: <h2>Análises</h2>,
    usuarios: <h2>Usuários</h2>,
    configuracoes: <h2>Configurações</h2>
  };

  return (
    <div style={{ display: 'flex' }}>
      <aside>
        <div className="logo">
          <p>Inve<span>neer</span></p>
        </div>
        <ul>
          <li
            className={secaoAtiva === 'dashboard' ? 'active' : ''}
            onClick={() => setSecaoAtiva('dashboard')}
          >
            <i className="bi bi-grid"></i> <span>Dashboard</span>
          </li>
          <li
            className={secaoAtiva === 'estoque' ? 'active' : ''}
            onClick={() => setSecaoAtiva('estoque')}
          >
            <i className="bi bi-box-seam"></i> <span>Estoque</span>
          </li>
          <li
            className={secaoAtiva === 'categorias' ? 'active' : ''}
            onClick={() => setSecaoAtiva('categorias')}
          >
            <i className="bi bi-boxes"></i> <span>Categorias</span>
          </li>
          <li
            className={secaoAtiva === 'relatorios' ? 'active' : ''}
            onClick={() => setSecaoAtiva('relatorios')}
          >
            <i className="bi bi-clipboard-data"></i> <span>Relatórios</span>
          </li>
          <li
            className={secaoAtiva === 'analises' ? 'active' : ''}
            onClick={() => setSecaoAtiva('analises')}
          >
            <i className="bi bi-bar-chart-line"></i> <span>Análises</span>
          </li>
          <li
            className={secaoAtiva === 'usuarios' ? 'active' : ''}
            onClick={() => setSecaoAtiva('usuarios')}
          >
            <i className="bi bi-people"></i> <span>Usuários</span>
          </li>
          <li
            className={secaoAtiva === 'configuracoes' ? 'active' : ''}
            onClick={() => setSecaoAtiva('configuracoes')}
          >
            <i className="bi bi-gear"></i> <span>Configurações</span>
          </li>
          <li>
            <div className="img-user"></div>
            <span className="user-infos">
              <span className="name">Name</span>
              <span className="privilege">Administrador</span>
            </span>
          </li>
        </ul>
      </aside>

      <main style={{ padding: '20px', flex: 1 }}>
        {secoes[secaoAtiva]}
      </main>
    </div>
  );
}

export default App;