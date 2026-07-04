# E-commerce API — Laravel

API RESTful para um sistema de e-commerce, desenvolvida em Laravel como parte de um teste técnico.

## 🎯 Objetivo

Testar conhecimento em APIs RESTful e boas práticas de desenvolvimento: endpoints para produtos e pedidos, com validação de entrada, tratamento de erros, autenticação e logs.

## 🛠️ Stack utilizada

- **Framework:** Laravel 11
- **Banco de dados:** MySQL
- **Autenticação:** Laravel Sanctum (tokens, adequado para API consumida por SPA/mobile)
- **Ambiente local:** Laragon (PHP, MySQL, Composer)

## 📐 Modelagem de dados

O banco segue a estrutura definida em `database/tabelas_criadas.sql`:

| Tabela | Descrição |
|---|---|
| `clientes` | Clientes cadastrados (também usados como entidade de autenticação) |
| `categorias` | Categorias de produtos |
| `produtos` | Produtos do catálogo |
| `produtos_categorias` | Tabela pivot (relação N:N entre produtos e categorias) |
| `pedidos` | Pedidos realizados pelos clientes |
| `itens_pedido` | Itens de cada pedido (chave composta `id_pedido` + `id_produto`) |
| `pagamentos` | Pagamentos associados a pedidos |

As tabelas usam nomenclatura em português e chaves primárias customizadas (ex: `id_produto` em vez de `id`). Os Models Eloquent foram configurados para respeitar essa estrutura (`$table`, `$primaryKey`, `$timestamps = false`).

### Relacionamentos implementados

- `Cliente` `hasMany` `Pedido`
- `Pedido` `belongsTo` `Cliente`, `hasMany` `ItemPedido`, `hasMany` `Pagamento`
- `Produto` `belongsToMany` `Categoria` (via `produtos_categorias`)
- `ItemPedido` `belongsTo` `Pedido` e `belongsTo` `Produto`

## 🚀 Setup do projeto

1. Clone o repositório:
   ```bash
   git clone https://github.com/Paulopiazentin/ecommerce-api-laravel.git
   cd ecommerce-api-laravel
   ```
2. Instale as dependências:
   ```bash
   composer install
   ```
3. Copie o arquivo de ambiente:
   ```bash
   cp .env.example .env
   ```
   Edite `.env` com suas credenciais MySQL:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ecommerce
   DB_USERNAME=root
   DB_PASSWORD=
   ```
4. Gere a chave da aplicação:
   ```bash
   php artisan key:generate
   ```
5. Rode as migrations (cria o banco e todas as tabelas, incluindo as do Sanctum):
   ```bash
   php artisan migrate
   ```
6. Suba o servidor:
   ```bash
   php artisan serve
   ```

A API estará disponível em `http://127.0.0.1:8000/api`.

> 💡 Todas as requisições devem enviar o header `Accept: application/json`, para que erros e respostas venham sempre em JSON.

## 📍 Endpoints implementados

### Autenticação

| Método | Rota | Protegida? | Descrição |
|---|---|---|---|
| POST | `/api/register` | Não | Cadastra um novo cliente e retorna um token |
| POST | `/api/login` | Não | Autentica um cliente e retorna um token |
| POST | `/api/logout` | Sim | Revoga o token atual |

**Exemplo — Registro:**
```json
POST /api/register
{
    "nome": "João Silva",
    "email": "joao@email.com",
    "password": "123456",
    "password_confirmation": "123456",
    "telefone": "11999998888"
}
```
Resposta (201):
```json
{
    "cliente": { "id_cliente": 2, "nome": "João Silva", "email": "joao@email.com" },
    "token": "3|pL7EKyR3j0CkbWB5n7XMJjU40RTAICJN7qlAp0Q4aebac961"
}
```

**Exemplo — Login:**
```json
POST /api/login
{
    "email": "joao@email.com",
    "password": "123456"
}
```

Rotas protegidas exigem o header:
```
Authorization: Bearer {token}
```

### Produtos

| Método | Rota | Protegida? | Descrição |
|---|---|---|---|
| GET | `/api/produtos` | Não | Lista produtos com filtros opcionais |

**Filtros disponíveis (query params):**
- `nome` — busca parcial pelo nome do produto
- `categoria_id` — filtra por categoria
- `preco_min` / `preco_max` — filtra por faixa de preço

**Exemplo:**
```
GET /api/produtos?nome=mouse&preco_min=50&preco_max=300
```

Resposta paginada (15 itens por página), incluindo categorias associadas a cada produto.

### Pedidos

| Método | Rota | Protegida? | Descrição |
|---|---|---|---|
| POST | `/api/pedidos` | Sim | Cria um novo pedido para o cliente autenticado |
| GET | `/api/pedidos` | Sim | Lista os pedidos do cliente autenticado |
| GET | `/api/pedidos/{id}` | Sim | Consulta um pedido específico (somente do próprio cliente) |
| PUT | `/api/pedidos/{id}` | Sim | Atualiza o status do pedido |

**Exemplo — Criar pedido:**
```json
POST /api/pedidos
Authorization: Bearer {token}

{
    "itens": [
        { "id_produto": 1, "quantidade": 2 }
    ]
}
```
- O `id_cliente` é obtido automaticamente do token, nunca do body.
- O `preco_unitario` e `valor_total` são calculados no servidor a partir do preço atual do produto — nunca confia no valor enviado pelo cliente.
- Toda a operação roda dentro de uma `DB::transaction`, com `lockForUpdate()` no produto, evitando venda de estoque duplicada em requisições simultâneas.
- Se o estoque for insuficiente, retorna 422 com mensagem explicativa.

**Exemplo — Atualizar status:**
```json
PUT /api/pedidos/1
Authorization: Bearer {token}

{
    "status": "cancelado"
}
```
- Status aceitos: `pendente`, `pago`, `enviado`, `entregue`, `cancelado`.
- Ao cancelar um pedido que não estava cancelado, o estoque dos produtos é devolvido automaticamente.

## ✅ Tratamento de erros

Erros são padronizados globalmente (`bootstrap/app.php`) e sempre retornam JSON:

| Situação | Status | Exemplo |
|---|---|---|
| Validação falhou | 422 | `{"message": "Os dados enviados são inválidos.", "errors": {...}}` |
| Não autenticado | 401 | `{"message": "Não autenticado."}` |
| Recurso não encontrado | 404 | `{"message": "Pedido não encontrado."}` |
| Rota inexistente | 404 | `{"message": "Rota não encontrada."}` |
| Regra de negócio violada (ex: estoque) | 422 | `{"message": "Estoque insuficiente para o produto 'Mouse Gamer'..."}` |
| Erro interno inesperado | 500 | `{"message": "Ocorreu um erro interno no servidor."}` |

## 📝 Logs

Operações importantes registram logs via `Log::info()`:
- Criação de pedido (`id_pedido`, `id_cliente`, `valor_total`)
- Atualização de status de pedido (status anterior e novo)
- Registro e login de cliente

Os logs ficam em `storage/logs/laravel.log`.

## 🔒 Segurança e boas práticas aplicadas

- Preço de produtos sempre lido do banco no momento da compra (nunca do request)
- `lockForUpdate()` + transactions para evitar condições de corrida no controle de estoque
- Senhas armazenadas com hash (`Hash::make`), nunca em texto plano, e ocultas nas respostas (`$hidden`)
- Autorização por dono do recurso: cada cliente só acessa os próprios pedidos
- Tokens de API via Sanctum, revogáveis individualmente (`logout`)

## 🧪 Como testar

Recomendado usar [Thunder Client](https://www.thunderclient.com/) (extensão do VS Code) ou [Postman](https://www.postman.com/) para testar endpoints `POST`/`PUT` e rotas autenticadas. Endpoints `GET` públicos podem ser testados direto no navegador.
