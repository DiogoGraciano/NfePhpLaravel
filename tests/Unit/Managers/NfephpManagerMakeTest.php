<?php

namespace DiogoGraciano\Nfephp\Tests\Unit\Managers;

use DiogoGraciano\Nfephp\Nfephp;
use DiogoGraciano\Nfephp\Tests\TestCase;
use NFePHP\NFe\Make;

class NfephpManagerMakeTest extends TestCase
{
    /**
     * Replica o bug original: taginfNFe seguido de getXML não deve
     * lançar "Cannot access uninitialized non-nullable property Make::$infNFe"
     */
    public function testTaginfNFeFollowedByGetXMLDoesNotThrowUninitializedError(): void
    {
        $core = new Nfephp();
        $make = $core->createNFe();

        $make->taginfNFe((object) ['versao' => '4.00', 'Id' => '', 'pk_nItem' => '']);
        $make->tagide($this->buildIdeStd());
        $make->tagemit($this->buildEmitStd());
        $make->tagenderEmit($this->buildEnderEmitStd());
        $make->tagdest($this->buildDestStd());
        $make->tagenderDest($this->buildEnderDestStd());
        $make->tagprod($this->buildProdStd());
        $make->tagimposto((object) ['item' => 1, 'vTotTrib' => 0]);
        $make->tagICMS($this->buildIcmsStd());
        $make->tagPIS($this->buildPisStd());
        $make->tagCOFINS($this->buildCofinsStd());
        $make->tagICMSTot($this->buildIcmsTotStd());
        $make->tagtransp((object) ['modFrete' => 9]);
        $make->tagpag((object) ['vTroco' => 0]);
        $make->tagdetPag((object) ['indPag' => 0, 'tPag' => '01', 'vPag' => 100]);

        $xml = $make->getXML();

        $this->assertNotEmpty($xml);
        $this->assertStringContainsString('<infNFe', $xml);
    }

    /**
     * Testa que setConfig não afeta instância Make já obtida
     */
    public function testSetConfigDoesNotAffectExistingMakeInstance(): void
    {
        $core = new Nfephp();
        $make = $core->createNFe();

        $make->taginfNFe((object) ['versao' => '4.00', 'Id' => '', 'pk_nItem' => '']);

        // setConfig recria internamente, mas $make é independente
        $core->setConfig(['nfe_config' => ['tpAmb' => 1, 'razaosocial' => 'Outra Empresa']]);

        $make->tagide($this->buildIdeStd());
        $make->tagemit($this->buildEmitStd());
        $make->tagenderEmit($this->buildEnderEmitStd());
        $make->tagdest($this->buildDestStd());
        $make->tagenderDest($this->buildEnderDestStd());
        $make->tagprod($this->buildProdStd());
        $make->tagimposto((object) ['item' => 1, 'vTotTrib' => 0]);
        $make->tagICMS($this->buildIcmsStd());
        $make->tagPIS($this->buildPisStd());
        $make->tagCOFINS($this->buildCofinsStd());
        $make->tagICMSTot($this->buildIcmsTotStd());
        $make->tagtransp((object) ['modFrete' => 9]);
        $make->tagpag((object) ['vTroco' => 0]);
        $make->tagdetPag((object) ['indPag' => 0, 'tPag' => '01', 'vPag' => 100]);

        $xml = $make->getXML();

        $this->assertNotEmpty($xml);
    }

    /**
     * Testa que createNFe retorna instâncias independentes
     */
    public function testCreateNFeReturnsIndependentInstances(): void
    {
        $core = new Nfephp();

        $make1 = $core->createNFe();
        $make2 = $core->createNFe();

        $this->assertNotSame($make1, $make2);
    }

    /**
     * Testa que createNFCe retorna instâncias independentes
     */
    public function testCreateNFCeReturnsIndependentInstances(): void
    {
        $core = new Nfephp();

        $make1 = $core->createNFCe();
        $make2 = $core->createNFCe();

        $this->assertNotSame($make1, $make2);
    }

    /**
     * Testa que getXML sem taginfNFe lança erro de propriedade não inicializada
     * (reproduz o bug original)
     */
    public function testGetXMLWithoutTaginfNFeThrowsError(): void
    {
        $this->expectException(\Error::class);

        $core = new Nfephp();
        $make = $core->createNFe();

        // Não chama taginfNFe — deve falhar
        $make->getXML();
    }

    /**
     * Testa que múltiplas NFes podem ser criadas em sequência sem interferência
     */
    public function testMultipleNFeCreationInSequence(): void
    {
        $core = new Nfephp();

        // Cria primeira NFe
        $make1 = $core->createNFe();
        $make1->taginfNFe((object) ['versao' => '4.00', 'Id' => '', 'pk_nItem' => '']);
        $make1->tagide($this->buildIdeStd());
        $make1->tagemit($this->buildEmitStd());
        $make1->tagenderEmit($this->buildEnderEmitStd());
        $make1->tagdest($this->buildDestStd());
        $make1->tagenderDest($this->buildEnderDestStd());
        $make1->tagprod($this->buildProdStd());
        $make1->tagimposto((object) ['item' => 1, 'vTotTrib' => 0]);
        $make1->tagICMS($this->buildIcmsStd());
        $make1->tagPIS($this->buildPisStd());
        $make1->tagCOFINS($this->buildCofinsStd());
        $make1->tagICMSTot($this->buildIcmsTotStd());
        $make1->tagtransp((object) ['modFrete' => 9]);
        $make1->tagpag((object) ['vTroco' => 0]);
        $make1->tagdetPag((object) ['indPag' => 0, 'tPag' => '01', 'vPag' => 100]);

        $xml1 = $make1->getXML();

        // Cria segunda NFe — não deve ser afetada pela primeira
        $make2 = $core->createNFe();
        $make2->taginfNFe((object) ['versao' => '4.00', 'Id' => '', 'pk_nItem' => '']);
        $make2->tagide($this->buildIdeStd(['nNF' => '2']));
        $make2->tagemit($this->buildEmitStd());
        $make2->tagenderEmit($this->buildEnderEmitStd());
        $make2->tagdest($this->buildDestStd());
        $make2->tagenderDest($this->buildEnderDestStd());
        $make2->tagprod($this->buildProdStd());
        $make2->tagimposto((object) ['item' => 1, 'vTotTrib' => 0]);
        $make2->tagICMS($this->buildIcmsStd());
        $make2->tagPIS($this->buildPisStd());
        $make2->tagCOFINS($this->buildCofinsStd());
        $make2->tagICMSTot($this->buildIcmsTotStd());
        $make2->tagtransp((object) ['modFrete' => 9]);
        $make2->tagpag((object) ['vTroco' => 0]);
        $make2->tagdetPag((object) ['indPag' => 0, 'tPag' => '01', 'vPag' => 100]);

        $xml2 = $make2->getXML();

        $this->assertNotEmpty($xml1);
        $this->assertNotEmpty($xml2);
        $this->assertNotEquals($xml1, $xml2);
    }

    /**
     * Testa que setConfig entre duas criações de NFe não causa problema
     */
    public function testSetConfigBetweenTwoNFeCreations(): void
    {
        $core = new Nfephp();

        $make1 = $core->createNFe();
        $make1->taginfNFe((object) ['versao' => '4.00', 'Id' => '', 'pk_nItem' => '']);

        // Muda config no meio
        $core->setConfig(['nfe_config' => ['tpAmb' => 1]]);

        // Cria segundo Make — deve funcionar normalmente
        $make2 = $core->createNFe();
        $make2->taginfNFe((object) ['versao' => '4.00', 'Id' => '', 'pk_nItem' => '']);

        // Ambos devem ter infNFe inicializado (sem erro)
        $this->assertInstanceOf(Make::class, $make1);
        $this->assertInstanceOf(Make::class, $make2);
        $this->assertNotSame($make1, $make2);
    }

    /**
     * Testa fluxo completo simulando o EmitirNfe com setConfig antes de createNFe
     */
    public function testFullFlowWithSetConfigBeforeCreateNFe(): void
    {
        $core = new Nfephp();

        // Simula Nfephp::setConfig($tenant->toNfephpConfig())
        $core->setConfig([
            'nfe_config' => [
                'atualizacao' => '2024-01-01 00:00:00',
                'tpAmb' => 2,
                'razaosocial' => 'Empresa Teste LTDA',
                'cnpj' => '12345678000195',
                'siglaUF' => 'SP',
                'schemes' => 'PL_009_V4_00_NT_2020_006_v1.20',
                'versao' => '4.00',
            ],
        ]);

        $make = $core->createNFe();
        $this->assertInstanceOf(Make::class, $make);

        $make->taginfNFe((object) ['versao' => '4.00', 'Id' => '', 'pk_nItem' => '']);
        $make->tagide($this->buildIdeStd());
        $make->tagemit($this->buildEmitStd());
        $make->tagenderEmit($this->buildEnderEmitStd());
        $make->tagdest($this->buildDestStd());
        $make->tagenderDest($this->buildEnderDestStd());
        $make->tagprod($this->buildProdStd());
        $make->tagimposto((object) ['item' => 1, 'vTotTrib' => 0]);
        $make->tagICMS($this->buildIcmsStd());
        $make->tagPIS($this->buildPisStd());
        $make->tagCOFINS($this->buildCofinsStd());
        $make->tagICMSTot($this->buildIcmsTotStd());
        $make->tagtransp((object) ['modFrete' => 9]);
        $make->tagpag((object) ['vTroco' => 0]);
        $make->tagdetPag((object) ['indPag' => 0, 'tPag' => '01', 'vPag' => 100]);

        $xml = $make->getXML();

        $this->assertNotEmpty($xml);
        $this->assertStringContainsString('Empresa Teste', $xml);
    }

    // ===========================
    // Helpers para montar stdClass
    // ===========================

    private function buildIdeStd(array $overrides = []): \stdClass
    {
        $std = new \stdClass();
        $std->cUF = 35;
        $std->cNF = '12345678';
        $std->natOp = 'VENDA';
        $std->mod = 55;
        $std->serie = 1;
        $std->nNF = $overrides['nNF'] ?? '1';
        $std->dhEmi = date('Y-m-d\TH:i:sP');
        $std->dhSaiEnt = date('Y-m-d\TH:i:sP');
        $std->tpNF = 1;
        $std->idDest = 1;
        $std->cMunFG = '3550308';
        $std->tpImp = 1;
        $std->tpEmis = 1;
        $std->cDV = 0;
        $std->tpAmb = 2;
        $std->finNFe = 1;
        $std->indFinal = 1;
        $std->indPres = 1;
        $std->indIntermed = null;
        $std->procEmi = 0;
        $std->verProc = '1.0';

        return $std;
    }

    private function buildEmitStd(): \stdClass
    {
        $std = new \stdClass();
        $std->xNome = 'Empresa Teste LTDA';
        $std->xFant = 'Empresa Teste';
        $std->IE = '123456789012';
        $std->IEST = null;
        $std->IM = null;
        $std->CNAE = null;
        $std->CRT = 3;
        $std->CNPJ = '12345678000195';
        $std->CPF = null;

        return $std;
    }

    private function buildEnderEmitStd(): \stdClass
    {
        $std = new \stdClass();
        $std->xLgr = 'Rua Teste';
        $std->nro = '100';
        $std->xCpl = null;
        $std->xBairro = 'Centro';
        $std->cMun = '3550308';
        $std->xMun = 'SAO PAULO';
        $std->UF = 'SP';
        $std->CEP = '01001000';
        $std->cPais = '1058';
        $std->xPais = 'BRASIL';
        $std->fone = null;

        return $std;
    }

    private function buildDestStd(): \stdClass
    {
        $std = new \stdClass();
        $std->xNome = 'Cliente Teste';
        $std->indIEDest = 9;
        $std->IE = null;
        $std->ISUF = null;
        $std->IM = null;
        $std->email = null;
        $std->CNPJ = null;
        $std->CPF = '11144477735';
        $std->idEstrangeiro = null;

        return $std;
    }

    private function buildEnderDestStd(): \stdClass
    {
        $std = new \stdClass();
        $std->xLgr = 'Rua Cliente';
        $std->nro = '200';
        $std->xCpl = null;
        $std->xBairro = 'Bairro';
        $std->cMun = '3550308';
        $std->xMun = 'SAO PAULO';
        $std->UF = 'SP';
        $std->CEP = '01002000';
        $std->cPais = '1058';
        $std->xPais = 'BRASIL';
        $std->fone = null;

        return $std;
    }

    private function buildProdStd(): \stdClass
    {
        $std = new \stdClass();
        $std->item = 1;
        $std->cProd = '001';
        $std->cEAN = 'SEM GTIN';
        $std->xProd = 'Produto Teste';
        $std->NCM = '84159090';
        $std->CFOP = '5102';
        $std->uCom = 'UN';
        $std->qCom = 1;
        $std->vUnCom = 100;
        $std->vProd = 100;
        $std->cEANTrib = 'SEM GTIN';
        $std->uTrib = 'UN';
        $std->qTrib = 1;
        $std->vUnTrib = 100;
        $std->indTot = 1;

        return $std;
    }

    private function buildIcmsStd(): \stdClass
    {
        $std = new \stdClass();
        $std->item = 1;
        $std->orig = 0;
        $std->CST = '00';
        $std->modBC = 3;
        $std->vBC = 100;
        $std->pICMS = 18;
        $std->vICMS = 18;

        return $std;
    }

    private function buildPisStd(): \stdClass
    {
        $std = new \stdClass();
        $std->item = 1;
        $std->CST = '01';
        $std->vBC = 100;
        $std->pPIS = 1.65;
        $std->vPIS = 1.65;

        return $std;
    }

    private function buildCofinsStd(): \stdClass
    {
        $std = new \stdClass();
        $std->item = 1;
        $std->CST = '01';
        $std->vBC = 100;
        $std->pCOFINS = 7.60;
        $std->vCOFINS = 7.60;

        return $std;
    }

    private function buildIcmsTotStd(): \stdClass
    {
        $std = new \stdClass();
        $std->vBC = 100;
        $std->vICMS = 18;
        $std->vICMSDeson = 0;
        $std->vFCP = 0;
        $std->vBCST = 0;
        $std->vST = 0;
        $std->vFCPST = 0;
        $std->vFCPSTRet = 0;
        $std->vProd = 100;
        $std->vFrete = 0;
        $std->vSeg = 0;
        $std->vDesc = 0;
        $std->vII = 0;
        $std->vIPI = 0;
        $std->vIPIDevol = 0;
        $std->vPIS = 1.65;
        $std->vCOFINS = 7.60;
        $std->vOutro = 0;
        $std->vNF = 100;
        $std->vTotTrib = 0;

        return $std;
    }
}
