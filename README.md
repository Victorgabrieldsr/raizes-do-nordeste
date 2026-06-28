# Raízes do Nordeste - API REST

API REST desenvolvida em Laravel 11 para o sistema de pedidos da rede de lanchonetes **Raízes do Nordeste**, projeto multidisciplinar UNINTER 2026.

## Tecnologias Utilizadas

- PHP 8.3
- Laravel 11
- MySQL / MariaDB
- JWT (tymon/jwt-auth)
- Swagger (darkaonline/l5-swagger)

## Requisitos

- PHP 8.3+
- Composer
- MySQL ou MariaDB
- Laragon (recomendado)

## Instalação

### 1. Clone o repositório

```bash
git clone https://github.com/Victorgabrieldsr/raizes-do-nordeste.git
cd raizes-do-nordeste
```

### 2. Instale as dependências

```bash
composer install
```

### 3. Configure o ambiente

```bash
cp .env.example .env
```

Edite o arquivo `.env` com suas configurações de banco de dados:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=raizes_do_nordeste
DB_USERNAME=root
DB_PASSWORD=
SESSION_DRIVER=file
```

### 4. Gere a chave da aplicação e o secret JWT

```bash
php artisan key:generate
php artisan jwt:secret
```

### 5. Crie o banco de dados

No MySQL/MariaDB execute:

```sql
CREATE DATABASE raizes_do_nordeste;
```

### 6. Execute as migrations

```bash
php artisan migrate
```

### 7. Inicie o servidor

```bash
php artisan serve
```

A API estará disponível em: `http://127.0.0.1:8000`

## Endpoints Principais

| Método | Rota | Descrição |
|--------|------|-----------|
| POST | /api/auth/register | Cadastro de usuário |
| POST | /api/auth/login | Login |
| POST | /api/auth/logout | Logout |
| GET | /api/branches | Listar unidades |
| POST | /api/branches | Criar unidade |
| GET | /api/products | Listar produtos |
| POST | /api/products | Criar produto |
| GET | /api/inventories | Consultar estoque |
| POST | /api/inventories | Adicionar estoque |
| POST | /api/orders | Criar pedido |
| GET | /api/orders | Listar pedidos |
| PATCH | /api/orders/{id}/status | Atualizar status |
| POST | /api/payments/{orderId} | Processar pagamento mock |
| GET | /api/loyalty | Consultar pontos |

## Fluxo Principal

1. Registrar usuário → `POST /api/auth/register`
2. Fazer login → `POST /api/auth/login`
3. Criar unidade → `POST /api/branches`
4. Criar produto → `POST /api/products`
5. Adicionar estoque → `POST /api/inventories`
6. Criar pedido → `POST /api/orders`
7. Processar pagamento → `POST /api/payments/{orderId}`
8. Verificar status → `GET /api/orders/{id}`

## Testes

Importe o arquivo `raizes-do-nordeste-postman.json` no Postman para executar todos os cenários de teste.

## Segurança e LGPD

- Autenticação via JWT
- Senhas armazenadas com Hash bcrypt
- Consentimento LGPD obrigatório no cadastro
- Dados sensíveis não expostos nas respostas

## Autor

Victor Gabriel Dos Santos Rangel - RU 4819335
UNINTER - Projeto Multidisciplinar 2026