<?php

return [
    'areas' => [
        'service_desk' => [
            'label' => 'Service Desk',
            'description' => 'Triagem inicial e atendimento de primeiro nível.',
        ],
        'systems' => [
            'label' => 'Sistemas',
            'description' => 'Demandas funcionais e integrações de sistemas.',
        ],
        'development' => [
            'label' => 'Desenvolvimento',
            'description' => 'Correções, evoluções e ajustes em código.',
        ],
        'infrastructure' => [
            'label' => 'Infraestrutura',
            'description' => 'Rede, servidores, acessos e ambiente.',
        ],
    ],

    'routing' => [
        'default_area' => 'service_desk',
        'keywords' => [
            'service_desk' => [
                'acesso',
                'senha',
                'login',
                'logar',
                'impressora',
                'email',
                'e-mail',
                'cadastro',
                'suporte',
                'chamado',
                'erro simples',
            ],
            'systems' => [
                'sistema',
                'erp',
                'módulo',
                'modulo',
                'tela',
                'relatório',
                'relatorio',
                'funcional',
                'cadastro',
                'integração',
                'integracao',
            ],
            'development' => [
                'bug',
                'código',
                'codigo',
                'deploy',
                'release',
                'ajuste',
                'feature',
                'script',
                'api',
                'exception',
                'stack trace',
            ],
            'infrastructure' => [
                'rede',
                'internet',
                'vpn',
                'servidor',
                'backup',
                'firewall',
                'wi-fi',
                'wifi',
                'dns',
                'roteador',
                'switch',
                'storage',
                'ldap',
                'active directory',
                'dominio',
            ],
        ],
    ],

    'statuses' => [
        'open' => 'Aberto',
        'analysis' => 'Em Análise',
        'pending' => 'Pendente',
        'resolved' => 'Finalizado',
        'closed' => 'Fechado',
    ],

    'priorities' => [
        'low' => [
            'label' => 'Baixo',
            'badge' => 'border-emerald-400/20 bg-emerald-500/10 text-emerald-100',
            'icon' => 'text-emerald-300',
        ],
        'normal' => [
            'label' => 'Normal',
            'badge' => 'border-amber-400/20 bg-amber-500/10 text-amber-100',
            'icon' => 'text-amber-300',
        ],
        'medium' => [
            'label' => 'Médio',
            'badge' => 'border-orange-400/20 bg-orange-500/10 text-orange-100',
            'icon' => 'text-orange-300',
        ],
        'high' => [
            'label' => 'Alto',
            'badge' => 'border-rose-400/20 bg-rose-500/10 text-rose-100',
            'icon' => 'text-rose-300',
        ],
    ],

    'event_types' => [
        'created' => 'Ticket aberto',
        'comment' => 'Nova informação',
        'assigned' => 'Assumido pela TI',
        'transferred' => 'Encaminhado para outra área',
        'analysis' => 'Em análise',
        'requested_info' => 'Solicitação de informações',
        'resolved' => 'Marcado como solucionado',
        'pending' => 'Devolvido para a TI',
        'closed' => 'Fechado pelo usuário',
    ],
];
