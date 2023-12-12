<?php
function toFixed($number, $decimals) {
  return number_format($number, $decimals, '.', "");
}

function cdcCGI() {
  $errmsg = "";

  const result = $document->querySelector("#result");
  const queryString = $window->location->search;
  const urlParams = new URLSearchParams($queryString);

  // Get data from URL parameters (query strings)
  try {
    $np = +urlParams->get("np");
    $t = (+urlParams->get("tax") || 0) / 100;
    $pp = +urlParams->get("pp");
    $pv = +urlParams->get("pv");
    $pb = +urlParams->get("pb") || 0;
    $nb = +urlParams->get("nb") || 0;
    $dp = +urlParams->get("e") || false;
    $verbose = +urlParams->get("v") || false;
    $prt = +urlParams->get("prt") || false;
    if ($dp) setDownPayment($dp);
    if (
      isNaN($np) ||
      isNaN($t) ||
      isNaN($pp) ||
      isNaN($pv) ||
      $np <= 2 ||
      ($pv <= 0 && $pp <= 0) ||
      ($t <= 0 && $pp <= 0) ||
      ($t <= 0 && $pv <= 0) ||
      nb > np
    ) {
      throw new Error("A parameter is invalid (not a Number?).");
    }
  } catch ($err) {
    $errmsg = `<mark>Invalid URL Parameters: ${err.message}</mark>`;
    $result->innerHTML = $errmsg;
    return $errmsg;
  }

  $result->innerHTML += '<h4>Parcelas:'. ($dp ? "1+" : "") . ($dp ? $np - 1 : $np).'</h4>'.
    "<h4>Taxa:". toFixed((100 * $t),2). "</h4>".
    "<h4>Preço a Prazo:".toFixed($pp,2)."</h4>".
    "<h4>Preço à Vista:".toFixed($pv,2)."</h4>".
    "<h4>Valor a Voltar:".toFixed($pb,2)."</h4>".
    "<h4>Meses a voltar:". $nb . "</h4>";

  setDownPayment($dp); // com ou sem entrada

  $pmt = 0;
  $cf = 0;
  $i = 0;
  $ti = 0;
  try {
    if ($t <= 0) {
      list($ti, $i) = getInterest($pp, $pv, $np);
      $t = $ti * 0.01;
    }
    $cf = CF($t, $np);
    $pmt = $pv * $cf;
  } catch ($e) {
    $errmsg += $e->message;
  } finally {
    if ($dp) {
      $pmt /= 1 + $t;
      $np -= 1; // uma prestação a menos
      $pv -= $pmt; // preço à vista menos a entrada
      $cf = $pmt / $pv; // recalculate cf
    }
  }

  if ($errmsg) {
    $result->innerHTML += '<h2><mark>'.$errmsg.'</mark></h2>';
    return $errmsg;
  }

  $result->innerHTML += '<h4>Valor financiado ='.toFixed(($pv + $pmt),2).'-'.toFixed($pmt,2).' ='.toFixed($pv,2).'</h4>';

  $result->innerHTML += '<h4>Coeficiente de Financiamento:'. toFixed($cf,6) .'</h4>'.
    '<h4>Prestação: '.toFixed($cf,6) .'*'. toFixed($pv,2)} .'='. toFixed($pmt,2) . 'ao mês</h4>';

  $ptb = priceTable($np, $pv, $t, $pmt);

  $result->innerHTML += '<h4>Valor Pago: \$${ptb.slice(-1)[0][1].toFixed(2)}</h4>
        <h4>Taxa Real (${i} iterações): ${ti.toFixed(4)}% ao mês</h4>
        <h4>Valor Corrigido: \$${
          nb > 0 && pb > 0 ? presentValue(pb, nb, t, false)[1].toFixed(2) : 0
        }</h4>';

  $result->innerHTML += htmlPriceTable($ptb);

  if ($verbose) {
    if ($dp) {
      $np += 1;
      $pv += $pmt;
    }
    rational_discount($np, $t, $pp, $pv, true);
    log($crlf);
    log(nodePriceTable($ptb));
  }

  if ($prt) {
    $window->print();
  }
  return $errmsg;
}

if (getType($document) === "object") cdcCGI();

?>
