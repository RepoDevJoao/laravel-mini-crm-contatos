# Mini CRM de Contatos

API REST para gerenciamento de contatos com cálculo assíncrono de score, eventos de domínio e atualização em tempo real via WebSockets.

---

## Stack

- **Laravel 10** + **PHP 8.5**
- **MySQL 8.4** — banco de dados
- **Redis** — fila assíncrona
- **Laravel Reverb** — WebSocket server
- **Laravel Sail** — ambiente Docker
- **PHPUnit 10** — testes

---

## Arquitetura

O projeto segue **DDD pragmático** com separação clara em três camadas:

    app/
    ├── Domain/          # Regras de negócio puras
    │   └── Contact/
    │       ├── ValueObjects/   # Email, Phone, ContactStatus
    │       ├── Strategies/     # EmailScoreStrategy, NameScoreStrategy, PhoneScoreStrategy
    │       ├── Services/       # ContactScoreCalculator
    │       └── Events/
    ├── Application/     # Orquestração dos fluxos
    │   └── Contact/
    │       ├── UseCases/
    │       ├── DTOs/
    │       └── Contracts/      # ContactRepositoryInterface
    └── Infrastructure/  # Laravel, Eloquent, Redis, Reverb
        ├── Http/               # Controllers, Requests, Resources
        ├── Persistence/        # Eloquent Model, Repository, Observer
        ├── Queue/              # Jobs
        └── Broadcasting/       # Events, Listeners

### Padrões aplicados

- **Strategy Pattern** — cada regra de score é uma estratégia isolada e extensível
- **Repository Pattern** — ContactRepositoryInterface desacopla Application da infraestrutura
- **Value Objects** — Email e Phone encapsulam validação e lógica de domínio
- **Use Cases** — orquestração sem lógica nos Controllers ou Models

---

## Pré-requisitos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- [WSL2](https://learn.microsoft.com/pt-br/windows/wsl/install) (Windows)
- [Composer](https://getcomposer.org/)

---

## Setup do ambiente

### 1. Clone o repositório

    git clone https://github.com/RepoDevJoao/laravel-mini-crm-contatos.git
    cd laravel-mini-crm-contatos

### 2. Instale as dependências

    composer install

### 3. Configure o ambiente

    cp .env.example .env
    php artisan key:generate

Edite o .env e ajuste:

    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=mini_crm
    DB_USERNAME=sail
    DB_PASSWORD=password

    QUEUE_CONNECTION=redis
    CACHE_DRIVER=redis

    REDIS_HOST=redis
    REDIS_PASSWORD=null
    REDIS_PORT=6379

    BROADCAST_DRIVER=reverb
    REVERB_APP_ID=mini-crm
    REVERB_APP_KEY=mini-crm-key
    REVERB_APP_SECRET=mini-crm-secret
    REVERB_HOST=localhost
    REVERB_PORT=8080
    REVERB_SCHEME=http

### 4. Suba os containers

    ./vendor/bin/sail up -d

### 5. Rode as migrations

    ./vendor/bin/sail artisan migrate

---

## Rodando a aplicação completa

Você precisará de três terminais rodando simultaneamente:

Terminal 1 — Aplicação (já está rodando com sail up -d)

Terminal 2 — Worker da fila (Redis)

    ./vendor/bin/sail artisan queue:work

Terminal 3 — Servidor WebSocket (Reverb)

    ./vendor/bin/sail artisan reverb:start

---

## Rodando os testes

    ./vendor/bin/sail artisan test

Para rodar por camada:

    # Apenas testes unitários (Domain)
    ./vendor/bin/sail artisan test tests/Unit/

    # Apenas testes de integração (Use Cases)
    ./vendor/bin/sail artisan test tests/Integration/

    # Apenas testes de feature (Endpoints)
    ./vendor/bin/sail artisan test tests/Feature/

---

## Endpoints

| Método | Rota | Descrição |
|--------|------|-----------|
| POST | /api/contacts | Criar contato |
| GET | /api/contacts | Listar contatos (paginado) |
| GET | /api/contacts/{id} | Exibir contato |
| PUT | /api/contacts/{id} | Atualizar contato |
| DELETE | /api/contacts/{id} | Excluir contato (soft delete) |
| POST | /api/contacts/{id}/process-score | Enfileirar cálculo de score |

### Exemplos

Criar contato:

    curl -s -X POST http://localhost/api/contacts \
      -H "Content-Type: application/json" \
      -H "Accept: application/json" \
      -d '{"name":"João Roberto","email":"joaoroberto@empresa.com.br","phone":"(11) 99999-8888"}' | jq

Processar score:

    curl -s -X POST http://localhost/api/contacts/1/process-score \
      -H "Accept: application/json" | jq

---

## Regras de cálculo do score

| Critério | Pontos |
|----------|--------|
| E-mail com domínio corporativo (não gmail/hotmail/yahoo) | +20 |
| E-mail terminado em .br | +10 |
| Nome completo (mais de uma palavra) | +10 |
| Telefone com DDD de SP (11-19) | +20 |
| Telefone com DDD de outro estado | +10 |

Os bônus de e-mail são cumulativos: joao@empresa.com.br soma +30 pontos.

---

## Fluxo do process-score

    POST /api/contacts/{id}/process-score
            │
            ▼
    ProcessContactScoreJob (enfileirado no Redis)
            │
            ▼
    ProcessContactScoreUseCase
      ├── status → processing
      ├── ContactScoreCalculator (Strategy Pattern)
      │     ├── EmailScoreStrategy
      │     ├── NameScoreStrategy
      │     └── PhoneScoreStrategy
      ├── status → active | failed
      ├── score e processed_at salvos
      └── ContactScoreProcessed (event disparado)
                │
                ├── LogContactScoreListener → storage/logs/contact.log
                └── Broadcast → canal contacts.{id} via Reverb

---

## Escutando o WebSocket (exemplo HTML/JS)

Com o Reverb rodando (sail artisan reverb:start), crie um arquivo websocket-test.html e abra no navegador:

    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title>Mini CRM - WebSocket Test</title>
        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    </head>
    <body>
        <h2>Aguardando atualização de score...</h2>
        <pre id="output">Conectando...</pre>

        <script>
            const pusher = new Pusher('mini-crm-key', {
                wsHost: 'localhost',
                wsPort: 8080,
                forceTLS: false,
                disableStats: true,
                enabledTransports: ['ws'],
                cluster: 'mt1',
            });

            const contactId = 1; // altere para o ID desejado
            const channel = pusher.subscribe('contacts.' + contactId);

            channel.bind('score.processed', function(data) {
                document.getElementById('output').textContent =
                    JSON.stringify(data, null, 2);
            });

            pusher.connection.bind('connected', () => {
                document.getElementById('output').textContent =
                    'Conectado! Aguardando eventos no canal contacts.' + contactId;
            });
        </script>
    </body>
    </html>

---

## Estrutura de testes

    tests/
    ├── Unit/Domain/
    │   ├── ValueObjects/   # ContactStatus, Email, Phone
    │   ├── Strategies/     # EmailScore, NameScore, PhoneScore
    │   └── Services/       # ContactScoreCalculator
    ├── Integration/UseCases/
    │   └── ProcessContactScoreUseCaseTest
    └── Feature/Api/
        ├── ContactCrudTest
        └── ProcessScoreTest

56 testes | 80 assertions