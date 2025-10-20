<?php

namespace DiogoGraciano\Nfephp;

use DiogoGraciano\Nfephp\Managers\CertificateManager;
use DiogoGraciano\Nfephp\Managers\ContingencyManager;
use Exception;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Factories\QRCode;
use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;

class NfephpCore
{
    /**
     * Instância do Make para criação de NFe
     */
    protected ?Make $make = null;

    /**
     * Instância do Tools para comunicação com SEFAZ
     */
    protected ?Tools $tools = null;

    /**
     * Configurações do NFePHP
     *
     * @var array<string, mixed>
     */
    protected array $config;

    /**
     * Gerenciador de contingências
     */
    protected ContingencyManager $contingencyManager;

    /**
     * Gerenciador de certificados
     */
    protected CertificateManager $certificateManager;

    /**
     * Construtor da classe
     */
    public function __construct()
    {
        $this->config = config('nfephp', []);
        $this->contingencyManager = new ContingencyManager();
        $this->certificateManager = new CertificateManager();
        $this->initializeNFePHP();
    }

    /**
     * Inicializa o NFePHP
     *
     * @throws Exception
     */
    protected function initializeNFePHP(): void
    {
        try {
            // Inicializa o Make
            $this->make = new Make();

            // Se temos certificado configurado, inicializa o Tools
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

                $certificate = Certificate::readPfx(
                    $certificateContent,
                    $this->config['certificate']['password']
                );

                $configJson = json_encode($this->config['nfe_config']);
                if ($configJson === false) {
                    throw new Exception("Erro ao converter configuração para JSON: " . json_last_error_msg());
                }

                $this->tools = new Tools(
                    $configJson,
                    $certificate,
                    $this->contingencyManager->getContingency()
                );

                // Atualiza o gerenciador de certificados
                $this->certificateManager->setCertificate($certificate);
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao inicializar NFePHP: " . $e->getMessage());
        }
    }

    /**
     * Cria uma instância de NFe
     */
    public function createNFe(): ?Make
    {
        return $this->make;
    }

    /**
     * Cria uma instância de NFCe
     */
    public function createNFCe(): ?Make
    {
        return $this->make;
    }

    /**
     * Obtém instância do Tools
     */
    public function getTools(): Tools
    {
        if (! $this->tools) {
            throw new Exception("Tools não inicializado. Verifique se o certificado está configurado.");
        }

        return $this->tools;
    }

    /**
     * Envia NFe para SEFAZ
     *
     * @param array<string> $xmls Array de XMLs das NFe
     * @param string $idLote ID do lote
     * @param int $indSinc Indicador de sincronização
     * @param bool $compactar Se deve compactar
     * @param array<string> $xmlsSubstitutos Array de XMLs substitutos
     * @return string Resposta da SEFAZ
     * @throws Exception
     */
    public function sendNFe(
        array $xmls,
        string $idLote = '',
        int $indSinc = 0,
        bool $compactar = false,
        array &$xmlsSubstitutos = []
    ): string {
        if (! $this->tools) {
            throw new Exception("Tools não inicializado. Verifique se o certificado está configurado.");
        }

        try {
            return $this->tools->sefazEnviaLote($xmls, $idLote, $indSinc, $compactar, $xmlsSubstitutos);
        } catch (Exception $e) {
            throw new Exception("Erro ao enviar NFe: " . $e->getMessage());
        }
    }

    /**
     * Consulta NFe na SEFAZ
     *
     * @param string $chave Chave de acesso da NFe
     * @return string Resposta da SEFAZ
     * @throws Exception
     */
    public function consultNFe(string $chave): string
    {
        if (! $this->tools) {
            throw new Exception("Tools não inicializado. Verifique se o certificado está configurado.");
        }

        try {
            return $this->tools->sefazConsultaChave($chave);
        } catch (Exception $e) {
            throw new Exception("Erro ao consultar NFe: " . $e->getMessage());
        }
    }

    /**
     * Cancela NFe na SEFAZ
     *
     * @param string $chave Chave de acesso da NFe
     * @param string $justificativa Justificativa do cancelamento
     * @param string $numeroProtocolo Número do protocolo
     * @return string Resposta da SEFAZ
     * @throws Exception
     */
    public function cancelNFe(string $chave, string $justificativa, string $numeroProtocolo): string
    {
        if (! $this->tools) {
            throw new Exception("Tools não inicializado. Verifique se o certificado está configurado.");
        }

        try {
            return $this->tools->sefazCancela($chave, $justificativa, $numeroProtocolo);
        } catch (Exception $e) {
            throw new Exception("Erro ao cancelar NFe: " . $e->getMessage());
        }
    }

    /**
     * Inutiliza NFe na SEFAZ
     *
     * @param int $serie Série da NFe
     * @param int $numeroInicial Número inicial
     * @param int $numeroFinal Número final
     * @param string $justificativa Justificativa da inutilização
     * @param int|null $tpAmb Tipo de ambiente
     * @param string|null $ano Ano da inutilização
     * @return string Resposta da SEFAZ
     * @throws Exception
     */
    public function inutilizeNFe(
        int $serie,
        int $numeroInicial,
        int $numeroFinal,
        string $justificativa,
        ?int $tpAmb = null,
        ?string $ano = null
    ): string {
        if (! $this->tools) {
            throw new Exception("Tools não inicializado. Verifique se o certificado está configurado.");
        }

        try {
            return $this->tools->sefazInutiliza($serie, $numeroInicial, $numeroFinal, $justificativa, $tpAmb, $ano);
        } catch (Exception $e) {
            throw new Exception("Erro ao inutilizar NFe: " . $e->getMessage());
        }
    }

    /**
     * Gera QR Code para NFCe
     *
     * @param \DOMDocument $dom DOM da NFCe
     * @param string $token Token do CSC
     * @param string $idToken ID do token
     * @param string $versao Versão do QR Code
     * @param string $urlqr URL do QR Code
     * @param string $urichave URI da chave
     * @param Certificate|null $certificate Certificado digital
     * @return string QR Code
     * @throws Exception
     */
    public function generateQRCode(
        \DOMDocument $dom,
        string $token,
        string $idToken,
        string $versao,
        string $urlqr,
        string $urichave,
        ?Certificate $certificate = null
    ): string {
        try {
            return QRCode::putQRTag($dom, $token, $idToken, $versao, $urlqr, $urichave, $certificate);
        } catch (Exception $e) {
            throw new Exception("Erro ao gerar QR Code: " . $e->getMessage());
        }
    }

    /**
     * Gera QR Code para NFe
     *
     * @param \DOMDocument $dom DOM da NFe
     * @param string $token Token do CSC
     * @param string $idToken ID do token
     * @param string $versao Versão do QR Code
     * @param string $urlqr URL do QR Code
     * @param string $urichave URI da chave
     * @param Certificate|null $certificate Certificado digital
     * @return string QR Code
     * @throws Exception
     */
    public function generateNFeQRCode(
        \DOMDocument $dom,
        string $token,
        string $idToken,
        string $versao,
        string $urlqr,
        string $urichave,
        ?Certificate $certificate = null
    ): string {
        try {
            return QRCode::putQRTag($dom, $token, $idToken, $versao, $urlqr, $urichave, $certificate);
        } catch (Exception $e) {
            throw new Exception("Erro ao gerar QR Code da NFe: " . $e->getMessage());
        }
    }

    /**
     * Padroniza resposta da SEFAZ
     *
     * @param string $response Resposta da SEFAZ
     * @return \stdClass Resposta padronizada
     * @throws Exception
     */
    public function standardizeResponse(string $response): \stdClass
    {
        try {
            $st = new Standardize($response);

            return $st->toStd();
        } catch (Exception $e) {
            throw new Exception("Erro ao padronizar resposta: " . $e->getMessage());
        }
    }

    /**
     * Obtém configurações
     *
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Obtém configuração para NFe
     *
     * @return array<string, mixed>
     */
    public function getNFeConfig(): array
    {
        return $this->config['nfe_config'] ?? [];
    }

    /**
     * Define configurações
     *
     * @param array<string, mixed> $config
     */
    public function setConfig(array $config): void
    {
        $this->config = array_merge($this->config, $config);
        $this->initializeNFePHP();
    }

    /**
     * Define configuração para NFe
     *
     * @param array<string, mixed> $config
     */
    public function setNFeConfig(array $config): void
    {
        $this->config['nfe_config'] = array_merge($this->config['nfe_config'] ?? [], $config);
        $this->initializeNFePHP();
    }

    /**
     * Obtém o gerenciador de contingências
     */
    public function getContingencyManager(): ContingencyManager
    {
        return $this->contingencyManager;
    }

    /**
     * Obtém o gerenciador de certificados
     */
    public function getCertificateManager(): CertificateManager
    {
        return $this->certificateManager;
    }
}
