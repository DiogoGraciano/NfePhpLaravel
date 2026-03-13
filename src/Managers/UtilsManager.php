<?php

namespace DiogoGraciano\Nfephp\Managers;

use DiogoGraciano\Nfephp\Helpers\StringHelper;
use DiogoGraciano\Nfephp\Helpers\UfHelper;
use DiogoGraciano\Nfephp\Helpers\ValidationHelper;

class UtilsManager
{
    // ============================================
    // VALIDACAO
    // ============================================

    /**
     * Valida XML contra um schema XSD
     *
     * @param string $xml Conteudo XML
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
     */
    public function validateCnpj(string $cnpj): bool
    {
        return ValidationHelper::validateCnpj($cnpj);
    }

    /**
     * Valida CPF
     */
    public function validateCpf(string $cpf): bool
    {
        return ValidationHelper::validateCpf($cpf);
    }

    /**
     * Valida CEP
     */
    public function validateCep(string $cep): bool
    {
        return ValidationHelper::validateCep($cep);
    }

    /**
     * Valida email
     */
    public function validateEmail(string $email): bool
    {
        return ValidationHelper::validateEmail($email);
    }

    /**
     * Valida telefone
     */
    public function validatePhone(string $phone): bool
    {
        return ValidationHelper::validatePhone($phone);
    }

    /**
     * Valida chave de acesso da NFe
     */
    public function validateNFeKey(string $key): bool
    {
        return ValidationHelper::validateNFeKey($key);
    }

    // ============================================
    // STRINGS
    // ============================================

    /**
     * Limpa caracteres nao aceitos em strings
     */
    public function cleanString(string $string): string
    {
        return StringHelper::clean($string);
    }

    /**
     * Converte string para ASCII
     */
    public function stringToAscii(string $string): string
    {
        return StringHelper::toAscii($string);
    }

    /**
     * Equilibra parametros em um objeto stdClass
     *
     * @param \stdClass $std Objeto a ser equilibrado
     * @param array $possible Array com chaves possiveis
     * @param bool $replaceAccentedChars Se deve substituir caracteres acentuados
     * @return \stdClass Objeto equilibrado
     */
    public function equilizeParameters(\stdClass $std, array $possible, bool $replaceAccentedChars = false): \stdClass
    {
        return StringHelper::equilizeParameters($std, $possible, $replaceAccentedChars);
    }

    /**
     * Formata CNPJ
     */
    public function formatCnpj(string $cnpj): string
    {
        return StringHelper::formatCnpj($cnpj);
    }

    /**
     * Formata CPF
     */
    public function formatCpf(string $cpf): string
    {
        return StringHelper::formatCpf($cpf);
    }

    /**
     * Formata CEP
     */
    public function formatCep(string $cep): string
    {
        return StringHelper::formatCep($cep);
    }

    /**
     * Formata telefone
     */
    public function formatPhone(string $phone): string
    {
        return StringHelper::formatPhone($phone);
    }

    /**
     * Remove formatacao de CNPJ/CPF
     */
    public function unformatDocument(string $document): string
    {
        return StringHelper::unformatDocument($document);
    }

    /**
     * Remove formatacao de CEP
     */
    public function unformatCep(string $cep): string
    {
        return StringHelper::unformatCep($cep);
    }

    /**
     * Remove formatacao de telefone
     */
    public function unformatPhone(string $phone): string
    {
        return StringHelper::unformatPhone($phone);
    }

    /**
     * Gera string aleatoria
     */
    public function random(int $length = 10, string $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): string
    {
        return StringHelper::random($length, $characters);
    }

    /**
     * Converte string para maiuscula
     */
    public function upper(string $string): string
    {
        return StringHelper::upper($string);
    }

    /**
     * Converte string para minuscula
     */
    public function lower(string $string): string
    {
        return StringHelper::lower($string);
    }

    /**
     * Converte primeira letra para maiuscula
     */
    public function capitalize(string $string): string
    {
        return StringHelper::capitalize($string);
    }

    // ============================================
    // UF
    // ============================================

    /**
     * Obtem codigo da UF pela sigla
     */
    public function getUfCode(string $uf): int
    {
        return UfHelper::getCode($uf);
    }

    /**
     * Obtem sigla da UF pelo codigo
     */
    public function getUfByCode(int $code): string
    {
        return UfHelper::getByCode($code);
    }

    /**
     * Obtem timezone da UF
     */
    public function getTimezoneByUf(string $uf): string
    {
        return UfHelper::getTimezone($uf);
    }

    /**
     * Lista todas as UFs disponiveis
     *
     * @return array<string, int>
     */
    public function getAllUfs(): array
    {
        return UfHelper::getAll();
    }

    /**
     * Verifica se a sigla da UF e valida
     */
    public function isValidUf(string $uf): bool
    {
        return UfHelper::isValid($uf);
    }

    /**
     * Gera chave de acesso da NFe
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

    /**
     * Obtem nome completo da UF
     */
    public function getUfFullName(string $uf): string
    {
        return UfHelper::getFullName($uf);
    }

    /**
     * Obtem regiao da UF
     */
    public function getUfRegion(string $uf): string
    {
        return UfHelper::getRegion($uf);
    }

    /**
     * Extrai informacoes da chave de acesso
     *
     * @return array<string, mixed>|null
     */
    public function parseNFeKey(string $key): ?array
    {
        return UfHelper::parseNFeKey($key);
    }
}
