<?php

namespace DiogoGraciano\Nfephp\Managers;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use NFePHP\DA\NFe\Danfe;
use NFePHP\DA\NFe\Danfce;
use NFePHP\DA\NFe\DanfeSimples;
use NFePHP\DA\NFe\Daevento;

class DanfeManager
{
    /**
     * Caminho do logo para o cabeçalho do DANFE
     */
    protected ?string $logoPath = null;

    /**
     * Construtor
     */
    public function __construct(?string $logoPath = null)
    {
        $this->logoPath = $logoPath;
    }

    /**
     * Define o logo do DANFE
     */
    public function setLogo(?string $logoPath): void
    {
        $this->logoPath = $logoPath;
    }

    /**
     * Gera PDF do DANFE a partir do XML da NFe
     *
     * @param string $xml XML autorizado da NFe
     * @return string Conteúdo binário do PDF
     * @throws Exception
     */
    public function generateDanfe(string $xml): string
    {
        try {
            $danfe = new Danfe($xml);
            if ($this->logoPath) {
                $danfe->logoParameters($this->logoPath);
            }

            return $danfe->render();
        } catch (\Throwable $e) {
            throw new Exception("Erro ao gerar DANFE: " . $e->getMessage());
        }
    }

    /**
     * Gera PDF do DANFE e salva usando Storage do Laravel
     *
     * @param string $xml XML autorizado da NFe
     * @param string $filePath Caminho relativo ao disco
     * @param string $disk Disco do Storage (default: disco padrão)
     * @throws Exception
     */
    public function saveDanfe(string $xml, string $filePath, string $disk = ''): void
    {
        $pdf = $this->generateDanfe($xml);
        $storage = $disk ? Storage::disk($disk) : Storage::disk();

        if (! $storage->put($filePath, $pdf)) {
            throw new Exception("Erro ao salvar DANFE em: " . $filePath);
        }
    }

    /**
     * Retorna Response HTTP com o PDF do DANFE para download
     *
     * @param string $xml XML autorizado da NFe
     * @param string $filename Nome do arquivo para download
     * @return Response
     * @throws Exception
     */
    public function downloadDanfe(string $xml, string $filename = 'danfe.pdf'): Response
    {
        $pdf = $this->generateDanfe($xml);

        return $this->pdfResponse($pdf, $filename, 'attachment');
    }

    /**
     * Retorna Response HTTP com o PDF do DANFE para visualização inline
     *
     * @param string $xml XML autorizado da NFe
     * @param string $filename Nome do arquivo
     * @return Response
     * @throws Exception
     */
    public function renderDanfe(string $xml, string $filename = 'danfe.pdf'): Response
    {
        $pdf = $this->generateDanfe($xml);

        return $this->pdfResponse($pdf, $filename, 'inline');
    }

    /**
     * Gera PDF do DANFE para NFCe
     *
     * @param string $xml XML autorizado da NFCe
     * @return string Conteúdo binário do PDF
     * @throws Exception
     */
    public function generateDanfce(string $xml): string
    {
        try {
            $danfce = new Danfce($xml);
            if ($this->logoPath) {
                $danfce->logoParameters($this->logoPath);
            }

            return $danfce->render();
        } catch (\Throwable $e) {
            throw new Exception("Erro ao gerar DANFCe: " . $e->getMessage());
        }
    }

    /**
     * Gera PDF do DANFE para NFCe e salva usando Storage do Laravel
     *
     * @param string $xml XML autorizado da NFCe
     * @param string $filePath Caminho relativo ao disco
     * @param string $disk Disco do Storage (default: disco padrão)
     * @throws Exception
     */
    public function saveDanfce(string $xml, string $filePath, string $disk = ''): void
    {
        $pdf = $this->generateDanfce($xml);
        $storage = $disk ? Storage::disk($disk) : Storage::disk();

        if (! $storage->put($filePath, $pdf)) {
            throw new Exception("Erro ao salvar DANFCe em: " . $filePath);
        }
    }

    /**
     * Retorna Response HTTP com o PDF da DANFCe para download
     *
     * @param string $xml XML autorizado da NFCe
     * @param string $filename Nome do arquivo para download
     * @return Response
     * @throws Exception
     */
    public function downloadDanfce(string $xml, string $filename = 'danfce.pdf'): Response
    {
        $pdf = $this->generateDanfce($xml);

        return $this->pdfResponse($pdf, $filename, 'attachment');
    }

    /**
     * Retorna Response HTTP com o PDF da DANFCe para visualização inline
     *
     * @param string $xml XML autorizado da NFCe
     * @param string $filename Nome do arquivo
     * @return Response
     * @throws Exception
     */
    public function renderDanfce(string $xml, string $filename = 'danfce.pdf'): Response
    {
        $pdf = $this->generateDanfce($xml);

        return $this->pdfResponse($pdf, $filename, 'inline');
    }

    /**
     * Gera PDF do DANFE simplificado
     *
     * @param string $xml XML autorizado da NFe
     * @return string Conteúdo binário do PDF
     * @throws Exception
     */
    public function generateDanfeSimples(string $xml): string
    {
        try {
            $danfe = new DanfeSimples($xml);
            if ($this->logoPath) {
                $danfe->logoParameters($this->logoPath);
            }

            return $danfe->render();
        } catch (\Throwable $e) {
            throw new Exception("Erro ao gerar DANFE simplificado: " . $e->getMessage());
        }
    }

    /**
     * Gera PDF do DANFE simplificado e salva usando Storage do Laravel
     *
     * @param string $xml XML autorizado da NFe
     * @param string $filePath Caminho relativo ao disco
     * @param string $disk Disco do Storage (default: disco padrão)
     * @throws Exception
     */
    public function saveDanfeSimples(string $xml, string $filePath, string $disk = ''): void
    {
        $pdf = $this->generateDanfeSimples($xml);
        $storage = $disk ? Storage::disk($disk) : Storage::disk();

        if (! $storage->put($filePath, $pdf)) {
            throw new Exception("Erro ao salvar DANFE simplificado em: " . $filePath);
        }
    }

    /**
     * Retorna Response HTTP com o PDF do DANFE simplificado para download
     *
     * @param string $xml XML autorizado da NFe
     * @param string $filename Nome do arquivo para download
     * @return Response
     * @throws Exception
     */
    public function downloadDanfeSimples(string $xml, string $filename = 'danfe_simples.pdf'): Response
    {
        $pdf = $this->generateDanfeSimples($xml);

        return $this->pdfResponse($pdf, $filename, 'attachment');
    }

    /**
     * Retorna Response HTTP com o PDF do DANFE simplificado para visualização inline
     *
     * @param string $xml XML autorizado da NFe
     * @param string $filename Nome do arquivo
     * @return Response
     * @throws Exception
     */
    public function renderDanfeSimples(string $xml, string $filename = 'danfe_simples.pdf'): Response
    {
        $pdf = $this->generateDanfeSimples($xml);

        return $this->pdfResponse($pdf, $filename, 'inline');
    }

    /**
     * Gera PDF do documento de evento (cancelamento, CCe, etc.)
     *
     * @param string $xml XML do evento
     * @param array<string, string> $dadosEmitente Dados do emitente (razao, logradouro, numero, complemento, bairro, CEP, municipio, UF, telefone, email)
     * @return string Conteúdo binário do PDF
     * @throws Exception
     */
    public function generateDaevento(string $xml, array $dadosEmitente = []): string
    {
        try {
            $daevento = new Daevento($xml, $dadosEmitente);
            if ($this->logoPath) {
                $daevento->logoParameters($this->logoPath);
            }

            return $daevento->render();
        } catch (\Throwable $e) {
            throw new Exception("Erro ao gerar documento de evento: " . $e->getMessage());
        }
    }

    /**
     * Gera PDF do documento de evento e salva usando Storage do Laravel
     *
     * @param string $xml XML do evento
     * @param string $filePath Caminho relativo ao disco
     * @param array<string, string> $dadosEmitente Dados do emitente
     * @param string $disk Disco do Storage (default: disco padrão)
     * @throws Exception
     */
    public function saveDaevento(string $xml, string $filePath, array $dadosEmitente = [], string $disk = ''): void
    {
        $pdf = $this->generateDaevento($xml, $dadosEmitente);
        $storage = $disk ? Storage::disk($disk) : Storage::disk();

        if (! $storage->put($filePath, $pdf)) {
            throw new Exception("Erro ao salvar documento de evento em: " . $filePath);
        }
    }

    /**
     * Retorna Response HTTP com o PDF do evento para download
     *
     * @param string $xml XML do evento
     * @param array<string, string> $dadosEmitente Dados do emitente
     * @param string $filename Nome do arquivo para download
     * @return Response
     * @throws Exception
     */
    public function downloadDaevento(string $xml, array $dadosEmitente = [], string $filename = 'evento.pdf'): Response
    {
        $pdf = $this->generateDaevento($xml, $dadosEmitente);

        return $this->pdfResponse($pdf, $filename, 'attachment');
    }

    /**
     * Retorna Response HTTP com o PDF do evento para visualização inline
     *
     * @param string $xml XML do evento
     * @param array<string, string> $dadosEmitente Dados do emitente
     * @param string $filename Nome do arquivo
     * @return Response
     * @throws Exception
     */
    public function renderDaevento(string $xml, array $dadosEmitente = [], string $filename = 'evento.pdf'): Response
    {
        $pdf = $this->generateDaevento($xml, $dadosEmitente);

        return $this->pdfResponse($pdf, $filename, 'inline');
    }

    /**
     * Cria uma Response HTTP com conteúdo PDF
     *
     * @param string $pdf Conteúdo binário do PDF
     * @param string $filename Nome do arquivo
     * @param string $disposition attachment ou inline
     * @return Response
     */
    protected function pdfResponse(string $pdf, string $filename, string $disposition): Response
    {
        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "{$disposition}; filename=\"{$filename}\"",
            'Content-Length' => strlen($pdf),
        ]);
    }
}
