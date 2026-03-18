<?php

namespace DiogoGraciano\Nfephp\Tests;

use DiogoGraciano\Nfephp\NfephpServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Configurações padrão para os testes
        config([
            'nfephp' => [
                'certificate' => [
                    'path' => '',
                    'password' => '',
                ],
                'danfe' => [
                    'logo_path' => null,
                ],
                'nfe_config' => [
                    'atualizacao' => '2024-01-01 00:00:00',
                    'tpAmb' => 2, // Homologação
                    'razaosocial' => 'Empresa Teste',
                    'cnpj' => '12345678000195',
                    'siglaUF' => 'SP',
                    'schemes' => 'PL_009_V4_00_NT_2020_006_v1.20',
                    'versao' => '4.00',
                    'tokenIBPT' => '',
                    'CSC' => '',
                    'CSCid' => '',
                ],
            ],
        ]);
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            NfephpServiceProvider::class,
        ];
    }

    /**
     * Get package aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageAliases($app): array
    {
        return [
            'Nfe' => \DiogoGraciano\Nfephp\Facades\Nfe::class,
            'Danfe' => \DiogoGraciano\Nfephp\Facades\Danfe::class,
            'Contingency' => \DiogoGraciano\Nfephp\Facades\Contingency::class,
            'Certificate' => \DiogoGraciano\Nfephp\Facades\Certificate::class,
            'Utils' => \DiogoGraciano\Nfephp\Facades\Utils::class,
        ];
    }
}
