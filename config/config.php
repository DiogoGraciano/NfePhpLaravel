<?php

/*
 * Configuração do NFePHP para Laravel
 * 
 * Baseado na documentação oficial: https://github.com/nfephp-org/sped-nfe/blob/master/docs/Config.md
 * 
 * IMPORTANTE: O NFePHP espera um array simples, não uma estrutura aninhada complexa.
 * Esta configuração será convertida para o formato JSON esperado pelo NFePHP.
 */
return [
    /*
     * Configurações do certificado digital
     * 
     * IMPORTANTE: 
     * - 'path' deve conter o caminho completo para o arquivo .pfx
     * - 'password' deve conter a senha do certificado
     * - O conteúdo binário será lido automaticamente pelo pacote
     */
    'certificate' => [
        'path' => env('NFEPHP_CERTIFICATE_PATH', ''),
        'password' => env('NFEPHP_CERTIFICATE_PASSWORD', ''),
    ],

    /*
     * Configuração principal do NFePHP (formato esperado pela biblioteca)
     * 
     * Esta é a configuração que será passada diretamente para o Tools do NFePHP
     */
    'nfe_config' => [
        'atualizacao' => date('Y-m-d H:i:s'),
        'tpAmb' => (int) env('NFEPHP_TP_AMB', 2), // 1=Produção, 2=Homologação
        'razaosocial' => env('NFEPHP_RAZAO_SOCIAL', ''),
        'cnpj' => env('NFEPHP_CNPJ', ''),
        'ie' => env('NFEPHP_IE', ''),
        'siglaUF' => env('NFEPHP_SIGLA_UF', ''),
        'schemes' => env('NFEPHP_SCHEMES', 'PL_009_V4'),
        'versao' => env('NFEPHP_VERSAO', '4.00'),
        'tokenIBPT' => env('NFEPHP_TOKEN_IBPT', ''),
        'CSC' => env('NFEPHP_CSC', ''),
        'CSCid' => env('NFEPHP_CSC_ID', ''),
        'proxyConf' => [
            'proxyIp' => env('NFEPHP_PROXY_HOST', ''),
            'proxyPort' => env('NFEPHP_PROXY_PORT', ''),
            'proxyUser' => env('NFEPHP_PROXY_USER', ''),
            'proxyPass' => env('NFEPHP_PROXY_PASSWORD', ''),
        ],
    ],

    /*
     * Configurações de paths para arquivos
     */
    'paths' => [
        'schemes' => storage_path('app/nfephp/schemes'),
        'nfe' => storage_path('app/nfephp/nfe'),
        'nfce' => storage_path('app/nfephp/nfce'),
        'csc' => storage_path('app/nfephp/csc'),
        'csp' => storage_path('app/nfephp/csp'),
        'logs' => storage_path('logs/nfephp'),
        'cacerts' => storage_path('app/nfephp/cacerts'),
        'arquivos' => storage_path('app/nfephp/arquivos'),
        'tmp' => storage_path('app/nfephp/tmp'),
    ],

    /*
     * Configurações de ambiente
     */
    'environment' => [
        'production' => (bool) env('NFEPHP_PRODUCTION', false),
        'debug' => (bool) env('NFEPHP_DEBUG', false),
    ],

    /*
     * Configurações de timeout
     */
    'timeout' => [
        'connection' => (int) env('NFEPHP_TIMEOUT_CONNECTION', 30),
        'read' => (int) env('NFEPHP_TIMEOUT_READ', 60),
    ],

    /*
     * Configurações de contingência
     * 
     * IMPORTANTE: As contingências são ativadas dinamicamente via código,
     * mas você pode definir configurações padrão aqui.
     */
    'contingency' => [
        'auto_activate' => (bool) env('NFEPHP_CONTINGENCY_AUTO_ACTIVATE', false),
        'default_motive' => env('NFEPHP_CONTINGENCY_DEFAULT_MOTIVE', 'SEFAZ fora do ar'),
        'default_type' => env('NFEPHP_CONTINGENCY_DEFAULT_TYPE', ''), // SVCAN, SVCRS ou vazio para automático
    ],
];