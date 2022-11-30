<?php

//namespace ......;

class FreightCalculate
{
    public function __construct(
        private string $code, //41106 - PAC , 40010 - SEDEX
        private string $originZipCode,
        private string $destinationZipCode,
        private int $weight,
        private int $length,
        private int $height,
        private int $width, //Min 10
        private array $response = [],
    ) {
        $url = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx';

        if ($length < 16) {
            $this->length = 16;
        }

         $params = [
            'nCdEmpresa' => '',
            'sDsSenha' => '',
            'sCepOrigem' => $this->originZipCode,
            'sCepDestino' => $this->destinationZipCode,
            'nVlPeso' => $this->weight, //kg
            'nCdFormato' => '1',  //1 para caixa / pacote e 2 para rolo/prisma.
            'nVlComprimento' => $this->length,
            'nVlAltura' => $this->height,
            'nVlLargura' => $this->width,
            'nVlDiametro' => '0',
            'sCdMaoPropria' => 'n',
            'nVlValorDeclarado' => '0',
            'sCdAvisoRecebimento' => 'n',
            'StrRetorno' => 'xml',
            'nCdServico' =>  $this->code,
        ];

        $params = http_build_query($params);

        $curl = curl_init($url . '?' . $params);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        $data = simplexml_load_string($data);

		print_r($data);
		die();

        foreach ($data->cServico as $service) {
            if ($service->Erro == 0) {
                $this->response['code'] = $service->Codigo ;
                $this->response['value'] = $service->Valor;
                $this->response['deadline'] = $service->PrazoEntrega ;
            }
        }
    }

    public function getValue(): float
    {
        return (float) $this->response['value'];
    }

    public function getDeadline(): int
    {
        return (int) $this->response['deadline'];
    }

    public function getCode(): int
    {
        return (int) $this->response['deadline'];
    }
}

// Teste
	$cepDeOrigem = '85803690';
    $cepDeDestino= '89012130' ;
//     $peso = 2;
//     $comprimento = 1;
//     $altura = 1;
//     $largura =1;
//     $valor = 400;



$frete = new FreightCalculate('41106',$cepDeOrigem, $cepDeDestino, 1, 5, 5, 10);

// ...
echo "getValor: "; 
 $frete->getValue();

// ...
echo "<hr>getPrazoEntrega: "; 
$frete->getDeadline();