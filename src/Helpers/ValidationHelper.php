<?php

namespace DiogoGraciano\Nfephp\Helpers;

use NFePHP\Common\Keys;
use NFePHP\Common\Validator;

class ValidationHelper
{
    /**
     * Valida XML contra um schema XSD
     *
     * @param string $xml Conteúdo XML
     * @param string $xsd Caminho para o arquivo XSD
     * @return bool
     * @throws \Exception
     */
    public static function validateXml(string $xml, string $xsd): bool
    {
        try {
            return Validator::isValid($xml, $xsd);
        } catch (\Exception $e) {
            throw new \Exception("Erro na validação XML: " . $e->getMessage());
        }
    }

    /**
     * Valida chave de acesso da NFe
     *
     * @param string $key Chave de acesso
     * @return bool
     */
    public static function validateNFeKey(string $key): bool
    {
        return Keys::isValid($key);
    }

    /**
     * Valida CNPJ
     *
     * @param string $cnpj CNPJ a ser validado
     * @return bool
     */
    public static function validateCnpj(string $cnpj): bool
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);

        if (strlen($cnpj) !== 14) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        // Validação do primeiro dígito verificador
        $sum = 0;
        $weight = 5;
        for ($i = 0; $i < 12; $i++) {
            $sum += intval($cnpj[$i]) * $weight;
            $weight = $weight === 2 ? 9 : $weight - 1;
        }
        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;

        if (intval($cnpj[12]) !== $digit1) {
            return false;
        }

        // Validação do segundo dígito verificador
        $sum = 0;
        $weight = 6;
        for ($i = 0; $i < 13; $i++) {
            $sum += intval($cnpj[$i]) * $weight;
            $weight = $weight === 2 ? 9 : $weight - 1;
        }
        $remainder = $sum % 11;
        $digit2 = $remainder < 2 ? 0 : 11 - $remainder;

        return intval($cnpj[13]) === $digit2;
    }

    /**
     * Valida CPF
     *
     * @param string $cpf CPF a ser validado
     * @return bool
     */
    public static function validateCpf(string $cpf): bool
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        if (strlen($cpf) !== 11) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Validação do primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += intval($cpf[$i]) * (10 - $i);
        }
        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;

        if (intval($cpf[9]) !== $digit1) {
            return false;
        }

        // Validação do segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += intval($cpf[$i]) * (11 - $i);
        }
        $remainder = $sum % 11;
        $digit2 = $remainder < 2 ? 0 : 11 - $remainder;

        return intval($cpf[10]) === $digit2;
    }

    /**
     * Valida CEP
     *
     * @param string $cep CEP a ser validado
     * @return bool
     */
    public static function validateCep(string $cep): bool
    {
        $cep = preg_replace('/\D/', '', $cep);

        return strlen($cep) === 8;
    }

    /**
     * Valida email
     *
     * @param string $email Email a ser validado
     * @return bool
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valida telefone
     *
     * @param string $phone Telefone a ser validado
     * @return bool
     */
    public static function validatePhone(string $phone): bool
    {
        $phone = preg_replace('/\D/', '', $phone);

        return strlen($phone) >= 10 && strlen($phone) <= 11;
    }
}
