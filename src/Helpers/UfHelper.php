<?php

namespace DiogoGraciano\Nfephp\Helpers;

use NFePHP\Common\Keys;
use NFePHP\Common\TimeZoneByUF;
use NFePHP\Common\UFList;

class UfHelper
{
    /**
     * Obtém código da UF pela sigla
     *
     * @param string $uf Sigla da UF (ex: SP, RJ, MG)
     * @return int Código da UF
     */
    public static function getCode(string $uf): int
    {
        return UFList::getCodeByUF($uf);
    }

    /**
     * Obtém sigla da UF pelo código
     *
     * @param int $code Código da UF
     * @return string Sigla da UF
     */
    public static function getByCode(int $code): string
    {
        return UFList::getUFByCode($code);
    }

    /**
     * Obtém timezone da UF
     *
     * @param string $uf Sigla da UF
     * @return string Timezone da UF
     */
    public static function getTimezone(string $uf): string
    {
        return TimeZoneByUF::get($uf);
    }

    /**
     * Lista todas as UFs disponíveis
     *
     * @return array<string, int> Array com sigla => código
     */
    public static function getAll(): array
    {
        return [
            'AC' => 12, 'AL' => 27, 'AP' => 16, 'AM' => 13, 'BA' => 29,
            'CE' => 23, 'DF' => 53, 'ES' => 32, 'GO' => 52, 'MA' => 21,
            'MT' => 51, 'MS' => 50, 'MG' => 31, 'PA' => 15, 'PB' => 25,
            'PR' => 41, 'PE' => 26, 'PI' => 22, 'RJ' => 33, 'RN' => 24,
            'RS' => 43, 'RO' => 11, 'RR' => 14, 'SC' => 42, 'SP' => 35,
            'SE' => 28, 'TO' => 17,
        ];
    }

    /**
     * Verifica se a sigla da UF é válida
     *
     * @param string $uf Sigla da UF
     * @return bool
     */
    public static function isValid(string $uf): bool
    {
        $ufs = self::getAll();

        return array_key_exists(strtoupper($uf), $ufs);
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
    public static function generateNFeKey(
        string $cUF,
        string $aamm,
        string $cnpj,
        string $mod,
        string $serie,
        string $nNF,
        string $tpEmis,
        string $cNF
    ): string {
        return Keys::build(
            $cUF,
            $aamm,
            $cnpj,
            $mod,
            $serie,
            $nNF,
            $tpEmis,
            $cNF
        );
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
     * Extrai informações da chave de acesso
     *
     * @param string $key Chave de acesso
     * @return array<string, mixed>|null
     */
    public static function parseNFeKey(string $key): ?array
    {
        if (! self::validateNFeKey($key)) {
            return null;
        }

        return [
            'cUF' => substr($key, 0, 2),
            'aamm' => substr($key, 2, 4),
            'cnpj' => substr($key, 6, 14),
            'mod' => substr($key, 20, 2),
            'serie' => substr($key, 22, 3),
            'nNF' => substr($key, 25, 9),
            'tpEmis' => substr($key, 34, 1),
            'cNF' => substr($key, 35, 8),
            'dv' => substr($key, 43, 1),
        ];
    }

    /**
     * Obtém nome completo da UF
     *
     * @param string $uf Sigla da UF
     * @return string Nome completo da UF
     */
    public static function getFullName(string $uf): string
    {
        $ufs = [
            'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá', 'AM' => 'Amazonas',
            'BA' => 'Bahia', 'CE' => 'Ceará', 'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo',
            'GO' => 'Goiás', 'MA' => 'Maranhão', 'MT' => 'Mato Grosso', 'MS' => 'Mato Grosso do Sul',
            'MG' => 'Minas Gerais', 'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná',
            'PE' => 'Pernambuco', 'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro', 'RN' => 'Rio Grande do Norte',
            'RS' => 'Rio Grande do Sul', 'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina',
            'SP' => 'São Paulo', 'SE' => 'Sergipe', 'TO' => 'Tocantins',
        ];

        return $ufs[strtoupper($uf)] ?? '';
    }

    /**
     * Obtém região da UF
     *
     * @param string $uf Sigla da UF
     * @return string Região da UF
     */
    public static function getRegion(string $uf): string
    {
        $regions = [
            'Norte' => ['AC', 'AM', 'AP', 'PA', 'RO', 'RR', 'TO'],
            'Nordeste' => ['AL', 'BA', 'CE', 'MA', 'PB', 'PE', 'PI', 'RN', 'SE'],
            'Centro-Oeste' => ['DF', 'GO', 'MT', 'MS'],
            'Sudeste' => ['ES', 'MG', 'RJ', 'SP'],
            'Sul' => ['PR', 'RS', 'SC'],
        ];

        foreach ($regions as $region => $ufs) {
            if (in_array(strtoupper($uf), $ufs)) {
                return $region;
            }
        }

        return '';
    }
}
