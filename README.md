# ⚡ HeroForce — Portal de Projetos Heroicos

> Desafio Técnico Fullstack — Laravel + Vue.js + PostgreSQL

Sistema de gestão e vendas de projetos heroicos para a empresa fictícia **HeroForce**, onde heróis se cadastram, escolhem seu personagem e gerenciam missões com base em metas de **Agilidade, Encantamento, Eficiência, Excelência, Transparência e Ambição**.

**Personagem do dev:** 🤖 Iron Man — tecnologia de ponta, inovação e autonomia.

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

Base URL: `http://localhost:8000/api`

| Método | Endpoint | Descrição | Auth |
|--------|----------|-----------|------|
| POST | `/auth/register` | Cadastro de herói | ❌ |
| POST | `/auth/login` | Login | ❌ |
| GET | `/auth/me` | Usuário autenticado | ✅ |
| POST | `/auth/logout` | Logout | ✅ |
| POST | `/auth/refresh` | Renovar token | ✅ |
| GET | `/projects` | Listar projetos | ✅ |
| POST | `/projects` | Criar projeto | ✅ Admin |
| GET | `/projects/{id}` | Detalhe | ✅ |
| PUT | `/projects/{id}` | Atualizar | ✅ Admin |
| DELETE | `/projects/{id}` | Excluir | ✅ Admin |
| GET | `/users` | Listar heróis | ✅ |
| GET | `/users/{id}` | Detalhe herói | ✅ |

### Filtros disponíveis em `GET /projects`
- `?status=pendente` | `em andamento` | `concluído`
- `?user_id=1`

### Documentação OpenAPI
O arquivo de especificação está em `backend/storage/api-docs/api-docs.yaml`.

Para visualizar, importe no [Swagger Editor](https://editor.swagger.io/) ou use a extensão **REST Client** do VS Code.

---

## Estrutura do projeto

```
portal-web-HeroForce/
├── backend/               # Laravel API
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   │   ├── AuthController.php
│   │   │   ├── ProjectController.php
│   │   │   └── UserController.php
│   │   └── Models/
│   │       ├── User.php
│   │       └── Project.php
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/api.php
│   ├── storage/api-docs/api-docs.yaml
│   └── Dockerfile
├── frontend/              # Vue.js SPA
│   ├── src/
│   │   ├── api/axios.js
│   │   ├── stores/auth.js
│   │   ├── router/index.js
│   │   ├── views/
│   │   │   ├── LoginView.vue
│   │   │   ├── RegisterView.vue
│   │   │   ├── DashboardView.vue
│   │   │   └── ProjectFormView.vue
│   │   └── components/
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
