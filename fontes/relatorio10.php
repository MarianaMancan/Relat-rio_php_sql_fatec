<?php

require_once("../toolskit.php");
# Dentro deste arquivo existe a função que 'conecta' o PA ao SGBD postgresql na base ibd100cppk2.
$bloco=( ISSET($_POST['bloco']) ) ? $_POST['bloco'] : 1;
$cordefundo=($bloco<3) ? '#FFDEAD' : '#FFFFFF';
iniciapagina($cordefundo,"Cursos,Disciplinas e livros","Relatorio 10");
$sair=(ISSET($_REQUEST['sair'])) ? $_REQUEST['sair']+1 : 1;
# Separador de Blocos Lógicos do programa
switch (TRUE)
{
  case ( $bloco==1 ):
  { # este bloco monta o form e passa o bloco para o valor 2 em modo oculto
  printf("A partir de um curso escolhido mostre quais são as suas disciplinas e livros usados em cada uma.<hr>\n");
    printf(" <form action='./relatorio10.php' method='post'>\n");
    printf("  <input type='hidden' name='bloco' value=2>\n");
    printf("  <input type='hidden' name='sair' value='$sair'>\n");
    printf("  <table>\n");
    printf("   <tr><td colspan=2>Escolha a <negrito>ordem</negrito> como os dados serão exibidos no relatório:</td></tr>\n");
    printf("   <tr><td>DisciplinaS................:</td><td>(<input type='radio' name='ordem' value='disciplinas.txnomedisciplina'>)</td></tr>\n");
   printf("   <tr><td>Livros................:</td><td>(<input type='radio' name='ordem' value='livros.txtituloacervo'>)</td></tr>\n");
    
   
   $cmdsql="SELECT cursos.cpcurso, cursos.txnomecurso from cursos order by cpcurso";
   $execcmd=pg_query($dbp,$cmdsql);
  
    printf("</td></tr>\n");
   
    printf("<select name='cpcurso'>");
    printf("<option value='TODAS'>Todas</option>");
    while ( $reg=pg_fetch_array($execcmd) )
    {
      printf("<option value='$reg[cpcurso]'>$reg[txnomecurso] - ($reg[cpcurso])</option>");
    }
    printf("</select>\n");
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
  
    $selecao=" where cursos.cpcurso='$_REQUEST[cpcurso]'" ;
    $selecao=( $_REQUEST['cpcurso']!='TODAS' ) ? '$_REQUEST[cpcurso]' : $selecao ;
    $cmdsql="SELECT cursos.cpcurso,cursos.txnomecurso,disciplinas.txnomedisciplina,
    livros.txtituloacervo from cursos 
    inner join disciplinas on cursos.cpcurso = disciplinas.cecurso
    inner join bibliografia on bibliografia.cedisciplina = disciplinas.cpdisciplina
    inner join livros ON livros.cplivro = bibliografia.celivro".$selecao."  ORDER BY $_REQUEST[ordem]";
    # $cmdsql="SELECT * FROM medicostotal AS M".$selecao." ORDER BY $_REQUEST[ordem]";
    # printf("<br><br><br><br>$cmdsql<br>\n");
    $execsql=pg_query($dbp,$cmdsql);
    printf("<br><br><table class='borda'>\n");
    printf(" <tr><td class='borda'class='borda' >Cod. Curso</td>\n");
    printf("     <td class='borda' >Nome curso</td>\n");
    printf("     <td class='borda' >Nome disciplina</td>\n");
    printf("     <td class='borda' >Nome livro</td>\n");
 
    $corlinha="White";
    while ( $le=pg_fetch_array($execsql) )
    {
      printf("<tr bgcolor=$corlinha><td>$le[cpcurso]</td>\n");
      printf("   <td class='borda'>$le[txnomecurso]</td>\n");
      printf("   <td class='borda'>$le[txnomedisciplina]</td>\n");
      printf("   <td class='borda'>$le[qtxtituloacervo]</td>\n");
      $corlinha=( $corlinha=="White" ) ? "Navajowhite" : "White";
    }
    printf("</table>\n");
    if ( $bloco==2 )
    {
      printf("<form action='./relatorio10.php' method='POST' target='_NEW'>\n");
      printf(" <input type='hidden' name='bloco' value=3>\n");
      printf(" <input type='hidden' name='sair' value='$sair'>\n");
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