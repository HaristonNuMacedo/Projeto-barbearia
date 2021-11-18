<?php


include_once 'C:/xampp/htdocs/Projeto-barbearia/dao/daoFuncionario.php';
include_once 'C:/xampp/htdocs/Projeto-barbearia/controller/funcionarioController.php';
include_once 'C:/xampp/htdocs/Projeto-barbearia/model/Usuario.php';
include_once 'C:/xampp/htdocs/Projeto-barbearia/model/mensagem.php';

$msg = new Mensagem();

?>

<!DOCTYPE html>
<html lang="pt-bt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinição de Senha</title>
    <link rel="sorcut icon" href="img/barber-shop.png" type="image/png" style="width: 16px; height: 16px;">
    <Link rel="stylesheet" href="css/style-funcioanarionovasenha.css">
</head>

<body>
    <div id="senhafuncionario">
    <script src="Js/sweetalert2.all.min.js"></script>
        <img src="img/barbearianeves.png" class="imagem">
        <form method="post">
            <label for="n_senha">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="n_senha">Nova senha:</label>
            <input type="password" id="nsenha" name="nsenha" required>

            <label for="c_senha">Confirmar senha:</label>
            <input type="password" id="csenha" name="csenha" required>

            <button type="submit" class="btn efeito-btn" name="esenha">Confirmar</button>
        </form>
    </div>
    <script>
        var senha = document.querySelector('#nsenha');

        senha.addEventListener('blur', (eventoLegal) => {
            verificaSenha(eventoLegal.target);
        })

        function verificaSenha(input) {
            var expSenha = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[$*&@#!"'%¨¬()+=§])[0-9a-zA-Z$*&@#!"'%¨¬()+=§]{8,}$/g;
            var senhaValida = expSenha.exec(input.value);
            var msgSenha = '';

            if (!senhaValida) {
                msgSenha = 'Precisa ter pelo menos 1 letra minúscula, maiúscula, número e caracter especial e ao menos 8 caracteres (!@#$&?*).';
            }

            input.setCustomValidity(msgSenha);

        }
    </script>
</body>

</html>