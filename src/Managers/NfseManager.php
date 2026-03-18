<?php

namespace DiogoGraciano\Nfephp\Managers;

use Exception;
use Hadder\NfseNacional\Dps;
use Hadder\NfseNacional\Tools as NfseTools;
use NFePHP\Common\Certificate;

class NfseManager
{
    /**
     * Instância do Tools para comunicação com a API NFSe Nacional
     */
    protected ?NfseTools $tools = null;

    /**
     * Configurações da NFSe
     *
     * @var array<string, mixed>
     */
    protected array $config;

    /**
     * Gerenciador de certificados
     */
    protected CertificateManager $certificateManager;

    /**
     * Construtor
     *
     * @param array<string, mixed> $config Configurações completas do pacote
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->certificateManager = new CertificateManager();
        $this->initializeNfse();
    }

    /**
     * Inicializa a comunicação com a API NFSe Nacional
     *
     * @throws Exception
     */
    protected function initializeNfse(): void
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

                $certificate = Certificate::readPfx(
                    $certificateContent,
                    $this->config['certificate']['password']
                );

                $nfseConfig = new \stdClass();
                $nfseConfig->tpamb = $this->config['nfse_config']['tpAmb'] ?? 2;

                $configJson = json_encode($nfseConfig);
                if ($configJson === false) {
                    throw new Exception("Erro ao converter configuração NFSe para JSON: " . json_last_error_msg());
                }

                $this->tools = new NfseTools($configJson, $certificate);

                $this->certificateManager->setCertificate($certificate);
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao inicializar NFSe Nacional: " . $e->getMessage());
        }
    }

    /**
     * Obtém instância do Tools da NFSe
     *
     * @throws Exception
     */
    public function getTools(): NfseTools
    {
        if (! $this->tools) {
            throw new Exception("NFSe Tools não inicializado. Verifique se o certificado está configurado.");
        }

        return $this->tools;
    }

    /**
     * Obtém o gerenciador de certificados
     */
    public function getCertificateManager(): CertificateManager
    {
        return $this->certificateManager;
    }

    /**
     * Cria XML do DPS (Declaração de Prestação de Serviço)
     *
     * @param \stdClass $std Dados do DPS
     * @return string XML do DPS
     * @throws Exception
     */
    public function createDps(\stdClass $std): string
    {
        try {
            $dps = new Dps($std);

            return $dps->render();
        } catch (\Throwable $e) {
            throw new Exception("Erro ao criar DPS: " . $e->getMessage());
        }
    }

    /**
     * Cria XML de evento do DPS
     *
     * @param \stdClass $std Dados do evento
     * @return string XML do evento
     * @throws Exception
     */
    public function createDpsEvento(\stdClass $std): string
    {
        try {
            $dps = new Dps($std);

            return $dps->renderEvento();
        } catch (\Throwable $e) {
            throw new Exception("Erro ao criar evento DPS: " . $e->getMessage());
        }
    }

    /**
     * Envia DPS para a API NFSe Nacional
     *
     * @param string $xml XML do DPS
     * @return string|array Resposta da API (array quando a SEFAZ responde com JSON)
     * @throws Exception
     */
    public function sendDps(string $xml): string|array
    {
        if (! $this->tools) {
            throw new Exception("NFSe Tools não inicializado. Verifique se o certificado está configurado.");
        }

        try {
            return $this->tools->enviaDps($xml);
        } catch (\Throwable $e) {
            throw new Exception("Erro ao enviar DPS: " . $e->getMessage());
        }
    }

    /**
     * Consulta NFSe por chave de acesso
     *
     * @param string $chave Chave de acesso da NFSe (50 caracteres)
     * @param bool $encoding Se deve converter encoding ISO-8859-1 para UTF-8
     * @return string Resposta da API
     * @throws Exception
     */
    public function consultNfseByKey(string $chave, bool $encoding = true): string
    {
        if (! $this->tools) {
            throw new Exception("NFSe Tools não inicializado. Verifique se o certificado está configurado.");
        }

        try {
            return $this->tools->consultarNfseChave($chave, $encoding);
        } catch (\Throwable $e) {
            throw new Exception("Erro ao consultar NFSe: " . $e->getMessage());
        }
    }

    /**
     * Consulta DPS por chave de acesso
     *
     * @param string $chave Chave de acesso do DPS
     * @return string Resposta da API
     * @throws Exception
     */
    public function consultDpsByKey(string $chave): string
    {
        if (! $this->tools) {
            throw new Exception("NFSe Tools não inicializado. Verifique se o certificado está configurado.");
        }

        try {
            return $this->tools->consultarDpsChave($chave);
        } catch (\Throwable $e) {
            throw new Exception("Erro ao consultar DPS: " . $e->getMessage());
        }
    }

    /**
     * Consulta eventos da NFSe
     *
     * @param string $chave Chave de acesso da NFSe
     * @param int|null $tipoEvento Tipo do evento (opcional)
     * @param int|null $nSequencial Número sequencial do evento (opcional)
     * @return string Resposta da API
     * @throws Exception
     */
    public function consultNfseEvents(string $chave, ?int $tipoEvento = null, ?int $nSequencial = null): string
    {
        if (! $this->tools) {
            throw new Exception("NFSe Tools não inicializado. Verifique se o certificado está configurado.");
        }

        try {
            return $this->tools->consultarNfseEventos($chave, $tipoEvento, $nSequencial);
        } catch (\Throwable $e) {
            throw new Exception("Erro ao consultar eventos da NFSe: " . $e->getMessage());
        }
    }

    /**
     * Obtém DANFSE (PDF) da NFSe
     *
     * @param string $chave Chave de acesso da NFSe
     * @return string Conteúdo binário do PDF
     * @throws Exception
     */
    public function getDanfse(string $chave): string
    {
        if (! $this->tools) {
            throw new Exception("NFSe Tools não inicializado. Verifique se o certificado está configurado.");
        }

        try {
            return $this->tools->consultarDanfse($chave);
        } catch (\Throwable $e) {
            throw new Exception("Erro ao obter DANFSE: " . $e->getMessage());
        }
    }

    /**
     * Cancela NFSe
     *
     * @param \stdClass $std Dados do cancelamento (chave, CNPJ, motivo, etc.)
     * @return string Resposta da API
     * @throws Exception
     */
    public function cancelNfse(\stdClass $std): string
    {
        if (! $this->tools) {
            throw new Exception("NFSe Tools não inicializado. Verifique se o certificado está configurado.");
        }

        try {
            return $this->tools->cancelaNfse($std);
        } catch (\Throwable $e) {
            throw new Exception("Erro ao cancelar NFSe: " . $e->getMessage());
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
     * Define configurações e reinicializa
     *
     * @param array<string, mixed> $config
     * @throws Exception
     */
    public function setConfig(array $config): void
    {
        $this->config = array_merge($this->config, $config);
        $this->initializeNfse();
    }
}
