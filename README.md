# Sistema de Gerenciamento de Criatório

## 🚀 Proposta do Sistema

Este sistema web tem como objetivo principal otimizar a gestão completa de um criatório, abrangendo desde o manejo detalhado das aves até o controle financeiro rigoroso das operações. Desenvolvido para oferecer uma interface intuitiva e funcionalidades robustas, ele visa centralizar informações, automatizar processos e fornecer insights valiosos para a tomada de decisões, garantindo a eficiência e a rentabilidade do negócio.

A plataforma busca simplificar o dia a dia do criador, permitindo um acompanhamento preciso de cada ave, controle de ciclos de reprodução, gestão de estoque de ovos e pintinhos, além de uma visão clara da saúde financeira do criatório.

## 📦 Módulos Principais

O sistema é estruturado em módulos interconectados para cobrir todas as áreas essenciais do gerenciamento de um criatório:

### 🐔 Módulo de Manejo de Aves

Dedica-se ao controle e acompanhamento individual e coletivo das aves, seus tipos, variações e ciclos de vida.

- **Aves:** Cadastro e gerenciamento detalhado de cada ave (identificação, status, histórico, etc.).
- **Tipos de Aves:** Definição e organização dos diferentes tipos de aves presentes no criatório.
- **Variações:** Registro de variações genéticas ou de plumagem dentro dos tipos de aves.
- **Lotes:** Agrupamento de aves para manejo simplificado (ex: lotes de reprodução, lotes de engorda).
- **Acasalamentos:** Registro e acompanhamento dos pares de acasalamento, com datas e resultados esperados.
- **Postura de Ovos:** Registro diário da produção de ovos por ave ou lote.
- **Incubação:** Monitoramento do processo de incubação, desde a entrada dos ovos até a eclosão, com controle de chocadeiras e taxas.
- **Mortes:** Registro de óbitos, com data, causa e observações para análise de mortalidade.

### 💰 Módulo Financeiro

Responsável por todas as transações financeiras, relatórios e a saúde econômica do criatório.

- **Dashboard Financeiro:** Visão geral consolidada das finanças, com gráficos de receitas, despesas e saldo.
- **Receitas:** Registro de todas as entradas de dinheiro (vendas, subsídios, etc.).
- **Despesas:** Registro de todos os gastos (alimentação, medicamentos, manutenção, salários, etc.).
- **Vendas:** Gerenciamento das vendas de aves, ovos ou outros produtos, incluindo comissões.
- **Reservas:** Controle de reservas futuras, com datas e valores.
- **Transações Recorrentes:** Cadastro de receitas e despesas que se repetem periodicamente.
- **Categorias Financeiras:** Organização das receitas e despesas em categorias personalizadas para melhor análise.
- **Contracheque:** Geração e acompanhamento dos lançamentos de contracheque para funcionários ou colaboradores.
- **Relatórios Financeiros:** Geração de relatórios detalhados por categoria, período e tipo de transação.

## ⚙️ Rotinas e Funcionalidades Possíveis

Para cada módulo, as seguintes rotinas e funcionalidades são previstas:

### Rotinas de Manejo de Aves

- **CRUD Completo:**
    - **Criação:** Formulários intuitivos para adicionar novas aves, tipos, variações, lotes, acasalamentos, posturas e incubadoras.
    - **Leitura (Listagem e Detalhes):** Telas de listagem com filtros e busca, e páginas de detalhes para cada registro com informações abrangentes.
    - **Atualização:** Formulários para editar informações existentes de qualquer registro.
    - **Exclusão:** Funcionalidade para remover registros, com confirmação para evitar perdas acidentais.
- **Acompanhamento de Ciclos:** Visualização do progresso de incubação (dias restantes, porcentagem de conclusão).
- **Alertas:** Notificações sobre incubadoras atrasadas, aves inativas, etc.
- **Gráficos de Desempenho:**
    - Distribuição de aves por tipo (gráfico de pizza).
    - Tendência de mortes ao longo do tempo (gráfico de linha).
    - Histórico de eclosões por tipo de ave (gráfico de barras).
    - Ovos postos diariamente (gráfico de linha).

### Rotinas Financeiras

- **CRUD Completo:** Para Receitas, Despesas, Vendas, Reservas, Transações Recorrentes e Categorias Financeiras.
- **Filtros por Período:** Capacidade de filtrar dados financeiros por mês e ano.
- **Cálculo de Saldo:** Saldo total e saldo mensal automatizados.
- **Gestão de Comissões:** Acompanhamento de comissões pagas e a pagar.
- **Detalhamento de Contracheque:** Listagem de proventos e descontos, com cálculo de valor bruto e líquido.
- **Gráficos Financeiros:**
    - Comparativo de Receitas vs. Despesas por mês (gráfico de barras).
    - Evolução do saldo acumulado (gráfico de linha).
    - Distribuição de despesas por categoria (gráfico de pizza).

## 🛠️ Tecnologias Utilizadas

- **Backend:** PHP 8.x, Laravel Framework 10.x
- **Banco de Dados:** MySQL (ou MariaDB)
- **Frontend:** HTML5, CSS3, JavaScript
- **Framework CSS/JS:** AdminLTE 3 (com Bootstrap 4)
- **Bibliotecas JS:** jQuery, Chart.js, Moment.js, SweetAlert2, Select2, JQVMap, Sparkline, jQuery Knob.
- **Controle de Versão:** Git

## ⚙️ Como Instalar e Configurar (Futuramente)

1.  **Clonar o Repositório:**
    ```bash
    git clone [https://github.com/corneliopj/laravel.git](https://github.com/corneliopj/laravel.git) # Substitua pelo seu link real
    cd seu-projeto-laravel
    ```
2.  **Instalar Dependências:**
    ```bash
    composer install
    npm install # Se você tiver dependências de Node.js (ex: para compilar assets)
    ```
3.  **Configurar Variáveis de Ambiente:**
    - Copie o arquivo `.env.example` para `.env`:
        ```bash
        cp .env.example .env
        ```
    - Edite o arquivo `.env` com suas credenciais de banco de dados e outras configurações.
4.  **Gerar Chave da Aplicação:**
    ```bash
    php artisan key:generate
    ```
5.  **Executar Migrações do Banco de Dados:**
    ```bash
    php artisan migrate
    ```
6.  **Opcional: Seed de Dados (para dados de teste):**
    ```bash
    php artisan db:seed
    ```
7.  **Compilar Assets (se necessário):**
    ```bash
    npm run dev # ou npm run prod
    ```
8.  **Iniciar o Servidor de Desenvolvimento:**
    ```bash
    php artisan serve
    ```
    - Acesse o sistema em `http://127.0.0.1:8000` (ou o endereço que o comando indicar).

## 🤝 Como Contribuir

(Esta seção será preenchida com diretrizes de contribuição, como "fork the repository", "create a new branch", "submit pull requests", etc., quando o projeto estiver mais maduro para colaboração externa.)

## 📄 Licença

(Esta seção será preenchida com a licença do seu projeto, por exemplo, MIT, GPL, etc.)
