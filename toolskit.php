<?php
###################################################################################################################################################################################
# Programa....: toolskit
# Descrição...: Conjunto com as funções desenvolvidas para facilitar a construção de programas. Todas as funções são mantidas em arquivo para facilitar a edição e estrutura.
#               Este arquivio DEVE estar localizado no diretório FNTS (um nível acima do diretório onde devem estar os arquivos dos programas de manutenção de dados de uma tabela).
# Autor.......: João Maurício Hypólito - Use! Mas fale quem fez!
# Criação.....: 2014-11-10
# Atualização.: 2017-05-10 - Reorganização das função com mudança em parâmetros nas funções
#               2018-04-27 - Inclui a função concecta_my
###################################################################################################################################################################################
# Trecho de declaração das funções. Para cada uma apresentamos um cabeçalho curto com nome/parâmetros/descrição/histórico de atualizações e objetivo
#----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function iniciapagina($cordefundo,$tab,$titulo)
{ # Função.....: iniciapagina
  # Parametros.: Cor de fundo da página ($cordefundo), a cor do fonte das telas ($corfonte), texto com a funcionalidade em execução ($acao).
  # Descrição..: Emite as TAGS que iniciam uma tela com a cor de fundo padrao, alinha o texto com um TAB para a direita e a determina o fonte do projeto.
  ###################################################################################################################################################################################
  # Autor......: João Maurício Hypólito - Use! Mas fale quem fez!
  # Criação....: 2009-09-23
  # Atualização: 2018-04-27 - Tirei a variável $titulo colocando os operadores ternários dentro do printf();
  ###################################################################################################################################################################################
  printf("<html xml:lang='pt-BR' lang='pt-BR' dir='ltr'>\n");
  # declara o conjunto de caracteres universais (UTF-8)
  printf("<head>\n");
  printf("  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n"); # ISO-8859-1
  printf("  <link rel='stylesheet' type='text/css' href='./style.css'>\n");
  printf("</head>\n");
  # inicia o corpo da pagina com a cor indicada no parametro
  $cor=($cordefundo=='#FFDEAD') ? "navajowhite" : "white" ;
  printf("<body class='$cor'>\n");
  # posiciona os textos com um TAB para a direita. Este alinhamento melhora a visibilidade da tela.
  printf("<dir>\n");
  printf("<vermelhoforte>$titulo</vermelhoforte> - $tab<br>\n");
  ################################ Fim da Função IniciaPagina ################################
}
#----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function botoes($page,$menu,$sair,$rset,$acao,$salto)
{ # Função.....: botoes
  # Parametros.: Esta Função recebe TRUE|FALSE para os parâmetros que apontam para montar as tags de exibição dos botões de navegação
  # Descrição..: Esta Função emite as TAGS para "< 1 Pag.", "< Menu","Saída","Limpar" e "Ação"
  #################################################################################################################################################################################
  # Autor......: João Maurício Hypólito - Use! Mas fale quem fez!
  # Criação....: 2017-05-31
  # Atualização: 2017-05-31 - Todo desenvolvimento e teste da função.
  #              2018-04-27 - Alterei a ordem dos textos que formam a barra de botões.
  #################################################################################################################################################################################
  $barra=(ISSET($acao)) ? "<button type='submit'>$acao</button>" : "";
  $barra=($rset) ? $barra."<button type='reset'>Limpar</button>" : $barra;
  $barra=($page) ? $barra."<button type='button' onclick='history.go(-1)'>< 1 Pag.</button>" : $barra;
  $barra=($menu) ? $barra."<button type='button' onclick='history.go(-$salto)'>< Menu</button>" : $barra;
  $barra=($sair) ? $barra."<button type='button' onclick='history.go(-($salto+1))'>< Sa&iacute;da</button>" : $barra;
  printf("$barra<br>\n");
}
#----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function terminapagina($texto,$prg,$center)
{ # Função.....: terminapagina
  # Parametros.: $texto - descreve a ação (apresentado no lado esquerdo da linha de rodapé),
  #              $prg - código do programa (apresentado lado direito da linha de rodapé) e
  #              $center - TRUE/FALSE para colocar a linha de rodapé centralizada ou não.
  # Descrição..: Esta Função emite uma linha no final da página e coloca uma mensagem de Autoria.
  #################################################################################################################################################################################
  # Autor......: João Maurício Hypólito - Use! Mas fale quem fez!
  # Criação....: 2009-03-27
  # Atualização: 2009-09-17
  #################################################################################################################################################################################
  printf("%s",($center) ? "<center>" : "" ); # Este comando combina um operador ternário DENTRO print().
  printf("<font size=2 color='gray'>$texto - Resolu&ccedil;&atilde;o m&iacute;nima de 1280x720 &copy; Copyright %s, FATEC Ourinhos - $prg</font>\n",date('Y'));
  printf("</dir>\n</font>\n"); # Estas duas TAGS fecham TAGS aberta no iniciapágina.
  printf("%s</body>\n</html>\n",($center) ? "</center>" : "" );
  ################################ Fim da Função terminapagina ################################
}
#----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function mostracampos($campo,$entidade,$pk,$valor)
{ # Função.....: mostracampos
  # Parametros.: $campo - nome do campo que deve ter seu valor retornado,
  #              $entidade - nome da entidade onde o campo está,
  #              $pk - nome da chave primária da tabela e
  #              $valor - valor assumido na $pk
  # Descrição..: Esta Função retorna no ponto de chamada o valor do campo da tabela que foi projetado.
  #################################################################################################################################################################################
  # Autor......: João Maurício Hypólito - Use! Mas fale quem fez!
  # Criação....: 2009-03-27
  # Atualização: 2009-09-17
  #################################################################################################################################################################################
  global $dbp;
  # em um só comando Projeta e retorna o valor de um campo como resposta da função.
  return mysql_result(mysql_query($dbp,"SELECT $campo FROM $entidade WHERE $pk='$valor'"),0,"$campo");
    # return pg_result(pg_query($dbp,"SELECT $campo FROM $entidade WHERE $pk='$valor'"),0,"$campo");
  ################################ Fim da Função mostracampos ################################
}
#----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function conecta_pg($host,$porta,$dbname,$user,$senha)
{ # Função.....: conecta_pg
  # Descrição..: Esta função faz monte a conexão com o SGBD PostgreSQL
  # Observação.: $host   - Nome do Host que executa o serviço do SGBD (localhost, para o servidor local)
  #              $porta  - Número da porta onde o servidor de banco de dados pode ser referenciado
  #              $dbname - Nome da Base de Dados que será acessada
  #              $user   - Nome do usuário que tem acesso permitido na Base (ver permissões com o DBA do SGBD)
  #              $senha  - Senha de conexão do usuário na base (e no SGBD).
  # Autor......: João Maurício Hypólito - Use! Mas fale quem fez!
  # Criação....: 2013-05-02
  # Alteração..: 2014-10-15
  #              2017-01-20 - inclui o parâmetro $porta para receber o número da porta para o caso de alguém ter mudado o número na instalação do PostgreSQL.
  #################################################################################################################################################################################
  $con_string = "host='".$host."' port=".$porta." dbname='".$dbname."' user='".$user."' password='".$senha."'";
  # Conectando o PostgreSQL. O Ponteiro que retorna na conexão DEVE SER armazenado em uma variavel GLOBAL.
  global $dbp;
  # Fazendo a conexão com o banco de dados.
  $dbp = pg_connect($con_string) or die ("Problemas para Conectar no Banco de Dados PostgreSQL: <br>$con_string");
  # Agora vamos 'ajustar' os caracteres acentuados
  pg_query("SET NAMES 'utf8'");
  pg_query("SET CLIENT_ENCODING TO 'utf8'");
  pg_set_client_encoding('utf8'); # para a conexão com o PostgreSQL
  # Fim da função conecta_pg
  #################################################################################################################################################################################
}
function conecta_my($host,$dbname,$user,$senha)
{ # Função.....: conecta_my
  # Descrição..: Esta função faz monte a conexão com o SGBD PostgreSQL
  # Observação.: Recebe 4 parâmetros: $host   - Nome do Host que executa o serviço do SGBD (localhost, para o servidor local)
  #                                   $dbname - Nome da Base de Dados que será acessada
  #                                   $user   - Nome do usuário que tem acesso permitido na Base (ver permissões com o DBA do SGBD)
  #                                   $senha  - Senha de conexão do usuário na base (e no SGBD).
  # Autor......: João Maurício Hypólito - Use! Mas fale quem fez!
  # Criação....: 2013-05-02
  # Alteração..: 2014-10-15
  ################################################################################################################
  # Fazendo a conexão com o banco de dados.
  # Atribuicao de: - Nome de servidor, Nome do usuario, Senha do usuario e Base de dados
  global $dbm;
  $dbm=mysql_pconnect($host,$user,$senha) or die ("Erro ao conectar ao Banco");
  $banco=mysql_select_db($dbname,$dbm) or die ("Problemas ao escolher a base de dados");
  # Acertando a tabela de caracteres que sera usada no MySQL
  mysql_query("SET NAMES 'utf8'");
  mysql_query('SET character_set_connection=utf8');
  mysql_query('SET character_set_client=utf8');
  mysql_query('SET character_set_results=utf8');
  # aqui termina o trecho de conexão com o banco de dados
  ################################################################################################################
}
#----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
#----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
###################################################################################################################################################################################
# Aqui termina a declaração das Funções.
# EXECUTANDO a função de CONEXÃO
################################################### Fim das Funções ###################################################
# Aqui começa o bloco principal do programa ToolsKit
header('content-type: text/html; charset=utf-8');
###################################################################################################################################################################################
# Para fazer a conexão com o MySQL executamos a função conecta_my com os 4 parâmetros: hostname, database name, username e password
# conecta_my("localhost","lbdsql2termino","root",""); # Com esta linha comentada NÃO se executa a conexão.
# Troque os valores dos parâmetros indicados: NomeDaSUABase, Usuario e Senha para os valores usados na sua configuração.
conecta_pg('localhost',5432,'praticasql','postgres','1234' );

?>