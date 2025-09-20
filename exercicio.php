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

/*Parte 1: Conceitos (Valor: 3,0 pontos)
a) Qual é a finalidade da BoletoInterface neste sistema? Por que é importante usá-la?
    
 A BoletoInterface define um contrato que todas as classes de boleto devem seguir.
Ela garante que qualquer banco (Brasil, Caixa, Itaú etc.) terá os mesmos métodos obrigatórios (gerarCodigoBarras, validar, renderizar).
Isso é importante porque:
Permite padronização das regras.
Facilita polimorfismo (podemos tratar qualquer boleto como “BoletoInterface”).
Ajuda na manutenção e extensibilidade: se amanhã criar um BoletoCaixa, ele já sabe quais métodos precisa implementar.

b) Explique por que a classe BoletoAbstrato precisa ser declarada como abstract. O que aconteceria se ela não fosse abstrata?

BoletoAbstrato serve como modelo base para todos os boletos, trazendo:
Propriedades e métodos comuns (valor, vencimento, renderizar).
Métodos abstratos que cada banco deve implementar (renderizarHtml, renderizarPdf).
Se ela não fosse abstract:
Seria possível instanciá-la diretamente, o que não faz sentido, pois um “boleto genérico” não existe.
Além disso, os métodos que dependem de implementação específica (renderizarHtml, renderizarPdf) ficariam sem definição, quebrando a execução.

c) O método renderizar () é implementado na classe abstrata, mas delega a execução para métodos abstratos. Qual é a vantagem dessa abordagem em termos de reuso e manutenção?    

 A vantagem é aplicar o template method:
A lógica comum de renderização (se for HTML chama X, se for PDF chama Y) fica centralizada na classe abstrata.
Cada banco só precisa se preocupar com sua forma específica de renderizar (HTML/PDF).
Isso promove reuso de código (não precisa repetir a mesma estrutura em todas as classes) e facilidade de manutenção (se a regra de escolha mudar, só ajusta na abstrata).*/  
    
    
    
    
    

