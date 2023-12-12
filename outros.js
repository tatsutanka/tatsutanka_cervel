// pmt = pagamento mensal
//  @param np n ´u mero de presta ¸c ~o es .
// /  @param pv valor do empr ´e stimo .
//  @param t taxa de juros .
//  @param pmt pagamento mensal .
//  @return uma matriz cujas linhas s ~a o listas com :
//  ( mes , presta ¸c ~ao , juros , amortiza ¸c ~ao , saldo devedor )
//  Utilizar para fazer sumir as caixas
function priceTable(np, pv, t, pmt) {
  let table = [];
  let end = [];
  pt = jt = at = 0;
  for (let i = 0; i < np; i++) {
    juros = pv * t;
    amortizacao = pmt - juros;
    saldo = pv - amortizacao;
    pv = saldo;
    pt += pmt;
    jt += juros;
    at += amortizacao;
    table.push({
      numero: i + 1,
      pmt: pmt.toFixed(2),
      juros: juros.toFixed(2),
      amortizacao: amortizacao.toFixed(2),
      saldo: saldo.toFixed(2),
    });
  }
  table.push({
    numero: "Total",
    pmt: pt.toFixed(2),
    juros: jt.toFixed(2),
    amortizacao: at.toFixed(2),
    saldo: 0,
  });
  return table;
}

// coeficiente financeiro
// @param p: parcelas
// @param t: taxa
function CF(p, t) {
  return t / (1 - 1 / (1 + t) ** p);
}
function PMT(pv, cf) {
  return pv * cf;
}
var table = [];
var total = [];
var data_exemple = [
  {
    numero: 0,
    pmt: 0,
    juros: 0,
    amortizacao: 0,
    saldo: 0,
  },
];
$(document).ready(function () {
  $("#price-table").DataTable({
    data: data_exemple,
    columns: [
      { data: "numero", title: "Numero" },
      { data: "pmt", title: "Prestação" },
      { data: "juros", title: "juros" },
      { data: "amortizacao", title: "Amortização" },
      { data: "saldo", title: "Saldo" },
    ],
  });
  $("#pricefieldset").hide();
  $("#newtonfildset").hide();
  $("#consumidorfieldset").hide();
});

//making dragabble
$(function () {
  $(".draggable").draggable();
});

webshims.setOptions("forms-ext", {
  replaceUI: "auto",
  types: "number",
});
webshims.polyfill("forms forms-ext");

function getINterest() {
  let t1 = x / y;
  let t = 0;
  let n = 0;
}

function calcTaxaJuros(precoAVista, precoAPrazo, numParcelas, temEntrada) {
  const tolerancia = 0.0001;
  let taxaJuros = 0.1; // palpite inicial
  let taxaJurosAnterior = 0.0;
  let funcao = 0.0;
  let derivada = 0.0;
  for (let i = 0; Math.abs(taxaJurosAnterior - taxaJuros) >= tolerancia; i++) {
    taxaJurosAnterior = taxaJuros;
    funcao = calcularValorFuncao(
      precoAPrazo,
      taxaJuros,
      precoAVista,
      numParcelas,
      temEntrada,
    );
    console.log("funcao", funcao);
    derivada = calcularValorDerivadaFuncao(
      precoAPrazo,
      taxaJuros,
      precoAVista,
      numParcelas,
      temEntrada,
    );

    console.log("derivada", derivada);
    if (derivada == 0) {
      return 0;
    }
    if (taxaJuros <= 0.0001) {
      return (taxaJuros = 0);
    }
    taxaJuros = taxaJuros - funcao / derivada;
  }
  return taxaJuros;
}

function calcularValorFuncao(
  preceAprazo,
  taxaJuros,
  precoVista,
  numParcelas,
  temEntrada,
) {
  let a = 0;
  let b = 0;
  let c = 0;
  if (temEntrada) {
    a = Math.pow(1 + taxaJuros, numParcelas - 2);
    b = Math.pow(1 + taxaJuros, numParcelas - 1);
    c = Math.pow(1 + taxaJuros, numParcelas);
    precoVista * taxaJuros * b - (preceAprazo / numParcelas) * (c - 1);
    return precoVista * taxaJuros * b - (preceAprazo / numParcelas) * (c - 1);
  } else {
    a = Math.pow(1 + taxaJuros, -numParcelas);
    b = Math.pow(1 + taxaJuros, -numParcelas - 1);
    return precoVista * taxaJuros - (preceAprazo / numParcelas) * (1 - a);
  }
}

function calcularValorDerivadaFuncao(
  preceAprazo,
  taxaJuros,
  precoAVista,
  temEntrada,
  numParcelas,
) {
  let a = 0;
  let b = 0;
  if (temEntrada) {
    a = Math.pow(1 + taxaJuros, numParcelas - 2);
    b = Math.pow(1 + taxaJuros, numParcelas - 1);
    return (
      precoAVista * (b + taxaJuros * a * (numParcelas - 1)) - preceAprazo * b
    );
  } else {
    a = Math.pow(1 + taxaJuros, -numParcelas);
    b = Math.pow(1 + taxaJuros, -numParcelas - 1);
    return precoAVista - preceAprazo * b;
  }
}

$("#submitButton").click(function () {
  var errorMessage = "";
  let ipp = $("#ipp").val();
  let itax = $("#itax").val() * 0.01;
  let ipv = $("#ipv").val();
  let np = $("#parc").val();
  let pb = $("#ipb").val();
  let volta = $("#volta").val();
  let idp = $("#idp").val();
  console.log("ipp:", ipp);
  console.log("itax:", itax);
  console.log("ipv:", ipv);
  console.log("np:", np);
  console.log("pb:", pb);
  console.log("volta:", volta);
  console.log("idp:", idp);
  console.log("Entrou dentro do click");
  if (itax == 0 && ipp == 0) {
    errorMessage +=
      "<p>Taxa de juros e valor final não podem ser ambos nulos.</p>";
  }
  if (itax == 0 && ipv == 0) {
    errorMessage +=
      "<p>Taxa de juros e valor financiado não podem ser ambos nulos.</p>";
  }
  if (ipv == 0 && ipp == 0) {
    errorMessage +=
      "<p>Valor financiado e valor final não podem ser ambos nulos.</p>";
  }
  if (errorMessage != "") {
    $("#errorMessage").html(errorMessage);
    $("#errorMessage").show();
    $("#successMessage").hide();
    event.preventDefault();
  } else {
    $("#successMessage").show();
    $("#errorMessage").hide();
    cf = CF(np, itax);
    pmt = PMT(ipv, cf);
    table = priceTable(np, ipv, itax, pmt);
    end = table.splice(table.length - 1, table.length);
    console.log(table);
    $("#price-table").DataTable().clear().draw();
    console.log("limpou");
    $("#price-table").DataTable().rows.add(table).draw();
    console.log(end);
    $("#cdcfieldset").hide();
    $("#pricefieldset").show();
    $("#newtonfildset").show();
    $("#total-prestacao").html(end[0].pmt);
    $("#total-juros").html(end[0].juros);
    $("#total-amortizacao").html(end[0].amortizacao);
    $("#total-saldo").html(end[0].saldo);
    $("#newton-cf").html(cf);
    $("#newton-prestacao").html(`${ipv}*$${cf} = ${pmt.toFixed(2)}`);
    $("#newton-valorPago").html(end[0].pmt);
    // calcular taxa Real
    // precoAVista,
    // precoAPrazo,
    // numParcelas,
    // temEntrada,
    taxaReal = calcTaxaJuros(ipp, pmt, np, idp);
    console.log("taxaReal:", taxaReal);
    $("#newton-taxaReal").html(taxaReal);
    $("#newton-valorCorrigido").html();
  }
});
