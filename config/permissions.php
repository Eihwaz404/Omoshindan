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
                'support.areas.manage' => [
                    'label' => 'Gerenciar áreas e assuntos de suporte',
                    'description' => 'Permite cadastrar, editar e vincular usuários às áreas e aos assuntos de suporte.',
                ],
            ],
        ],
        'Administrativo' => [
            'label' => 'Administrativo',
            'permissions' => [
                'settings.manage' => [
                    'label' => 'Gerenciar configurações do sistema',
                    'description' => 'Permite ajustar parâmetros administrativos, como a atualização automática da fila de chamados.',
                ],
                'database.manage' => [
                    'label' => 'Gerenciar banco de dados',
                    'description' => 'Permite acessar a tela administrativa e sanitizar tabelas controladas.',
                ],
            ],
        ],
    ],
];
