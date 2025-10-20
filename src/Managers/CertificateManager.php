<?php

namespace DiogoGraciano\Nfephp\Managers;

use Exception;
use NFePHP\Common\Certificate;

class CertificateManager
{
    /**
     * Instância do certificado digital
     */
    protected ?Certificate $certificate = null;

    /**
     * Construtor
     */
    public function __construct(?Certificate $certificate = null)
    {
        $this->certificate = $certificate;
    }

    /**
     * Define a instância do certificado
     */
    public function setCertificate(?Certificate $certificate): void
    {
        $this->certificate = $certificate;
    }

    /**
     * Obtém informações do certificado digital
     *
     * @return array<string, mixed>|null
     */
    public function getInfo(): ?array
    {
        if (! $this->certificate) {
            return null;
        }

        try {
            return [
                'cnpj' => $this->certificate->getCnpj(),
                'cpf' => $this->certificate->getCpf(),
                'name' => $this->certificate->getCompanyName(),
                'valid_from' => $this->certificate->getValidFrom()?->format('Y-m-d H:i:s'),
                'valid_to' => $this->certificate->getValidTo()?->format('Y-m-d H:i:s'),
                'icp' => $this->certificate->getICP(),
                'ca_url' => $this->certificate->getCAurl(),
                'csp' => $this->certificate->getCSP(),
            ];
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Verifica se o certificado está válido
     *
     * @return bool
     */
    public function isValid(): bool
    {
        if (! $this->certificate) {
            return false;
        }

        try {
            return ! $this->certificate->isExpired();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Obtém dias restantes para expiração do certificado
     *
     * @return int|null Número de dias ou null se não conseguir calcular
     */
    public function getDaysToExpire(): ?int
    {
        if (! $this->certificate) {
            return null;
        }

        try {
            $now = new \DateTime();
            $validTo = $this->certificate->getValidTo();
            if (! $validTo) {
                return null;
            }

            $diff = $now->diff($validTo);

            return $diff->invert ? 0 : (int) $diff->days;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Verifica se o certificado está próximo do vencimento
     *
     * @param int $daysThreshold Dias de antecedência para considerar próximo do vencimento
     * @return bool
     */
    public function isNearExpiration(int $daysThreshold = 30): bool
    {
        $daysToExpire = $this->getDaysToExpire();

        if ($daysToExpire === null) {
            return false;
        }

        return $daysToExpire <= $daysThreshold;
    }

    /**
     * Obtém o CNPJ do certificado
     *
     * @return string|null
     */
    public function getCnpj(): ?string
    {
        if (! $this->certificate) {
            return null;
        }

        try {
            return $this->certificate->getCnpj();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Obtém o CPF do certificado
     *
     * @return string|null
     */
    public function getCpf(): ?string
    {
        if (! $this->certificate) {
            return null;
        }

        try {
            return $this->certificate->getCpf();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Obtém o nome da empresa do certificado
     *
     * @return string|null
     */
    public function getCompanyName(): ?string
    {
        if (! $this->certificate) {
            return null;
        }

        try {
            return $this->certificate->getCompanyName();
        } catch (Exception $e) {
            return null;
        }
    }
}
