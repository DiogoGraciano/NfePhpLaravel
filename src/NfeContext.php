<?php

namespace DiogoGraciano\Nfephp;

use DiogoGraciano\Nfephp\Managers\CertificateManager;
use DiogoGraciano\Nfephp\Managers\ContingencyManager;
use Exception;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Tools;

class NfeContext
{
    /**
     * Instancia do Tools para comunicacao com SEFAZ
     */
    protected ?Tools $tools = null;

    /**
     * Configuracoes do pacote
     *
     * @var array<string, mixed>
     */
    protected array $config;

    /**
     * Instancia do certificado digital
     */
    protected ?Certificate $certificate = null;

    /**
     * Gerenciador de contingencias
     */
    protected ContingencyManager $contingencyManager;

    /**
     * Gerenciador de certificados
     */
    protected CertificateManager $certificateManager;

    /**
     * Construtor
     */
    public function __construct()
    {
        $this->config = config('nfephp', []);
        $this->contingencyManager = new ContingencyManager(fn () => $this->reinitialize());
        $this->certificateManager = new CertificateManager();
        $this->initializeTools();
    }

    /**
     * Inicializa o Tools do NFePHP
     *
     * @throws Exception
     */
    protected function initializeTools(): void
    {
        try {
            if (
                ! empty($this->config['certificate']['path'])
                && ! empty($this->config['certificate']['password'])
            ) {
                $certificateContent = file_get_contents($this->config['certificate']['path']);
                if ($certificateContent === false) {
                    throw new Exception(
                        "Erro ao ler o arquivo do certificado: " . $this->config['certificate']['path']
                    );
                }

                $this->certificate = Certificate::readPfx(
                    $certificateContent,
                    $this->config['certificate']['password']
                );

                $configJson = json_encode($this->config['nfe_config']);
                if ($configJson === false) {
                    throw new Exception("Erro ao converter configuração para JSON: " . json_last_error_msg());
                }

                $this->tools = new Tools(
                    $configJson,
                    $this->certificate,
                    $this->contingencyManager->getContingency()
                );

                $this->certificateManager->setCertificate($this->certificate);
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao inicializar NFePHP: " . $e->getMessage());
        }
    }

    /**
     * Reinicializa o Tools (chamado apos mudanca de config ou contingencia)
     */
    public function reinitialize(): void
    {
        $this->initializeTools();
    }

    /**
     * Obtem instancia do Tools
     *
     * @throws Exception
     */
    public function getTools(): Tools
    {
        if (! $this->tools) {
            throw new Exception("Tools não inicializado. Verifique se o certificado está configurado.");
        }

        return $this->tools;
    }

    /**
     * Obtem instancia do certificado
     */
    public function getCertificate(): ?Certificate
    {
        return $this->certificate;
    }

    /**
     * Obtem configuracoes
     *
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Obtem configuracao NFe
     *
     * @return array<string, mixed>
     */
    public function getNFeConfig(): array
    {
        return $this->config['nfe_config'] ?? [];
    }

    /**
     * Define configuracoes
     *
     * @param array<string, mixed> $config
     */
    public function setConfig(array $config): void
    {
        $this->config = array_merge($this->config, $config);
        $this->initializeTools();
    }

    /**
     * Define configuracao NFe
     *
     * @param array<string, mixed> $config
     */
    public function setNFeConfig(array $config): void
    {
        $this->config['nfe_config'] = array_merge($this->config['nfe_config'] ?? [], $config);
        $this->initializeTools();
    }

    /**
     * Obtem o gerenciador de contingencias
     */
    public function getContingencyManager(): ContingencyManager
    {
        return $this->contingencyManager;
    }

    /**
     * Obtem o gerenciador de certificados
     */
    public function getCertificateManager(): CertificateManager
    {
        return $this->certificateManager;
    }
}
