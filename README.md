# Omoshindan

Plataforma oficial de suporte e gestão de chamados da Eihwaz.

O Omoshindan centraliza abertura, triagem, encaminhamento, acompanhamento e fechamento de tickets com fluxo auditável, SLA em horas úteis e painel de indicadores em tempo real.

## Visão Geral

- abertura de tickets por usuário comum
- triagem e execução por áreas de suporte
- máquina de estados com histórico completo
- prioridades com prazo de solução configurável
- cálculo de SLA apenas em horário útil da TI
- dashboard com indicadores operacionais
- atualização automática de fila e SLA via Livewire
- interface moderna com Tailwind CSS, Alpine.js e Blade Heroicons

## Stack

- Laravel 13
- PHP 8.3+
- Livewire 3
- Tailwind CSS
- Alpine.js
- Blade Heroicons

## Funcionalidades Principais

- cadastro e gestão de áreas de suporte
- cadastro e gestão de assuntos de ticket
- abertura de chamados com prioridade
- encaminhamento entre áreas
- solicitação de informações ao solicitante
- confirmação de solução e encerramento
- painel com indicadores por status, área e técnico
- SLA por prioridade com jornada de trabalho configurável
- controle administrativo de usuários, permissões e parâmetros

## Configurações Administrativas

O sistema permite ajustar:

- intervalo de atualização da fila de tickets
- intervalo de atualização do dashboard
- jornada de trabalho da TI de segunda a sexta
- pausa de almoço
- tempo de solução por prioridade

## Requisitos

- PHP 8.3 ou superior
- Composer
- Node.js e npm
- banco de dados compatível com Laravel

## Instalação

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

## Desenvolvimento

```bash
composer run dev
```

O comando acima inicia:

- servidor Laravel
- worker de filas
- logs em tempo real
- Vite em modo de desenvolvimento

## Testes

```bash
php artisan test
```

## Documentação

- [`docs/omoshindan_diretrizes.md`](docs/omoshindan_diretrizes.md)
- [`docs/diretrizes_tickets.md`](docs/diretrizes_tickets.md)
- [`docs/github_publish_guide.md`](docs/github_publish_guide.md)

## English

Omoshindan is the official support and ticket management platform for Eihwaz.

It centralizes ticket intake, triage, routing, SLA tracking, and operational monitoring with a real-time dashboard and a strict, auditable workflow.

### Highlights

- ticket creation and tracking
- support-area routing
- auditable ticket lifecycle
- configurable resolution priorities
- business-hours SLA calculation
- Livewire-powered auto refresh
- modern UI with Tailwind, Alpine.js, and Blade Heroicons

## License

MIT
