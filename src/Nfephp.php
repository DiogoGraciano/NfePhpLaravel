<?php

namespace DiogoGraciano\Nfephp;

use DiogoGraciano\Nfephp\Helpers\StringHelper;
use DiogoGraciano\Nfephp\Helpers\UfHelper;
use DiogoGraciano\Nfephp\Helpers\ValidationHelper;

class Nfephp extends NfephpCore
{
    // ============================================
    // MÉTODOS DE CONVENIÊNCIA PARA CONTINGÊNCIAS
    // ============================================

    /**
     * Ativa modo de contingência
     *
     * @param string $acronym Sigla do estado (ex: SP, RJ, MG)
     * @param string $motive Motivo da contingência (15-255 caracteres)
     * @param string $type Tipo de contingência (SVCAN, SVCRS, ou vazio para automático)
     * @return string JSON da configuração de contingência
     * @throws \Exception
     */
    public function activateContingency(string $acronym, string $motive, string $type = ''): string
    {
        $result = $this->contingencyManager->activate($acronym, $motive, $type);

        // Reinicializa o Tools com a contingência ativa
        $this->initializeNFePHP();

        return $result;
    }

    /**
     * Desativa modo de contingência
     *
     * @return string JSON da configuração de contingência (desativada)
     */
    public function deactivateContingency(): string
    {
        $result = $this->contingencyManager->deactivate();

        // Reinicializa o Tools sem contingência
        $this->initializeNFePHP();

        return $result;
    }

    /**
     * Verifica se está em modo de contingência
     */
    public function isInContingency(): bool
    {
        return $this->contingencyManager->isActive();
    }

    /**
     * Obtém informações da contingência ativa
     *
     * @return array<string, mixed>|null
     */
    public function getContingencyInfo(): ?array
    {
        return $this->contingencyManager->getInfo();
    }

    /**
     * Ajusta XML para modo de contingência
     *
     * @param string $xml XML da NFe
     * @return string XML ajustado para contingência
     * @throws \Exception
     */
    public function adjustXmlForContingency(string $xml): string
    {
        return $this->contingencyManager->adjustXml($xml);
    }

    /**
     * Carrega contingência a partir de JSON
     *
     * @param string $contingencyJson JSON da configuração de contingência
     * @throws \Exception
     */
    public function loadContingency(string $contingencyJson): void
    {
        $this->contingencyManager->load($contingencyJson);

        // Reinicializa o Tools com a contingência carregada
        $this->initializeNFePHP();
    }

    // ============================================
    // MÉTODOS DE CONVENIÊNCIA PARA CERTIFICADOS
    // ============================================

    /**
     * Obtém informações do certificado digital
     *
     * @return array<string, mixed>|null
     */
    public function getCertificateInfo(): ?array
    {
        return $this->certificateManager->getInfo();
    }

    /**
     * Verifica se o certificado está válido
     *
     * @return bool
     */
    public function isCertificateValid(): bool
    {
        return $this->certificateManager->isValid();
    }

    /**
     * Obtém dias restantes para expiração do certificado
     *
     * @return int|null Número de dias ou null se não conseguir calcular
     */
    public function getCertificateDaysToExpire(): ?int
    {
        return $this->certificateManager->getDaysToExpire();
    }

    // ============================================
    // MÉTODOS DE CONVENIÊNCIA PARA VALIDAÇÃO
    // ============================================

    /**
     * Valida XML contra um schema XSD
     *
     * @param string $xml Conteúdo XML
     * @param string $xsd Caminho para o arquivo XSD
     * @return bool
     * @throws \Exception
     */
    public function validateXml(string $xml, string $xsd): bool
    {
        return ValidationHelper::validateXml($xml, $xsd);
    }

    /**
     * Valida CNPJ
     *
     * @param string $cnpj CNPJ a ser validado
     * @return bool
     */
    public function validateCnpj(string $cnpj): bool
    {
        return ValidationHelper::validateCnpj($cnpj);
    }

    /**
     * Valida CPF
     *
     * @param string $cpf CPF a ser validado
     * @return bool
     */
    public function validateCpf(string $cpf): bool
    {
        return ValidationHelper::validateCpf($cpf);
    }

    /**
     * Valida chave de acesso da NFe
     *
     * @param string $key Chave de acesso
     * @return bool
     */
    public function validateNFeKey(string $key): bool
    {
        return ValidationHelper::validateNFeKey($key);
    }

    // ============================================
    // MÉTODOS DE CONVENIÊNCIA PARA STRINGS
    // ============================================

    /**
     * Limpa caracteres não aceitos em strings
     *
     * @param string $string String a ser limpa
     * @return string String limpa
     */
    public function cleanString(string $string): string
    {
        return StringHelper::clean($string);
    }

    /**
     * Converte string para ASCII
     *
     * @param string $string String a ser convertida
     * @return string String em ASCII
     */
    public function stringToAscii(string $string): string
    {
        return StringHelper::toAscii($string);
    }

    /**
     * Equilibra parâmetros em um objeto stdClass
     *
     * @param \stdClass $std Objeto a ser equilibrado
     * @param array $possible Array com chaves possíveis
     * @param bool $replaceAccentedChars Se deve substituir caracteres acentuados
     * @return \stdClass Objeto equilibrado
     */
    public function equilizeParameters(\stdClass $std, array $possible, bool $replaceAccentedChars = false): \stdClass
    {
        return StringHelper::equilizeParameters($std, $possible, $replaceAccentedChars);
    }

    /**
     * Formata CNPJ
     *
     * @param string $cnpj CNPJ sem formatação
     * @return string CNPJ formatado
     */
    public function formatCnpj(string $cnpj): string
    {
        return StringHelper::formatCnpj($cnpj);
    }

    /**
     * Formata CPF
     *
     * @param string $cpf CPF sem formatação
     * @return string CPF formatado
     */
    public function formatCpf(string $cpf): string
    {
        return StringHelper::formatCpf($cpf);
    }

    // ============================================
    // MÉTODOS DE CONVENIÊNCIA PARA UF
    // ============================================

    /**
     * Obtém código da UF pela sigla
     *
     * @param string $uf Sigla da UF (ex: SP, RJ, MG)
     * @return int Código da UF
     */
    public function getUfCode(string $uf): int
    {
        return UfHelper::getCode($uf);
    }

    /**
     * Obtém sigla da UF pelo código
     *
     * @param int $code Código da UF
     * @return string Sigla da UF
     */
    public function getUfByCode(int $code): string
    {
        return UfHelper::getByCode($code);
    }

    /**
     * Obtém timezone da UF
     *
     * @param string $uf Sigla da UF
     * @return string Timezone da UF
     */
    public function getTimezoneByUf(string $uf): string
    {
        return UfHelper::getTimezone($uf);
    }

    /**
     * Gera chave de acesso da NFe
     *
     * @param string $cUF Código da UF
     * @param string $aamm Ano e mês
     * @param string $cnpj CNPJ do emitente
     * @param string $mod Modelo do documento
     * @param string $serie Série
     * @param string $nNF Número da NFe
     * @param string $tpEmis Tipo de emissão
     * @param string $cNF Código numérico
     * @return string Chave de acesso
     */
    public function generateNFeKey(
        string $cUF,
        string $aamm,
        string $cnpj,
        string $mod,
        string $serie,
        string $nNF,
        string $tpEmis,
        string $cNF
    ): string {
        return UfHelper::generateNFeKey($cUF, $aamm, $cnpj, $mod, $serie, $nNF, $tpEmis, $cNF);
    }
}
