<?php

   $np = $_POST["np"];
   $itax = $_POST["tax"];
   $ipv = $_POST["pv"];
   $ipp = $_POST["pp"];
   $ipb = $_POST["pb"];
   $volta = $_POST["volta"];
   $idp = $_POST["dp"];
   $imprime = $_POST["imprime"];
   echo("OLcamscaklscklmsakkmakslclmsakmcklams-----------</br>");
   echo("np:".$np.'</br>');
   echo("itax:".$itax.'</br>');
   echo("ipv:".$ipv.'</br>');
   echo("ipp:".$ipp.'</br>');
   echo("ipb:".$ipb.'</br>');
   echo("volta:".$volta.'</br>');
   echo("idp:".$idp.'</br>');
   echo("imprime:".$imprime.'</br>');

  function toFixed($number, $decimals) {
    return number_format($number, $decimals, '.', "");
  };

  function priceTable($np, $pv, $t, $pmt) {
    $table = array();
    $pt = 0;
    $jt = 0;
    $at = 0;
    for ($i = 0; $i < $np; $i++) {
      $juros = $pv * $t;
      $amortizacao = $pmt - $juros;
      $saldo = $pv - $amortizacao;
      $pv = $saldo;
      $pt += $pmt;
      $jt += $juros;
      $at += $amortizacao;
      //table push
      $table[(string)$i] = array(
              "mes" => $i+1,
              "pmt" => toFixed($pmt,2),
              "juros" => toFixed($juros,2),
              "amortizacao" => toFixed($amortizacao,2),
              "saldo" => $i + 1
      );
    }
#    	$table["Total"] = array(
#		"mes" => "total"; 
#    		"pmt" => toFixed($pt,2),
#     	      "juros" => toFixed($jt,2),
#     	      "amortizacao" => toFixed($at,2),
#	      "saldo" => 0
#	);
      return $table;
    };

echo priceTable($np,$ipv,$itax,$pmt);
?>
