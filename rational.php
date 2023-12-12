<?php  declare(strict_types=1);

$result = "";

function log($process){
  gettype($process) === "object" ? appendToString($process) : appendToDiv($process);
}

function appendToDiv($str, $id = "#rational") {
  if (gettype($result) === "string") {
    $result = $document->querySelector($id);
    $result->innerHTML += `$str.$crlf`;
  } else {
    $resulti->nnerHTML += `$str.$crlf`;
  }
  return $result
}

function appendToString($str) {
  return $result = $str . $crlf;
}

$pt = [
  'lenNum' => 13,
  'lenMes' => 6,
  'precision' => 2,
  'eol' => "|",
  'filler' => " ",
];

const $crlf = "<br>";

setDownPayment.downP = false;

function setDownPayment($dp = true) {
  setDownPayment.downP = dp;
}

function getDownPayment() {
  return setDownPayment.downP;
}

function isZero($n, $tol = 1.0e-8) {
  return abs(n) < $tol;
}

function getInterest($x, $y, $p) {
  if ($x == 0 || $y == 0 || $p == 0) return array(0, 0);
  $R = $x / $p; // prestação

  if (!getDownPayment()) {
    return getInterest2($x, $y, $p);
  } else if (false) {
    return getInterest2($x - $R, $y - $R,$p - 1);
  } else {
    $t2 = $x / $y;
    $t = 0;
    $n = 0;
    while (!isZero($t2 - $t)) {
      if ($n > 150) throw new Error("Newton is not converging!");
      $t = $t2;
      $n += 1;
      $tPlusOne = 1.0 + $t;
      $a = $tPlusOne ** -$p; // (1.0+t)**(-p)
      $d = $y * $t - $R * (1 - $a) * $tPlusOne; // f(t_n)
      $dt = $y - $R * (1 - $a * (1 - $p)); // f'(t_n)
      $t2 = $t - $d / $dt;
    }
    if (isZero($t2, 1.0e-10)) throw new Error("Newton did not converge!");

    return array($t2 * 100.0, $n);
  }
}

function getInterest2($x, $y, $p) {
  if ($x == 0 || $y == 0 || $p == 0) return array(0, 0);
  $t2 = $x / $y;
  $t = 0;
  $n = 0;
  while (!isZero($t2 - $t)) {
    if ($n > 150) throw new Error("Newton is not converging!");
    $t = $t2;
    $n += 1;
    $tPlusOne = 1.0 + $t;
    $a = $tPlusOne ** -$p; // (1.0+t)**(-p)
    $b = $a / $tPlusOne; // (1.0+t)**(-p-1)
    $d = $y * $t - ($x / $p) * (1 - $a); // f(t_n)
    $dt = $y - $x * $b; // f'(t_n)
    $t2 = $t - $d / $dt;
  }
  if (isZero($t2, 1.0e-10)) throw new Error("Newton did not converge!");
  return array($t2 * 100.0, $n);
}

function presentValue($x, $p, $t, $fix = true) {
  $factor = 1.0 / ($p * CF($t, $p));
  if ($fix && getDownPayment()) {
    $factor *= 1.0 + $t;
  }
  return array($factor, $x * $factor);
}

function futureValue($y, $p, $t, $fix = true) {
  $factor = CF($t, $p) * $p;
  if ($fix && getDownPayment()) {
    $factor /= 1.0 + $t;
  }
  return array($factor, $y * $factor);
}

function CF($i, $n) {
  return $i / (1 - (1 + $i) ** -$n);
}

function rational_discount($p, $t, $x, $y, $option = true) {
  $result = "";
  if ($y >= $x) {
    log("Preço à vista deve ser menor do que o preço total:");
  } else {
    list($interest, $niter) = getInterest($x, $y, $p);
    if ($t == 0) {
      $t = 0.01 * $interest;
    }

    list($fx, $ux) = presentValue($x, $p, $t);
    if ($y <= 0) {
      $y = $ux;
    }
    list($fy, $uy) = futureValue($y, $p, $t);
    if (isZero($y - $ux, 0.01)) {
      log("O preço à vista é igual ao preço total corrigido.");
    } else if ($y > $ux) {
      log(
        "O preço à vista é maior do que preço total corrigido ⇒ melhor comprar a prazo."
      );
    } else {
      log("O preço à vista é menor ou igual do que preço total corrigido.");
    }
    $delta_p = $ux - $y;
    if (isZero($delta_p)) $delta_p = 0;
    $prct = ($delta_p / $ux) * 100.0;

    log(
      'Taxa Real = '.toFixed($interest,4).', Iterações = '. $niter.',' .'Fator = '.toFixed($fx,4)
    );
    log(
      'Preço à vista + juros de '.toFixed($t * 100,2)}.'% ao mês = '.toFixed($uy,2)}
    );
    log(
      'Preço a prazo - juros de '.toFixed(t*100,2)}.'% ao mês = '.toFixed($ux,2)}
    );
    log(
      'Juros Embutidos = '.toFixed($x,2)}.'-'.toFixed($y,2) .'/'. toFixed($y,2) .'='. toFixed(((($x - $y) / $y) * 100),2),'%'
    );
    // ToDo daqui a pouco volto aqui
    //
    log(
      `Desconto = (\$${x.toFixed(2)} - \$${y.toFixed(2)}) / \$${x.toFixed(2)} = ${(((x - y) / x) * 100).toFixed(2)}%`
    );
    log(
      `Excesso = \$${ux.toFixed(2)} - \$${y.toFixed(2)} = \$${delta_p.toFixed(
        2
      )}`
    );
    log(
      `Excesso = (\$${x.toFixed(2)} - \$${uy.toFixed(2)}) * ${fx.toFixed(
        4
      )} = \$${((x - uy) * fx).toFixed(2)}`
    );
    log(`Percentual pago a mais = ${prct.toFixed(2)}%`);
    if ($option) {
      if (0.0 <= $prct && $prct <= 1.0) {
        log("Valor aceitável.");
      } else if (1.0 < $prct && $prct <= 3.0) {
        log("O preço está caro.");
      } else if (3.0 < $prct) {
        log("Você está sendo roubado.");
      }
    }

    $cf = CF($t, $p);
    $pmt = $y * $cf;
    if (getDownPayment()) {
      $pmt /= 1 + $t;
      $p -= 1; // uma prestação a menos
      $y -= $pmt; // preço à vista menos a entrada
      $cf = $pmt / $y; // recalculate cf
    }
    $ptb = priceTable($p, $y, $t, $pmt);
    log($crlf);
    log(nodePriceTable($ptb));
  }
  return $result;
}

// ToDo não terminei o center
const center = ($str, $len) =>
  str
    .padStart(str.length + Math.floor((len - str.length) / 2), pt.filler)
    .padEnd(len, pt.filler);


//ToDo provavelmente vai ter problema aqui
function priceTable($np, $pv, $t, $pmt) {
  $dataTable = [
    ["Mês", "Prestação", "Juros", "Amortização", "Saldo Devedor"],
  ];
  $pt = getDownPayment() ? pmt : 0;
  $jt = 0;
  $at = 0;
  $dataTable->push(["n", "R = pmt", "J = SD * t", "U = pmt - J", "SD = PV - U"]);
  $dataTable->push([0, $pt, `(${t.toFixed(4)})`, 0, $pv]);
  if ($t <= 0) return $dataTable;
  for ($i = 0; $i < $np; ++$i) {
    $juros = $pv * $t;
    $amortizacao = $pmt - $juros;
    $saldo = $pv - $amortizacao;
    $pv = $saldo;
    $pt += $pmt;
    $jt += $juros;
    $at += $amortizacao;
    $dataTable->push([$i + 1, $pmt, $juros, $amortizacao, isZero($saldo) ? 0 : $saldo]);
  }
  $dataTable->push(["Total", $pt, $jt, $at, 0]);
  return $dataTable;
}

//ToDo Formatador de Row
// function formatRow($r) {
//   $row = "";
//   $val;
//
//   r.forEach((col, index) => {
//     if (index == 0) {
//       val = center(col.toString(), pt.lenMes);
//       row += `${pt.eol}${val}${pt.eol}`;
//     } else if (typeof col === "number") {
//       val = Number(col).toFixed(pt.precision);
//       row += center(val.toString(), pt.lenNum) + pt.eol;
//     } else {
//       row += center(col, pt.lenNum) + pt.eol;
//     }
//   });
//   return row;
// }

//ToDo Nao mexi no html
function nodePriceTable($ptb) {
  // Number of float columns
  $nfloat = $ptb[0]->length - 1;
  // Length of a row.
  $lenTable = $pt->lenMes + ($pt->lenNum + $pt->eol->length) * $nfloat;

  // Line separator.
  $line = `${pt.eol}${"_".repeat(pt.lenMes)}${pt.eol}${(
    "_".repeat(pt.lenNum) + pt.eol
  ).repeat(nfloat)}`;
  $line2 = ` ${"_".repeat(lenTable)}`;

  $table = [];

  $table->push(center("Tabela Price", $lenTable));
  $table->push($line2);
  $ptb.forEach(($row, $index) => {
    $table->push(formatRow($row));
    if ($index == 0 || $index == $ptb->length - 2) {
      $table->push($line);
    }
  });
  $table->push($line);

  return $table->join($crlf);
}

//ToDo Nao mexi no html
function htmlPriceTable($ptb) {
  $table = `<table border=1>
      <caption style='font-weight: bold; font-size:200%;'>
        Tabela Price
      </caption>
      <tbody style='text-align:center;'>
    `;
  $ptb.forEach(($row, $i) => {
    $table += "<tr>";
    $row.forEach(($col, $j) => {
      if (gettype($col) === "number") {
        if ($j > 0) col = toFixed($col,$j == 2 ? $pt->precision + 1 : $pt->precision);
      }
      $table += $i > 0 ? `<td>${col}</td>` : `<th>${col}</th>`;
    });
    $table += "</tr>";
  });
  $table += "</tbody></table>";

  return $table;
}

function cdcCLI($argv = process.argv) {
  // number of payments.
  $np = 0;
  // interest rate
  $t = 0;
  // initial price
  $pv = 0;
  // final price
  $pp = 0;
  // debugging state.
  $debug = false;
  // holds the existence of a down payment.
  setDownPayment(false);

  const $mod_getopt = require("posix-getopt");
  const $readlineSync = require("readline-sync");
  const $parse = ($str) => str.substring(str.lastIndexOf("/") + 1, str.length);
  let parser, option;

  try {
    try {
      // options that require an argument should be followed by a colon (:)
      parser = new mod_getopt.BasicParser(
        "h(help)n:(parcelas)t:(taxa)x:(valorP)y:(valorV)v(verbose)e(entrada)",
        argv
      );
    } catch (msg) {
      throw msg;
    }

    while ((option = parser.getopt()) !== undefined) {
      switch (option.option) {
        case "h":
          log(
            `Usage ${parse(argv[0])} ${parse(
              argv[1]
            )} -n <nº parcelas> -t <taxa> -x <valor a prazo> -y <valor à vista> -e -v`
          );
          return 1;
        case "n":
          np = +option.optarg;
          break;
        case "t":
          t = Number(option.optarg) / 100.0;
          break;
        case "x":
          pp = Number(option.optarg);
          break;
        case "y":
          pv = Number(option.optarg);
          break;
        case "v":
          debug = true;
          break;
        case "e":
          setDownPayment();
          break;
      }
    }
  } catch (err) {
    log(
      `${err.message}\nFor help, type: ${parse(argv[0])} ${parse(
        argv[1]
      )} --help`
    );
    return 2;
  }

  while (
    np <= 2 ||
    (pv <= 0 && pp <= 0) ||
    (t <= 0 && pp <= 0) ||
    (t <= 0 && pv <= 0) ||
    pp < pv
  ) {
    try {
      np = +readlineSync.question("Forneça o número de parcelas: ");
      t = +readlineSync.question("Forneça a taxa de juros: ") / 100.0;
      pp = +readlineSync.question("Forneça o preço a prazo: ");
      pv = +readlineSync.question("Forneça o preço à vista: ");
      if (isNaN(np) || isNaN(t) || isNaN(pp) || isNaN(pv)) {
        throw new Error("Value is not a Number");
      }
    } catch (err) {
      log(err.message);
      rational_discount(10, 0.01, 500, 450, debug);
      return;
    }
  }

  if (t > 0) {
    if (pp <= 0) {
      let factor;
      [factor, pp] = futureValue(pv, np, t);
    }
  } else {
    let ni;
    let pmt = pp / np;
    try {
      if (pmt >= pv) {
        throw new Error(
          `Prestação (\$${pmt.toFixed(2)}) é maior do que o empréstimo`
        );
      }
      // getInterest takes in considerarion any down payment
      [t, ni] = getInterest(pp, pv, np);
    } catch (e) {
      log(`${e.message}`);
      return;
    }
    log(`Taxa = ${t.toFixed(4)}% - ${ni} iterações${crlf}`);
    t *= 0.01;
  }

  // with or without any down payment
  let cf = CF(t, np);
  let pmt = pv * cf;
  if (pmt >= pv) {
    rational.log(`Prestação (\$${pmt.toFixed(2)}) é maior do que o empréstimo`);
  }
  log(`Coeficiente de Financiamento: ${cf.toFixed(6)}`);

  let dp = getDownPayment();
  if (dp) {
    pmt /= 1 + t;
    np -= 1; // uma prestação a menos
    pv -= pmt; // preço à vista menos a entrada
    pp -= pmt; // preço a prazo menos a entrada
    log(`Entrada: ${pmt.toFixed(2)}`);
    log(
      `Valor financiado = \$${(pv + pmt).toFixed(2)} - \$${pmt.toFixed(
        2
      )} = \$${pv.toFixed(2)}`
    );
    // the values were set here to work without down payment
    // otherwise, rational_discount will produce a misleading interest rate
    setDownPayment(false);
  }

  log(`Prestação: \$${pmt.toFixed(2)}${crlf}`);

  let output = result.slice() + rational_discount(np, t, pp, pv, debug);
  result = "";
  output = output.slice(
    0,
    output.indexOf(crlf + "                         Tabela Price")
  );

  // Tabela Price
  if (debug) {
    setDownPayment(dp);
    log(nodePriceTable(priceTable(np, pv, t, pmt)));
    output += result;
  }
  console.log(output.split(crlf).join("\n"));
}

//ToDo funcionalidade que exite no js
// module.exports = {
//   htmlPriceTable,
//   nodePriceTable,
//   priceTable,
//   CF,
//   presentValue,
//   futureValue,
//   getInterest,
//   getDownPayment,
//   setDownPayment,
//   rational_discount,
// };
//
// // called directly via command line interface (CLI)
// if (require.main === module) cdcCLI();
?>
