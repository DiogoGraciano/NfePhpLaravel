<?php

namespace DiogoGraciano\Nfephp\Helpers;

use NFePHP\Common\Strings;

class StringHelper
{
    /**
     * Limpa caracteres não aceitos em strings
     *
     * @param string $string String a ser limpa
     * @return string String limpa
     */
    public static function clean(string $string): string
    {
        $result = Strings::replaceUnacceptableCharacters($string);

        return $result ?? '';
    }

    /**
     * Converte string para ASCII
     *
     * @param string $string String a ser convertida
     * @return string String em ASCII
     */
    public static function toAscii(string $string): string
    {
        return Strings::toASCII($string);
    }

    /**
     * Equilibra parâmetros em um objeto stdClass
     *
     * @param \stdClass $std Objeto a ser equilibrado
     * @param array $possible Array com chaves possíveis
     * @param bool $replaceAccentedChars Se deve substituir caracteres acentuados
     * @return \stdClass Objeto equilibrado
     */
    public static function equilizeParameters(
        \stdClass $std,
        array $possible,
        bool $replaceAccentedChars = false
    ): \stdClass {
        return Strings::equilizeParameters($std, $possible, $replaceAccentedChars);
    }

    /**
     * Formata CNPJ
     *
     * @param string $cnpj CNPJ sem formatação
     * @return string CNPJ formatado
     */
    public static function formatCnpj(string $cnpj): string
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);
        if (strlen($cnpj) !== 14) {
            return $cnpj;
        }
        $result = preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);

        return $result ?: $cnpj;
    }

    /**
     * Formata CPF
     *
     * @param string $cpf CPF sem formatação
     * @return string CPF formatado
     */
    public static function formatCpf(string $cpf): string
    {
        $cpf = preg_replace('/\D/', '', $cpf);
        if (strlen($cpf) !== 11) {
            return $cpf;
        }
        $result = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);

        return $result ?: $cpf;
    }

    /**
     * Formata CEP
     *
     * @param string $cep CEP sem formatação
     * @return string CEP formatado
     */
    public static function formatCep(string $cep): string
    {
        $cep = preg_replace('/\D/', '', $cep);
        if (strlen($cep) !== 8) {
            return $cep;
        }

        return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $cep);
    }

    /**
     * Formata telefone
     *
     * @param string $phone Telefone sem formatação
     * @return string Telefone formatado
     */
    public static function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (strlen($phone) === 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $phone);
        } elseif (strlen($phone) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $phone);
        }

        return $phone;
    }

    /**
     * Remove formatação de CNPJ/CPF
     *
     * @param string $document Documento formatado
     * @return string Documento sem formatação
     */
    public static function unformatDocument(string $document): string
    {
        return preg_replace('/\D/', '', $document);
    }

    /**
     * Remove formatação de CEP
     *
     * @param string $cep CEP formatado
     * @return string CEP sem formatação
     */
    public static function unformatCep(string $cep): string
    {
        return preg_replace('/\D/', '', $cep);
    }

    /**
     * Remove formatação de telefone
     *
     * @param string $phone Telefone formatado
     * @return string Telefone sem formatação
     */
    public static function unformatPhone(string $phone): string
    {
        return preg_replace('/\D/', '', $phone);
    }

    /**
     * Gera string aleatória
     *
     * @param int $length Tamanho da string
     * @param string $characters Caracteres permitidos
     * @return string String aleatória
     */
    public static function random(
        int $length = 10,
        string $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ): string {
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * Converte string para maiúscula
     *
     * @param string $string String a ser convertida
     * @return string String em maiúscula
     */
    public static function upper(string $string): string
    {
        return mb_strtoupper($string, 'UTF-8');
    }

    /**
     * Converte string para minúscula
     *
     * @param string $string String a ser convertida
     * @return string String em minúscula
     */
    public static function lower(string $string): string
    {
        return mb_strtolower($string, 'UTF-8');
    }

    /**
     * Converte primeira letra para maiúscula
     *
     * @param string $string String a ser convertida
     * @return string String com primeira letra maiúscula
     */
    public static function capitalize(string $string): string
    {
        return mb_convert_case($string, MB_CASE_TITLE, 'UTF-8');
    }
}
