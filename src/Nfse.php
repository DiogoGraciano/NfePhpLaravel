<?php

namespace DiogoGraciano\Nfephp;

use DiogoGraciano\Nfephp\Managers\NfseManager;

class Nfse
{
    /**
     * Gerenciador de NFSe
     */
    protected NfseManager $nfseManager;

    /**
     * Construtor
     */
    public function __construct()
    {
        $config = config('nfephp', []);
        $this->nfseManager = new NfseManager($config);
    }

    /**
     * Obtém o gerenciador de NFSe
     */
    public function getNfseManager(): NfseManager
    {
        return $this->nfseManager;
    }

    // ============================================
    // MÉTODOS DE CONVENIÊNCIA PARA DPS
    // ============================================

    /**
     * Cria XML do DPS (Declaração de Prestação de Serviço)
     *
     * @param \stdClass $std Dados do DPS
     * @return string XML do DPS
     * @throws \Exception
     */
    public function createDps(\stdClass $std): string
    {
        return $this->nfseManager->createDps($std);
    }

    /**
     * Cria XML de evento do DPS
     *
     * @param \stdClass $std Dados do evento
     * @return string XML do evento
     * @throws \Exception
     */
    public function createDpsEvento(\stdClass $std): string
    {
        return $this->nfseManager->createDpsEvento($std);
    }

    /**
     * Envia DPS para a API NFSe Nacional
     *
     * @param string $xml XML do DPS
     * @return string|array Resposta da API (array quando a SEFAZ responde com JSON)
     * @throws \Exception
     */
    public function sendDps(string $xml): string|array
    {
        return $this->nfseManager->sendDps($xml);
    }

    // ============================================
    // MÉTODOS DE CONVENIÊNCIA PARA CONSULTAS
    // ============================================

    /**
     * Consulta NFSe por chave de acesso
     *
     * @param string $chave Chave de acesso da NFSe (50 caracteres)
     * @param bool $encoding Se deve converter encoding ISO-8859-1 para UTF-8
     * @return string Resposta da API
     * @throws \Exception
     */
    public function consultNfseByKey(string $chave, bool $encoding = true): string
    {
        return $this->nfseManager->consultNfseByKey($chave, $encoding);
    }

    /**
     * Consulta DPS por chave de acesso
     *
     * @param string $chave Chave de acesso do DPS
     * @return string Resposta da API
     * @throws \Exception
     */
    public function consultDpsByKey(string $chave): string
    {
        return $this->nfseManager->consultDpsByKey($chave);
    }

    /**
     * Consulta eventos da NFSe
     *
     * @param string $chave Chave de acesso da NFSe
     * @param int|null $tipoEvento Tipo do evento (opcional)
     * @param int|null $nSequencial Número sequencial do evento (opcional)
     * @return string Resposta da API
     * @throws \Exception
     */
    public function consultNfseEvents(string $chave, ?int $tipoEvento = null, ?int $nSequencial = null): string
    {
        return $this->nfseManager->consultNfseEvents($chave, $tipoEvento, $nSequencial);
    }

    // ============================================
    // MÉTODOS DE CONVENIÊNCIA PARA DANFSE
    // ============================================

    /**
     * Obtém DANFSE (PDF) da NFSe
     *
     * @param string $chave Chave de acesso da NFSe
     * @return string Conteúdo binário do PDF
     * @throws \Exception
     */
    public function getDanfse(string $chave): string
    {
        return $this->nfseManager->getDanfse($chave);
    }

    // ============================================
    // MÉTODOS DE CONVENIÊNCIA PARA CANCELAMENTO
    // ============================================

    /**
     * Cancela NFSe
     *
     * @param \stdClass $std Dados do cancelamento (chave, CNPJ, motivo, etc.)
     * @return string Resposta da API
     * @throws \Exception
     */
    public function cancelNfse(\stdClass $std): string
    {
        return $this->nfseManager->cancelNfse($std);
    }

    // ============================================
    // MÉTODOS DE CONFIGURAÇÃO
    // ============================================

    /**
     * Obtém configurações
     *
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return $this->nfseManager->getConfig();
    }

    /**
     * Define configurações e reinicializa
     *
     * @param array<string, mixed> $config
     * @throws \Exception
     */
    public function setConfig(array $config): void
    {
        $this->nfseManager->setConfig($config);
    }
}
