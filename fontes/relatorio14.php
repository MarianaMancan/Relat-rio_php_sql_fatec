<?php

require_once("../toolskit.php");
# Dentro deste arquivo existe a função que 'conecta' o PA ao SGBD postgresql na base ibd100cppk2.
$bloco=( ISSET($_POST['bloco']) ) ? $_POST['bloco'] : 1;
$cordefundo=($bloco<3) ? '#FFDEAD' : '#FFFFFF';
iniciapagina($cordefundo,"Resumo produtos","Relatorio 14");
$sair=(ISSET($_REQUEST['sair'])) ? $_REQUEST['sair']+1 : 1;
# Separador de Blocos Lógicos do programa
switch (TRUE)
{
  case ( $bloco==1 ):
  { # este bloco monta o form e passa o bloco para o valor 2 em modo oculto
  printf("Tendo duas datas referenciais elabore um relatório que apresente um resumo dos produtos comprados pelos clientes totalizando os valores e as quantidades de cada produto.<hr>\n");
    printf(" <form action='./relatorio14.php' method='post'>\n");
    printf("  <input type='hidden' name='bloco' value=2>\n");
    printf("  <input type='hidden' name='sair' value='$sair'>\n");
    printf("  <table>\n");
    printf("   <tr><td colspan=2>Escolha a <negrito>ordem</negrito> como os dados serão exibidos no relatório:</td></tr>\n");
    printf("   <tr><td>Código do Produto..............:</td><td>(<input type='radio' name='ordem' value='nfvendasitens.ceproduto' checked>)</td></tr>\n");
    printf("   <tr><td>Nome do Produto................:</td><td>(<input type='radio' name='ordem' value='produtos.txnomeproduto'>)</td></tr>\n");
   
    $cmdsql="SELECT nfvendasitens.ceproduto,  produtos.txnomeproduto,     
    ROUND(SUM(nfvendasitens.qtvendida::numeric * nfvendasitens.vlunitario::numeric), 2) AS valor_total,
  SUM(nfvendasitens.qtvendida) AS quantidade_vendida
FROM nfvendasitens
INNER JOIN nfvendas ON nfvendas.cpnunfvenda = nfvendasitens.cenunfvenda
INNER JOIN PRODUTOS ON PRODUTOS.cpproduto = nfvendasitens.ceproduto
GROUP BY nfvendasitens.ceproduto,produtos.txnomeproduto";
    $execcmd=pg_query($dbp,$cmdsql);
  
    printf("</td></tr>\n");
    $dtini=date("Y-m-d");
    $dtfim=date("Y-m-d");
    printf("<tr><td>Intervalo de datas de cadastro:</td><td><input type='date' name='dtcadini' value='$dtini'> até <input type='date' name='dtcadfim' value='$dtfim'></td></tr>");
    printf("   <tr><td></td><td>");
    botoes(TRUE,TRUE,FALSE,TRUE,"Listar",$sair);
#    printf("<button type='submit'>Listar</button>\n"); # - <font size=6>&#x1f5a8;</font>
#    printf("<button type='button' onclick='history.go(-1)'><font size=5>&#x2397;</font></button>\n");
#    printf("<button type='button' onclick='history.go(-$sair)'>Sair</button>\n"); # - <font size=5>&#x2348;</font>
    printf("</td></tr>\n");
    printf("  </table>\n");
    printf(" </form>\n");
    break;
  }
  case ( $bloco==2 || $bloco==3 ):
  { # Este bloco vai processar a junção de medicos com instituicaoensino, logradouroscompletos (moradia e clinica) e especiaidadesmedicas.
    # Depois monta a tabela com os dados e a seguir um form permitindo que a listagem seja exibida para impressão em uma nova aba.
    $selecao=" WHERE (nfvendas.dtvenda between '$_REQUEST[dtcadini]' and '$_REQUEST[dtcadfim]')";
    
    $cmdsql="SELECT nfvendasitens.ceproduto,  produtos.txnomeproduto,     
    ROUND(SUM(nfvendasitens.qtvendida::numeric * nfvendasitens.vlunitario::numeric), 2) AS valor_total,
  SUM(nfvendasitens.qtvendida) AS quantidade_vendida
FROM nfvendasitens
INNER JOIN nfvendas ON nfvendas.cpnunfvenda = nfvendasitens.cenunfvenda
INNER JOIN PRODUTOS ON PRODUTOS.cpproduto = nfvendasitens.ceproduto".$selecao." GROUP BY nfvendasitens.ceproduto,produtos.txnomeproduto  ORDER BY $_REQUEST[ordem]";
    # $cmdsql="SELECT * FROM medicostotal AS M".$selecao." ORDER BY $_REQUEST[ordem]";
    # printf("<br><br><br><br>$cmdsql<br>\n");
    $execsql=pg_query($dbp,$cmdsql);
    printf("<br><br><table class='borda'>\n");
    printf(" <tr><td class='borda'class='borda' >Cod.</td>\n");
    printf("     <td class='borda' >Nome Produto</td>\n");
    printf("     <td class='borda' >Valor total</td>\n");
    printf("     <td class='borda' >Quantidade vendida</td>\n");
 
    $corlinha="White";
    while ( $le=pg_fetch_array($execsql) )
    {
      printf("<tr bgcolor=$corlinha><td>$le[ceproduto]</td>\n");
      printf("   <td class='borda'>$le[txnomeproduto]</td>\n");
      printf("   <td class='borda'>$le[valor_total]</td>\n");
      printf("   <td class='borda'>$le[quantidade_vendida]</td>\n");
      $corlinha=( $corlinha=="White" ) ? "Navajowhite" : "White";
    }
    printf("</table>\n");
    if ( $bloco==2 )
    {
      printf("<form action='./relatorio14.php' method='POST' target='_NEW'>\n");
      printf(" <input type='hidden' name='bloco' value=3>\n");
      printf(" <input type='hidden' name='sair' value='$sair'>\n");
      printf(" <input type='hidden' name='dtcadini' value=$_REQUEST[dtcadini]>\n");
      printf(" <input type='hidden' name='dtcadfim' value=$_REQUEST[dtcadfim]>\n");
      printf(" <input type='hidden' name='ordem' value=$_REQUEST[ordem]>\n");
      # <button type='submit'>Impressão</button>
      botoes(TRUE,TRUE,FALSE,FALSE,"Imprimir",$sair);
#      printf(" <button type='submit'                             >Imprimir</button>\n"); # - <font size=6>&#x1f5a8;</font>
#      printf(" <button type='button' onclick='history.go(-1)'    >Voltar</button>\n"); # - <font size=5>&#x2397;</font>
#      printf(" <button type='button' onclick='history.go(-$sair)'>Sair</button>\n"); # - <font size=5>&#x2348;</font>
      printf("</form>\n");
    }
    else
    {
      printf("<hr>\n<button type='submit' onclick='window.print();'>Imprimir</button> - Corte a folha na linha acima.\n");
    }
    break;
  }
}
terminapagina("Produtos","Listar","produtoslistar.php");

?>