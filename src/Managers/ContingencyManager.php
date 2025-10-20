<?php

namespace DiogoGraciano\Nfephp\Managers;

use Exception;
use NFePHP\NFe\Factories\Contingency;
use NFePHP\NFe\Factories\ContingencyNFe;

class ContingencyManager
{
    /**
     * Instância da contingência
     */
    protected ?Contingency $contingency = null;

    /**
     * Ativa modo de contingência
     *
     * @param string $acronym Sigla do estado (ex: SP, RJ, MG)
     * @param string $motive Motivo da contingência (15-255 caracteres)
     * @param string $type Tipo de contingência (SVCAN, SVCRS, ou vazio para automático)
     * @return string JSON da configuração de contingência
     * @throws Exception
     */
    public function activate(string $acronym, string $motive, string $type = ''): string
    {
        try {
            $this->contingency = new Contingency();
            $result = $this->contingency->activate($acronym, $motive, $type);

            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao ativar contingência: " . $e->getMessage());
        }
    }

    /**
     * Desativa modo de contingência
     *
     * @return string JSON da configuração de contingência (desativada)
     */
    public function deactivate(): string
    {
        if ($this->contingency) {
            $result = $this->contingency->deactivate();
        } else {
            $this->contingency = new Contingency();
            $result = $this->contingency->deactivate();
        }

        return $result;
    }

    /**
     * Verifica se está em modo de contingência
     */
    public function isActive(): bool
    {
        return $this->contingency && $this->contingency->type !== '';
    }

    /**
     * Obtém informações da contingência ativa
     *
     * @return array<string, mixed>|null
     */
    public function getInfo(): ?array
    {
        if (! $this->isActive() || ! $this->contingency) {
            return null;
        }

        return [
            'type' => $this->contingency->type,
            'motive' => $this->contingency->motive,
            'timestamp' => $this->contingency->timestamp,
            'tpEmis' => $this->contingency->tpEmis,
        ];
    }

    /**
     * Ajusta XML para modo de contingência
     *
     * @param string $xml XML da NFe
     * @return string XML ajustado para contingência
     * @throws Exception
     */
    public function adjustXml(string $xml): string
    {
        if (! $this->isActive() || ! $this->contingency) {
            throw new Exception("Nenhuma contingência ativa. Use activate() primeiro.");
        }

        try {
            return ContingencyNFe::adjust($xml, $this->contingency);
        } catch (Exception $e) {
            throw new Exception("Erro ao ajustar XML para contingência: " . $e->getMessage());
        }
    }

    /**
     * Carrega contingência a partir de JSON
     *
     * @param string $contingencyJson JSON da configuração de contingência
     * @throws Exception
     */
    public function load(string $contingencyJson): void
    {
        try {
            $this->contingency = new Contingency();
            $this->contingency->load($contingencyJson);
        } catch (Exception $e) {
            throw new Exception("Erro ao carregar contingência: " . $e->getMessage());
        }
    }

    /**
     * Obtém a instância da contingência
     */
    public function getContingency(): ?Contingency
    {
        return $this->contingency;
    }
}
