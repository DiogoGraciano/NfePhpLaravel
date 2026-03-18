<?php

namespace DiogoGraciano\Nfephp;

use DiogoGraciano\Nfephp\Managers\CertificateManager;
use DiogoGraciano\Nfephp\Managers\DanfeManager;
use DiogoGraciano\Nfephp\Managers\NfeManager;
use DiogoGraciano\Nfephp\Managers\UtilsManager;
use Illuminate\Support\ServiceProvider;

class NfephpServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('nfephp.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'nfephp');

        // Contexto compartilhado (config, certificate, tools, contingency)
        $this->app->singleton(NfeContext::class, fn ($app) => new NfeContext());

        // NFe - operacoes com SEFAZ
        $this->app->singleton('nfe', fn ($app) => new NfeManager($app->make(NfeContext::class)));

        // DANFE - geracao de PDF
        $this->app->singleton('danfe', fn ($app) => new DanfeManager(config('nfephp.danfe.logo_path')));

        // Contingencia
        $this->app->singleton('contingency', fn ($app) => $app->make(NfeContext::class)->getContingencyManager());

        // Certificado digital
        $this->app->singleton('certificate', fn ($app) => $app->make(NfeContext::class)->getCertificateManager());

        // Utilitarios (validacao, formatacao, UF)
        $this->app->singleton('nfe-utils', fn ($app) => new UtilsManager());
    }
}
