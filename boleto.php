<?php

/*
    Script CRL Boleto Bancario
*/

  function createEntry($key, $value)
  {
      $toReturn = array('key'   => $key,
                        'value' => $value);
      return $toReturn;
  }

  function getTicketXml()
  {
      $dados = array(createEntry('CONVENIO.COD-BANCO', '0000'),
                      createEntry('CONVENIO.COD-CONVENIO', '000000000000000'),
                      createEntry('PAGADOR.TP-DOC', '00'),
                      createEntry('PAGADOR.NUM-DOC', '00000000000000'),
                      createEntry('PAGADOR.NOME', 'xxxxxxxxxxxxxxxx'),
                      createEntry('PAGADOR.ENDER', 'xxxxxxxxxxxxxxxxxxxxxxxxxx'),
                      createEntry('PAGADOR.BAIRRO', 'xxxxxxx'),
                      createEntry('PAGADOR.CIDADE', 'xxxxxxx'),
                      createEntry('PAGADOR.UF', '00'),
                      createEntry('PAGADOR.CEP', '00000000'),
                      createEntry('TITULO.NOSSO-NUMERO', '0000000000000'),
                      createEntry('TITULO.SEU-NUMERO', '0000000000000'),
                      createEntry('TITULO.DT-VENCTO', '00000000'),
                      createEntry('TITULO.DT-EMISSAO', '00000000'),
                      createEntry('TITULO.ESPECIE', '000'),
                      createEntry('TITULO.VL-NOMINAL', '000000000063000'),
                      createEntry('TITULO.PC-MULTA', '00000'),
                      createEntry('TITULO.QT-DIAS-MULTA', '00'),
                      createEntry('TITULO.PC-JURO', '00000'),
                      createEntry('TITULO.TP-DESC', '0'),
                      createEntry('TITULO.VL-DESC', '00000'),
                      createEntry('TITULO.DT-LIMI-DESC', '00000000'),
                      createEntry('TITULO.VL-ABATIMENTO', '000'),
                      createEntry('TITULO.TP-PROTESTO', '0'),
                      createEntry('TITULO.QT-DIAS-PROTESTO', '00'),
                      createEntry('TITULO.QT-DIAS-BAIXA', '00'),  
                     
                      createEntry('MENSAGEM', 'Sr. Caixa Nao receber apos vencimento nem valor menor que o do documento.')
                     );
     
      $ticketRequest = array('dados'     => $dados,
                             'expiracao' => 000,
                             'sistema'   => 'YMB');

      $toReturn = array('TicketRequest' => $ticketRequest);
     
      return $toReturn;
  }

  function getRegistroXml($ticket)
  {
      $inclusaoTitulo = array('dtNsu'      => '00000000',
                              'estacao'    => '0000',
                              'nsu'        => '00000000000000000000',
                              'ticket'     => $ticket,
                              'tpAmbiente' => 'T'
                             );
     
      $toReturn = array('dto' => $inclusaoTitulo);
     
      return $toReturn;
  }

  function teste()
  {

      try
      {

        $options = array('keep_alive' => false,
                        'trace'      => true,
                        'local_cert' => 'c:\certs\.pem',
                        'passphrase' => 'XXXXXXXX',
                        'cache_ws'   => WSDL_CACHE_NONE
                        );          

          $cliTicket = new SoapClient("https://TicketEndpointService.wsdl", $options);
          echo ("TICKET!!");                            
          // ticket
         
          $xmlCreate = getTicketXml();
          $cResponse = $cliTicket->create($xmlCreate);

          // cobrança
          $cliCobranca = new SoapClient("https://CobrancaEndpointService.wsdl", $options);
          $xmlRegistro = getRegistroXml($cResponse->TicketResponse->ticket);
          $rResponse   = $cliCobranca->registraTitulo($xmlRegistro);

          // Imprime no browser:
          print_r($xmlCreate);
          echo '<br><br>';
          print_r($xmlRegistro);
          echo '<br><br>';
          print_r($cResponse);
          echo '<br><br>';
          print_r($rResponse);
      }
      catch(SoapFault $e)
      {
          echo("EXCEÇÂO DO SOAP");
          var_dump($e);
      }
  }

  teste();


?>
