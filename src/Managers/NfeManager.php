<?php

namespace DiogoGraciano\Nfephp\Managers;

use DiogoGraciano\Nfephp\NfeContext;
use Exception;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Factories\QRCode;
use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;

class NfeManager
{
    /**
     * Construtor
     */
    public function __construct(protected NfeContext $context)
    {
    }

    /**
     * Cria uma nova instancia de NFe (Make)
     */
    public function createNFe(): Make
    {
        return new Make();
    }

    /**
     * Cria uma nova instancia de NFCe (Make)
     */
    public function createNFCe(): Make
    {
        return new Make();
    }

    /**
     * Obtem instancia do Tools
     */
    public function getTools(): Tools
    {
        return $this->context->getTools();
    }

    /**
     * Envia NFe para SEFAZ
     *
     * @param array<string> $xmls Array de XMLs das NFe
     * @param string $idLote ID do lote
     * @param int $indSinc Indicador de sincronizacao
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
        try {
            return $this->context->getTools()->sefazEnviaLote($xmls, $idLote, $indSinc, $compactar, $xmlsSubstitutos);
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
        try {
            return $this->context->getTools()->sefazConsultaChave($chave);
        } catch (Exception $e) {
            throw new Exception("Erro ao consultar NFe: " . $e->getMessage());
        }
    }

    /**
     * Cancela NFe na SEFAZ
     *
     * @param string $chave Chave de acesso da NFe
     * @param string $justificativa Justificativa do cancelamento
     * @param string $numeroProtocolo Numero do protocolo
     * @return string Resposta da SEFAZ
     * @throws Exception
     */
    public function cancelNFe(string $chave, string $justificativa, string $numeroProtocolo): string
    {
        try {
            return $this->context->getTools()->sefazCancela($chave, $justificativa, $numeroProtocolo);
        } catch (Exception $e) {
            throw new Exception("Erro ao cancelar NFe: " . $e->getMessage());
        }
    }

    /**
     * Inutiliza NFe na SEFAZ
     *
     * @param int $serie Serie da NFe
     * @param int $numeroInicial Numero inicial
     * @param int $numeroFinal Numero final
     * @param string $justificativa Justificativa da inutilizacao
     * @param int|null $tpAmb Tipo de ambiente
     * @param string|null $ano Ano da inutilizacao
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
        try {
            return $this->context->getTools()->sefazInutiliza($serie, $numeroInicial, $numeroFinal, $justificativa, $tpAmb, $ano);
        } catch (Exception $e) {
            throw new Exception("Erro ao inutilizar NFe: " . $e->getMessage());
        }
    }

    /**
     * Consulta distribuicao de DFe na SEFAZ
     *
     * @param int $ultNSU Ultimo NSU recebido
     * @param int $numNSU NSU especifico para consulta
     * @param string|null $chave Chave de acesso da NFe
     * @param string $fonte Fonte de dados (AN ou RS)
     * @return string Resposta da SEFAZ
     * @throws Exception
     */
    public function distributionDFe(
        int $ultNSU = 0,
        int $numNSU = 0,
        ?string $chave = null,
        string $fonte = 'AN'
    ): string {
        try {
            return $this->context->getTools()->sefazDistDFe($ultNSU, $numNSU, $chave, $fonte);
        } catch (Exception $e) {
            throw new Exception("Erro ao consultar distribuição DFe: " . $e->getMessage());
        }
    }

    /**
     * Manifesta destinatario na SEFAZ
     *
     * @param string $chave Chave de acesso da NFe
     * @param int $tpEvento Tipo de evento (210200=Confirmacao, 210210=Ciencia, 210220=Desconhecimento, 210240=Nao Realizada)
     * @param string $xJust Justificativa (obrigatoria para Nao Realizada)
     * @param int $nSeqEvento Numero sequencial do evento
     * @return string Resposta da SEFAZ
     * @throws Exception
     */
    public function manifestNFe(
        string $chave,
        int $tpEvento,
        string $xJust = '',
        int $nSeqEvento = 1
    ): string {
        try {
            return $this->context->getTools()->sefazManifesta($chave, $tpEvento, $xJust, $nSeqEvento);
        } catch (Exception $e) {
            throw new Exception("Erro ao manifestar NFe: " . $e->getMessage());
        }
    }

    /**
     * Manifesta destinatario em lote na SEFAZ
     *
     * @param \stdClass $std Objeto com array de eventos (maximo 20)
     * @return string Resposta da SEFAZ
     * @throws Exception
     */
    public function manifestNFeLote(\stdClass $std): string
    {
        try {
            return $this->context->getTools()->sefazManifestaLote($std);
        } catch (Exception $e) {
            throw new Exception("Erro ao manifestar NFe em lote: " . $e->getMessage());
        }
    }

    /**
     * Confirma operacao da NFe (Manifestacao: Confirmacao da Operacao)
     *
     * @param string $chave Chave de acesso da NFe
     * @param int $nSeqEvento Numero sequencial do evento
     * @return string Resposta da SEFAZ
     * @throws Exception
     */
    public function confirmNFe(string $chave, int $nSeqEvento = 1): string
    {
        return $this->manifestNFe($chave, 210200, '', $nSeqEvento);
    }

    /**
     * Registra ciencia da operacao da NFe (Manifestacao: Ciencia da Operacao)
     *
     * @param string $chave Chave de acesso da NFe
     * @return string Resposta da SEFAZ
     * @throws Exception
     */
    public function acknowledgeNFe(string $chave): string
    {
        return $this->manifestNFe($chave, 210210);
    }

    /**
     * Registra desconhecimento da operacao da NFe (Manifestacao: Desconhecimento da Operacao)
     *
     * @param string $chave Chave de acesso da NFe
     * @param int $nSeqEvento Numero sequencial do evento
     * @return string Resposta da SEFAZ
     * @throws Exception
     */
    public function unknownNFe(string $chave, int $nSeqEvento = 1): string
    {
        return $this->manifestNFe($chave, 210220, '', $nSeqEvento);
    }

    /**
     * Registra operacao nao realizada (Manifestacao: Operacao Nao Realizada)
     *
     * @param string $chave Chave de acesso da NFe
     * @param string $xJust Justificativa (obrigatoria)
     * @param int $nSeqEvento Numero sequencial do evento
     * @return string Resposta da SEFAZ
     * @throws Exception
     */
    public function notPerformedNFe(string $chave, string $xJust, int $nSeqEvento = 1): string
    {
        return $this->manifestNFe($chave, 210240, $xJust, $nSeqEvento);
    }

    /**
     * Manifesta destinatario em lote na SEFAZ (alias)
     *
     * @param \stdClass $std Objeto com array de eventos (maximo 20)
     * @return string Resposta da SEFAZ
     * @throws Exception
     */
    public function manifestNFeBatch(\stdClass $std): string
    {
        return $this->manifestNFeLote($std);
    }

    /**
     * Gera QR Code para NFCe
     *
     * @param \DOMDocument $dom DOM da NFCe
     * @param string $token Token do CSC
     * @param string $idToken ID do token
     * @param string $versao Versao do QR Code
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
     * @param string $versao Versao do QR Code
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
     * Obtem configuracoes
     *
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return $this->context->getConfig();
    }

    /**
     * Obtem configuracao NFe
     *
     * @return array<string, mixed>
     */
    public function getNFeConfig(): array
    {
        return $this->context->getNFeConfig();
    }

    /**
     * Define configuracoes
     *
     * @param array<string, mixed> $config
     */
    public function setConfig(array $config): void
    {
        $this->context->setConfig($config);
    }

    /**
     * Define configuracao NFe
     *
     * @param array<string, mixed> $config
     */
    public function setNFeConfig(array $config): void
    {
        $this->context->setNFeConfig($config);
    }
}
