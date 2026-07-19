<?php

return [
    'roles' => [
        'super_admin' => 'Super Administrador',
        'admin' => 'Administrador',
        'support' => 'Suporte',
        'user' => 'Usuário',
    ],

    'groups' => [
        'Acesso' => [
            'label' => 'Acesso',
            'permissions' => [
                'users.view' => [
                    'label' => 'Visualizar usuários',
                    'description' => 'Permite listar e consultar usuários do sistema.',
                ],
                'users.create' => [
                    'label' => 'Criar usuários',
                    'description' => 'Permite cadastrar novos usuários.',
                ],
                'users.update' => [
                    'label' => 'Editar usuários',
                    'description' => 'Permite alterar dados cadastrais dos usuários.',
                ],
                'users.delete' => [
                    'label' => 'Excluir usuários',
                    'description' => 'Permite remover usuários do sistema.',
                ],
                'users.toggle' => [
                    'label' => 'Ativar/desativar usuários',
                    'description' => 'Permite alternar o status de acesso dos usuários.',
                ],
                'users.permissions' => [
                    'label' => 'Gerenciar permissões',
                    'description' => 'Permite atribuir permissões individualmente aos usuários.',
                ],
            ],
        ],
        'Suporte' => [
            'label' => 'Suporte',
            'permissions' => [
                'support.areas.service_desk' => [
                    'label' => 'Atender Service Desk',
                    'description' => 'Permite assumir e trabalhar tickets direcionados para Service Desk.',
                ],
                'support.areas.systems' => [
                    'label' => 'Atender Sistemas',
                    'description' => 'Permite assumir e trabalhar tickets direcionados para Sistemas.',
                ],
                'support.areas.development' => [
                    'label' => 'Atender Desenvolvimento',
                    'description' => 'Permite assumir e trabalhar tickets direcionados para Desenvolvimento.',
                ],
                'support.areas.infrastructure' => [
                    'label' => 'Atender Infraestrutura',
                    'description' => 'Permite assumir e trabalhar tickets direcionados para Infraestrutura.',
                ],
            ],
        ],
    ],
];
