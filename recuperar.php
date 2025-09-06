<?php

session_start();

//	if($_SESSION['id']!=""){
//	  header("location:painel.php");
//	}

?>
<!DOCTYPE html>
<html>
    <head>
        <!-- TÍTULO DA PÁGINA -->
        <title>Recuperar Senha </title>

        <!-- FAVICON -->
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

        <!-- TAGS -->
        <meta charset="UTF-8">
        <meta name="keywords" content="">
        <meta name="description" content="">
        <meta http-equiv="content-language" content="pt-br">
        <meta name="generator" content="NetBeans 8.x">
        <meta name="author" content="Marcos Marcolin e Wellton Costa de Oliveira">

        <!-- ESTILOS CSS -->
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <link href="css/header.css" rel="stylesheet" type="text/css" />
        <link href="css/footer.css" rel="stylesheet" type="text/css" />
        <link href="css/nivo-slider.css" rel="stylesheet" type="text/css" />

        <!-- JAVASCRIPT/JQUERY -->
        <script src="js/script.js" type="text/javascript"></script>

        <!-- FONTES EXTERNAS -->
        <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>

    </head>
<body>
    <!-- CABECALHO -->
    <?php
        include 'header.php';

	$pg = $_GET['pg'];

    ?>

    <!-- CORPO -->
    <section class="corpo">
        <section class="conteudo">
            <section class="programacao">
                <h2>Recuperação de Senha</h2>

            <aside class="c_login">
                <aside class="login">
			<form action="https://salin.fb.utfpr.edu.br/2024/mail/mail.php?senha=senha" method="POST">
                            Coloque seu email cadastrado e recupere sua senha
                         <?php
				$email=@$_GET["email"];
				if(@$_GET["erro"]==1)	echo "<span id='vermelho'>Email não cadastrado. </span>";
				if(@$_GET["erro"]==2)	echo "<span id='vermelho'>Tempo expirado. Se logue novamente.</span>";
				if(@$_GET["i"]==1)	echo "<span id='verde'>Enviamos um link de recuperação para seu email informado.</span>";
						?>
                        <br />
                        <input type="email" name="email" class="login_email" required autofocus placeholder="Email" value="<?php echo $email; ?>" />

                        <input type="submit" name="btn_logar" class="btn_logar" value="Recuperar Senha" /><br />
                     </form>
                    <br>

                    <center>
                <!-- section class="box-inscricao">
                    <a href="index.php#inscricoes">Inscreve-se na VIII Colin Camp</a>
                </section-->
            </section>
                </aside>
            </aside>
        </div>

        </section>
        <div class="clear"></div>
    </section>
    <!-- RODAPE -->
    <?php
        include 'footer.php';
    ?>
    <div class="clear"></div>
</body>
</html>
