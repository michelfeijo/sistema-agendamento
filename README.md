# Sistema de Agendamentos

Aplicação web para gerenciamento de agendamentos de atendimentos, desenvolvida como estudo de caso para o processo seletivo **01873/2026 - Desenvolvedor Full Stack - Pleno (FIESC)**.

## Stack utilizada

- **Backend:** PHP 8.3 + Laravel 13
- **Autenticação:** Laravel Sanctum (tokens)
- **Banco de dados:** MySQL
- **Frontend:** HTML, CSS e JavaScript puro (consumindo a API via fetch)

## Funcionalidades

- Autenticação de usuários com dois perfis: **Administrador** e **Atendente**
- CRUD de usuários com regras de permissão por perfil
- Inativação de usuários (em vez de exclusão definitiva, preservando histórico)
- Cadastro de disponibilidade de horários por atendente e dia da semana
- Consulta de horários disponíveis (oculta horários já ocupados)
- Criação e cancelamento de agendamentos
- Validações de backend e frontend, com mensagens em português
- Proteção contra auto-promoção de perfil (um atendente não pode se tornar administrador)

## Pré-requisitos

- PHP 8.2 ou superior
- Composer
- MySQL

## Como rodar o projeto

### 1. Clonar o repositório

```bash
git clone https://github.com/michelfeijo/sistema-agendamento.git
cd sistema-agendamento
```

### 2. Instalar as dependências

```bash
composer install
```

### 3. Configurar o ambiente

Copie o arquivo de exemplo e gere a chave da aplicação:

```bash
cp .env.example .env
php artisan key:generate
```

Abra o `.env` e configure o acesso ao banco de dados:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agendamento
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Criar o banco de dados

```sql
CREATE DATABASE agendamento CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Rodar as migrations e popular o banco

```bash
php artisan migrate --seed
```

Isso vai criar as tabelas e inserir 3 usuários de teste:

| Perfil         | E-mail                     | Senha     |
|----------------|-----------------------------|-----------|
| Administrador  | admin@agendamento.com       | admin123  |
| Atendente      | joao@agendamento.com        | admin123  |
| Atendente      | maria@agendamento.com       | admin123  |

### 6. Subir o servidor

```bash
php artisan serve
```

### 7. Acessar a aplicação

Abra no navegador:

```
http://127.0.0.1:8000/app.html
```

## Estrutura do projeto

```
app/
  Http/Controllers/   → Lógica das regras de negócio (Auth, Usuários, Disponibilidades, Agendamentos)
  Models/              → Models Eloquent (User, Disponibilidade, Agendamento)
database/
  migrations/          → Estrutura das tabelas do banco
  seeders/             → Dados iniciais para teste
routes/
  api.php              → Rotas da API REST
public/
  app.html             → Frontend da aplicação (HTML, CSS e JS)
```

## Observações técnicas

- A API segue o padrão REST, retornando códigos HTTP apropriados (200, 201, 400, 401, 403, 404).
- A autenticação é feita via token (Laravel Sanctum), enviado no header `Authorization: Bearer {token}`.
- Regras de autorização são validadas tanto no frontend (UX) quanto no backend (segurança).