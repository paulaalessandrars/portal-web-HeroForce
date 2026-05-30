# ⚡ HeroForce — Portal de Projetos Heroicos

> Desafio Técnico Fullstack — Laravel + Vue.js + PostgreSQL

Sistema de gestão e vendas de projetos heroicos para a empresa fictícia **HeroForce**, onde heróis se cadastram, escolhem seu personagem e gerenciam missões com base em metas de **Agilidade, Encantamento, Eficiência, Excelência, Transparência e Ambição**.

**Personagem do dev:** 🔮 Scarlet Witch — criatividade, poder e capacidade de reescrever a realidade.

---

## 🚀 Deploy

| Serviço  | URL |
|----------|-----|
| **Frontend** (Vercel) | [portal-web-hero-force.vercel.app](https://portal-web-hero-force.vercel.app) |
| **Backend API + Swagger** (Railway) | [portal-web-heroforce-production.up.railway.app/api-docs](https://portal-web-heroforce-production.up.railway.app/api-docs/) |

> Credenciais de demo disponíveis na tela de login.

---

## Stack

| Camada    | Tecnologia                              |
|-----------|-----------------------------------------|
| Backend   | PHP 8.2 + Laravel 11 + Eloquent ORM     |
| Auth      | JWT (tymon/jwt-auth 2.x)                |
| Banco     | PostgreSQL 16                           |
| Frontend  | Vue.js 3 + Vite + Pinia + Vue Router    |
| Estilo    | Tailwind CSS 3                          |
| Docker    | docker-compose (API + Frontend + DB)    |
| CI        | GitHub Actions                          |

---

## Execução local (sem Docker)

### Pré-requisitos
- PHP 8.2+ com extensão `pdo_pgsql`
- Composer 2.x
- Node.js 18+ e npm
- PostgreSQL rodando localmente

### Backend

```bash
cd backend

# 1. Instalar dependências
composer install

# 2. Copiar e ajustar o .env
cp .env.example .env
# Edite DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD conforme seu PostgreSQL local

# 3. Gerar chaves
php artisan key:generate
php artisan jwt:secret

# 4. Criar banco e rodar migrations + seed
createdb heroforce   # ou crie pelo pgAdmin/DBeaver
php artisan migrate --seed

# 5. Iniciar servidor
php artisan serve
# API disponível em http://localhost:8000
```

### Frontend

```bash
cd frontend

# 1. Instalar dependências
npm install

# 2. Configurar URL da API (opcional — padrão já aponta para localhost:8000)
cp .env.example .env

# 3. Iniciar
npm run dev
# Frontend disponível em http://localhost:5173
```

---

## Execução com Docker

```bash
# Na raiz do projeto
docker-compose up --build

# Na primeira execução, o backend irá:
# - instalar dependências
# - gerar as chaves
# - rodar migrations e seed automaticamente

# Serviços disponíveis:
# API:      http://localhost:8000
# Frontend: http://localhost:5173
# DB:       localhost:5432
```

---

## Credenciais de demonstração

| Papel | E-mail | Senha |
|-------|--------|-------|
| Admin (Iron Man) | `admin@heroforce.com` | `password` |
| Herói (Spider-Man) | `peter@heroforce.com` | `password` |
| Herói (Wonder Woman) | `diana@heroforce.com` | `password` |
| Herói (Batman) | `bruce@heroforce.com` | `password` |

> Apenas o **admin** pode criar, editar e excluir projetos.

---

## Endpoints da API

| URL Base | Uso |
|----------|-----|
| `http://localhost:8000/api` | Infraestrutura (health check) |
| `http://localhost:8000/api/v1` | Todos os endpoints de negócio |

| Método | Endpoint | Descrição | Auth |
|--------|----------|-----------|------|
| GET | `/api/health` | Status da API e banco | ❌ |
| POST | `/api/v1/auth/register` | Cadastro de herói | ❌ |
| POST | `/api/v1/auth/login` | Login (máx. 5 tentativas/min) | ❌ |
| GET | `/api/v1/auth/me` | Usuário autenticado | ✅ |
| POST | `/api/v1/auth/logout` | Logout | ✅ |
| POST | `/api/v1/auth/refresh` | Renovar token JWT | ✅ |
| GET | `/api/v1/projects` | Listar projetos (paginado) | ✅ |
| POST | `/api/v1/projects` | Criar projeto | ✅ Admin |
| GET | `/api/v1/projects/{id}` | Detalhe do projeto | ✅ |
| PUT | `/api/v1/projects/{id}` | Atualizar projeto | ✅ Admin |
| PATCH | `/api/v1/projects/{id}/status` | Atualizar status | ✅ |
| DELETE | `/api/v1/projects/{id}` | Excluir (soft delete) | ✅ Admin |
| GET | `/api/v1/users` | Listar heróis | ✅ |
| GET | `/api/v1/users/{id}` | Detalhe do herói | ✅ |

### Filtros disponíveis em `GET /api/v1/projects`
- `?status=pendente` | `em andamento` | `concluído`
- `?user_id=1`
- `?search=nome` — busca por nome (case-insensitive)
- `?per_page=12` — itens por página (padrão 12, máx. 50)

---

## Testes automatizados

```bash
cd backend
php artisan test
```

Os testes cobrem:
- **AuthTest** — registro (campos obrigatórios, e-mail duplicado, senhas divergentes), login (credenciais inválidas), rota `/me`, logout
- **ProjectTest** — listagem com e sem filtros, criação/edição/exclusão por admin, bloqueio de heróis em operações restritas, relacionamento com usuário, soft delete, 404 para projetos inexistentes

> Os testes rodam com SQLite em memória (configurado no `phpunit.xml`) — nenhuma configuração extra necessária.

---

## Documentação OpenAPI (Swagger UI)

Com o backend rodando, acesse:

```
http://localhost:8000/api-docs/
```

O Swagger UI está integrado ao projeto — é possível visualizar e testar todos os endpoints diretamente pelo navegador, incluindo autenticação via token JWT.

> **Como usar:** chame `POST /auth/login` para obter o token → clique em **Authorize** → cole o token → todos os endpoints protegidos ficam disponíveis.

O arquivo de especificação também está disponível em `backend/public/api-docs/openapi.yaml`.

---

## Estrutura do projeto

```
portal-web-HeroForce/
├── .github/
│   └── workflows/
│       └── ci.yml             # CI: testes + build em todo push
├── backend/               # Laravel API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── ProjectController.php
│   │   │   │   └── UserController.php
│   │   │   ├── Requests/
│   │   │   │   ├── StoreProjectRequest.php
│   │   │   │   └── UpdateProjectRequest.php
│   │   │   └── Resources/
│   │   │       ├── ProjectResource.php
│   │   │       └── UserResource.php
│   │   ├── Models/
│   │   │   ├── User.php
│   │   │   └── Project.php    # SoftDeletes habilitado
│   │   ├── Policies/
│   │   │   └── ProjectPolicy.php  # Autorização centralizada
│   │   └── Services/
│   │       └── ProjectService.php # Lógica de negócio + cache + logging
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/api.php
│   └── Dockerfile
├── frontend/              # Vue.js SPA
│   ├── src/
│   │   ├── api/axios.js
│   │   ├── stores/auth.js
│   │   ├── router/index.js
│   │   ├── constants/characters.js   # Fonte única de verdade dos personagens
│   │   ├── composables/
│   │   │   └── useGoalColor.js       # Lógica de cor das metas reutilizável
│   │   ├── views/
│   │   │   ├── LoginView.vue
│   │   │   ├── RegisterView.vue
│   │   │   ├── DashboardView.vue
│   │   │   └── ProjectFormView.vue
│   │   └── components/
│   │       ├── HeroAvatar.vue    # Avatares reais dos personagens
│   │       ├── NavBar.vue
│   │       └── ProjectCard.vue
│   └── Dockerfile
├── docker-compose.yml
└── README.md
```

---

## Funcionalidades

### Para todos os heróis
- Cadastro com nome, e-mail, senha e escolha de personagem (Marvel, DC e outros)
- Login com JWT — token armazenado em `localStorage`
- Dashboard com lista de todos os projetos, filtros por status e herói
- Visualização das 6 metas de cada projeto com barra de progresso
- Contador de missões por status (pendente, em andamento, concluídas)

### Exclusivo para admin
- Criar novas missões com todas as metas configuráveis via slider (0–100)
- Editar e excluir projetos existentes
- Atribuir projetos a qualquer herói cadastrado

---

## Modelo de dados

### User
| Campo | Tipo | Descrição |
|-------|------|-----------|
| name | string | Nome do herói |
| email | string | E-mail único |
| character | string | Personagem escolhido |
| role | enum | `admin` ou `hero` |

### Project
| Campo | Tipo | Descrição |
|-------|------|-----------|
| name | string | Nome da missão |
| description | text | Descrição |
| status | enum | `pendente`, `em andamento`, `concluído` |
| user_id | FK | Herói responsável |
| goal_agility | int 0-100 | Meta: Agilidade |
| goal_enchantment | int 0-100 | Meta: Encantamento |
| goal_efficiency | int 0-100 | Meta: Eficiência |
| goal_excellence | int 0-100 | Meta: Excelência |
| goal_transparency | int 0-100 | Meta: Transparência |
| goal_ambition | int 0-100 | Meta: Ambição |
| deleted_at | timestamp | Soft delete — registro nunca é apagado fisicamente |
