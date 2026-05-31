# PedeAi

Sistema de gestao digital de pedidos para restaurante/bar, com dashboard operacional, comandas, itens de cardapio, mesas, reservas, fila de atendimento e controle de usuarios por permissao.

## Stack

- PHP 8.3+
- Laravel 13
- MySQL
- Livewire 4
- Flux UI
- Tailwind CSS 4
- DaisyUI 5
- Vite
- Laravel Fortify

## Principais recursos

- Autenticacao, registro, recuperacao de senha, passkeys e 2FA via Fortify.
- Dashboard de tickets/comandas com indicadores reais do banco.
- Cadastro de itens da comanda/cardapio.
- Cadastro de mesas com status operacional.
- Cadastro e cancelamento de reservas.
- Abertura de comandas respeitando disponibilidade de mesas e reservas.
- Adicao de itens em comandas abertas.
- Alteracao de status da comanda.
- Pagamento de comanda com desconto, acrescimo/servico e forma de pagamento.
- Fila de atendimento/cozinha.
- Controle de usuarios e roles.
- Auditoria operacional de abertura, pagamento, alteracao de status e reservas.
- Tema claro/escuro/sistema via configuracoes de aparencia.

## Regras de negocio importantes

### Mesas

As mesas ficam em `restaurant_tables` e possuem:

- `identifier`: identificacao visivel da mesa.
- `capacity`: capacidade.
- `status`: `disponivel`, `ocupada`, `reservada`, `manutencao`.

Na abertura de comanda normal, apenas mesas `disponivel`, sem comanda aberta e sem reserva ativa no horario atual aparecem para selecao.

### Reservas

As reservas ficam em `reservations` e possuem:

- Mesa vinculada.
- Nome e telefone do cliente.
- Data/hora da reserva.
- Duracao em minutos.
- Status: `pendente`, `confirmada`, `cancelada`, `concluida`.

Ao cadastrar uma reserva, a mesa passa para `reservada`. Essa mesa nao aparece na criacao normal de comanda.

Para abrir comanda de uma mesa reservada, o atendente deve selecionar a reserva confirmada na tela **Nova comanda**. Nesse caso:

- A comanda e criada vinculada a reserva.
- A mesa passa para `ocupada`.
- A reserva passa para `concluida`.

Ao cancelar uma reserva sem comanda aberta, a mesa volta para `disponivel`.

Reservas podem ser editadas enquanto ainda nao possuem comanda vinculada. A remarcacao valida conflitos de horario e troca o status das mesas envolvidas dentro de transacao.

### Comandas

As comandas usam a tabela `tickets`.

Uma comanda pode estar vinculada a:

- Uma mesa (`restaurant_table_id`).
- Uma reserva (`reservation_id`), opcional.

Ao pagar ou cancelar uma comanda, a mesa volta para `disponivel`. O status `fechada` nao libera a mesa, pois ainda representa uma etapa antes do pagamento/cancelamento.

### Horario

As reservas sao tratadas com base no horario de Brasilia:

```env
APP_TIMEZONE=America/Sao_Paulo
```

O app tambem define `America/Sao_Paulo` como timezone padrao caso `APP_TIMEZONE` nao esteja configurado.

## Permissoes

O sistema usa roles em tabela propria (`roles`) vinculadas ao usuario por `role_id`.

Roles principais:

- `Administrador`
- `Cozinha`
- `Atendente`
- `Garcom`

Regras:

- Admin acessa cadastros administrativos.
- Admin consegue alterar roles de usuarios, mas nao consegue atribuir admin para outro usuario pela tela.
- Admin nao consegue alterar a propria role pela tela, evitando auto-bloqueio.
- Cozinha e Admin acessam a fila de atendimento.
- Garcom usa layout simplificado e mobile para criar reservas e abrir comandas.

## Instalacao

Clone o repositorio:

```bash
git clone <url-do-repositorio>
cd pedeai
```

Instale dependencias PHP:

```bash
composer install
```

Instale dependencias front-end:

```bash
npm install
```

Crie o `.env`:

```bash
cp .env.example .env
```

Gere a chave:

```bash
php artisan key:generate
```

Configure o banco no `.env`:

```env
APP_NAME=PedeAi
APP_TIMEZONE=America/Sao_Paulo

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pedeai
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

Rode migrations e seeders:

```bash
php artisan migrate --seed
```

Gere os assets:

```bash
npm run build
```

Para desenvolvimento:

```bash
composer run dev
```

Ou rode separadamente:

```bash
php artisan serve
npm run dev
```

## Usuario inicial

O seeder cria um usuario de teste:

```text
Email: test@example.com
Senha: password
Role: Administrador
```

Tambem cria roles, itens iniciais de cardapio e mesas iniciais.

## Rotas principais

### Publicas e autenticacao

| Metodo | Rota | Descricao |
| --- | --- | --- |
| GET | `/` | Home |
| GET/POST | `/login` | Login |
| POST | `/logout` | Logout |
| GET/POST | `/register` | Cadastro |
| GET/POST | `/forgot-password` | Recuperacao de senha |
| GET/POST | `/two-factor-challenge` | Desafio 2FA |

### Dashboard

| Metodo | Rota | Nome | Descricao |
| --- | --- | --- | --- |
| GET | `/dashboard` | `dashboard` | Dashboard de comandas |

### Comandas

| Metodo | Rota | Nome | Descricao |
| --- | --- | --- | --- |
| GET | `/ticket-list` | `ticket-list.index` | Listagem de comandas |
| GET | `/ticket-list/create` | `ticket-list.create` | Nova comanda |
| GET | `/ticket-list/available-tables` | `ticket-list.available-tables` | JSON de mesas disponiveis |
| POST | `/ticket-list/store` | `ticket-list.store` | Criar comanda |
| GET | `/ticket-list/{ticketList}` | `ticket-list.show` | Detalhes da comanda |
| PATCH | `/ticket-list/{ticketList}/status` | `ticket-list.status.update` | Alterar status da comanda |
| POST | `/ticket-list/{ticketList}/pay` | `ticket-list.pay` | Pagar comanda |
| POST | `/ticket-list/{ticketList}/items` | `ticket-list.items.store` | Adicionar itens na comanda |
| POST | `/ticket-list/{ticketList}/start-preparation` | `ticket-list.start-preparation` | Enviar para preparo |
| POST | `/ticket-items/{ticketItem}/deliver` | `ticket-items.deliver` | Marcar item como entregue |

### Reservas

| Metodo | Rota | Nome | Descricao |
| --- | --- | --- | --- |
| GET | `/reservations` | `reservations.index` | Listagem de reservas |
| GET | `/reservations/create` | `reservations.create` | Nova reserva |
| POST | `/reservations/store` | `reservations.store` | Criar reserva |
| GET | `/reservations/{reservation}/edit` | `reservations.edit` | Editar reserva |
| PATCH | `/reservations/{reservation}` | `reservations.update` | Atualizar reserva |
| PATCH | `/reservations/{reservation}/cancel` | `reservations.cancel` | Cancelar reserva |

### Mesas

| Metodo | Rota | Nome | Descricao |
| --- | --- | --- | --- |
| GET | `/restaurant-tables` | `restaurant-tables.index` | Listagem de mesas |
| GET | `/restaurant-tables/create` | `restaurant-tables.create` | Nova mesa |
| POST | `/restaurant-tables/store` | `restaurant-tables.store` | Criar mesa |
| GET | `/restaurant-tables/{restaurantTable}/edit` | `restaurant-tables.edit` | Editar mesa |
| PATCH | `/restaurant-tables/{restaurantTable}` | `restaurant-tables.update` | Atualizar mesa |

### Itens da comanda

| Metodo | Rota | Nome | Descricao |
| --- | --- | --- | --- |
| GET | `/menu-items` | `menu-items.index` | Listagem de itens |
| GET | `/menu-items/create` | `menu-items.create` | Novo item |
| POST | `/menu-items/store` | `menu-items.store` | Criar item |
| GET | `/menu-items/{menuItem}/edit` | `menu-items.edit` | Editar item |
| PATCH | `/menu-items/{menuItem}` | `menu-items.update` | Atualizar item |

### Cozinha

| Metodo | Rota | Nome | Descricao |
| --- | --- | --- | --- |
| GET | `/kitchen-queue` | `kitchen-queue.index` | Fila de atendimento/cozinha |

### Usuarios e configuracoes

| Metodo | Rota | Nome | Descricao |
| --- | --- | --- | --- |
| GET | `/users` | `users.index` | Listagem de usuarios |
| PATCH | `/users/{user}/role` | `users.role.update` | Alterar role do usuario |
| GET | `/settings/profile` | `profile.edit` | Perfil |
| GET | `/settings/security` | `security.edit` | Seguranca |
| GET | `/settings/appearance` | `appearance.edit` | Aparencia |

## Estrutura relevante

```text
app/
  Http/Controllers/
    DashboardController.php
    TicketListController.php
    ReservationController.php
    RestaurantTableController.php
    MenuItemController.php
    UserController.php
  Models/
    TicketList.php
    TicketItem.php
    Reservation.php
    RestaurantTable.php
    MenuItem.php
    Role.php
    User.php
    OperationalEvent.php
  Services/
    TicketOpeningService.php
    ReservationService.php
    TicketPaymentService.php
    OperationalAudit.php

resources/views/
  dashboard.blade.php
  ticket-list/
  reservations/
  restaurant-tables/
  menu-items/
  users/
  layouts/

database/migrations/
database/seeders/
```

## Services principais

### `TicketOpeningService`

Centraliza a regra critica de abertura de comanda:

- Busca mesas disponiveis.
- Busca reservas confirmadas que podem abrir comanda.
- Cria comanda em transacao.
- Usa `lockForUpdate()` para evitar corrida entre dois atendentes.
- Atualiza mesa e reserva junto com a criacao da comanda.

### `ReservationService`

Centraliza a regra de reserva:

- Lista mesas aptas para reserva.
- Cria reserva em transacao.
- Valida conflito de reserva por horario.
- Remarca reservas sem comanda vinculada.
- Marca mesa como `reservada`.
- Cancela reserva e libera mesa quando permitido.

### `TicketPaymentService`

Centraliza o pagamento da comanda:

- Calcula valor pago com desconto e acrescimo/servico.
- Marca a comanda como `paga`.
- Registra forma de pagamento e horario de pagamento.
- Libera a mesa quando nao houver outra comanda bloqueante.

### `OperationalAudit`

Registra eventos operacionais em `operational_events`, incluindo usuario, entidade afetada, evento e propriedades adicionais.

## Observacoes de ambiente

Para MySQL, garanta que o PHP tenha o driver PDO habilitado:

```ini
extension=pdo_mysql
```

Alguns comandos Artisan tambem precisam de `mbstring`:

```ini
extension=mbstring
```

Os testes usam SQLite em memoria. Para roda-los localmente, habilite tambem:

```ini
extension=pdo_sqlite
extension=sqlite3
```

Se aparecer `could not find driver`, o problema esta no PHP local sem `pdo_mysql`.

Se aparecer erro com `mb_strimwidth`, habilite `mbstring`.

Se os testes falharem com `Connection: sqlite` e `could not find driver`, habilite `pdo_sqlite`.

## Comandos uteis

```bash
php artisan migrate --seed
php artisan route:list
php artisan view:clear
php artisan config:clear
npm run build
npm run dev
composer run dev
```

## Licenca

Este projeto segue a licenca definida no arquivo `composer.json`.
