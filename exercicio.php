<?php

class BoletoBancoBrasil extends BoletoAbstrato {
    
    public function gerarCodigoBarras(): string {
       
        $valorFormatado = number_format($this->valor, 2, '', ''); 
        $dataFormatada = $this->dataVencimento->format('Ymd');    
        return "001" . $valorFormatado . $dataFormatada;
    }

    public function validar(): bool {
        $hoje = new DateTime();
        
        return $this->valor > 0 && $this->dataVencimento >= $hoje;
    }

    protected function renderizarHtml(): string {
        return "<div>Boleto BB - R$ {$this->valor} - Venc: " 
             . $this->dataVencimento->format('d/m/Y') . "</div>";
    }

    protected function renderizarPdf(): string {
        return "[PDF] Boleto BB - R$ {$this->valor} - Venc: " 
             . $this->dataVencimento->format('d/m/Y');
    }
}


interface BoletoComJurosInterface {
    public function aplicarJuros(float $taxa): void;
}


class BoletoBancoItau extends BoletoAbstrato implements BoletoComJurosInterface {

    private float $juros = 0;

    public function aplicarJuros(float $taxa): void {
       
        $this->juros = $this->valor * ($taxa / 100);
        $this->valor += $this->juros;
    }

    public function gerarCodigoBarras(): string {
        $valorFormatado = number_format($this->valor, 2, '', '');
        $dataFormatada = $this->dataVencimento->format('Ymd');
        return "341" . $valorFormatado . $dataFormatada;                                                       
    }

    public function validar(): bool {
        $hoje = new DateTime();
        return $this->valor > 0 && $this->dataVencimento >= $hoje;
    }

    protected function renderizarHtml(): string {
        return "<div>Boleto Itaú - R$ {$this->valor} (c/ juros R$ {$this->juros}) - Venc: " 
             . $this->dataVencimento->format('d/m/Y') . "</div>";
    }

    protected function renderizarPdf(): string {
        return "[PDF] Boleto Itaú - R$ {$this->valor} (c/ juros R$ {$this->juros}) - Venc: " 
             . $this->dataVencimento->format('d/m/Y');
    }
}
