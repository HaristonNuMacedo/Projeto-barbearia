<?php

include_once 'C:/xampp/htdocs/Projeto-barbearia/bd/banco.php';
include_once 'C:/xampp/htdocs/Projeto-barbearia/model/Usuario.php';
include_once 'C:/xampp/htdocs/Projeto-barbearia/model/servicos_has_funcionarios.php';
include_once 'C:/xampp/htdocs/Projeto-barbearia/model/Mensagem.php';

class DaoFuncionario
{

    public function inserirFuncionarioDAO(Usuario $funcionario)
    {

        $conn = new Conecta();
        $msg = new Mensagem();
        $conecta = $conn->conectadb();

        if ($conecta) {

            $resp = null;
            $nome = $funcionario->getNome();
            $perfil = $funcionario->getPerfil();
            $telefone = $funcionario->getTelefone();
            $email = $funcionario->getEmail();
            $senha = $funcionario->getSenha();
            $sexo = $funcionario->getSexo();
            $verifica = "F";
            $token = $funcionario->getToken();


            try {
                $st = $conecta->prepare("SELECT * FROM usuario where email = ?");
                $st->execute([$email]);
                $result = $st->rowCount();
                if ($result > 0) {
                    $resp = $funcionario;
                } else {
                    $stmt = $conecta->prepare("insert into usuario values "
                        . "(null,?,?,?,?,md5(?),?, ?,?)");

                    $stmt->bindParam(1, $nome);
                    $stmt->bindParam(2, $perfil);
                    $stmt->bindParam(3, $telefone);
                    $stmt->bindParam(4, $email);
                    $stmt->bindParam(5, $senha);
                    $stmt->bindParam(6, $sexo);
                    $stmt->bindParam(7, $verifica);
                    $stmt->bindParam(8, $token);
                    $stmt->execute();
                    $resp = "<p style='color: green;'>"
                        . "Dados Cadastrados com sucesso</p>";
                }
            } catch (Exception $ex) {
                $resp = $ex;
            }
        } else {
            $resp = "<p style='color: red;'>"
                . "Erro na conexão com o banco de dados.</p>";
        }
        $conn = null;
        return $resp;
    }

    public function pesquisarFuncionarioDAO()  // Para listar os funcionários em ListarFuncionario.php
    {
        $msg = new Mensagem();
        $conn = new Conecta();
        $conecta = $conn->conectadb();
        //echo "<script>alert('Cheguei aqui')</script>";
        $lista = array();
        if ($conecta) {
            try {

                $rs = $conecta->query("select * from usuario where perfil = 'Funcionario' or perfil = 'Secretaria'");
                $a = 0;
                if ($rs->execute()) {
                    if ($rs->rowCount() > 0) {
                        while ($linha = $rs->fetch(PDO::FETCH_OBJ)) {
                            $cliente = new Usuario();
                            $cliente->setId($linha->id);
                            $cliente->setNome($linha->nome);
                            $cliente->setPerfil($linha->perfil);
                            $cliente->setSexo($linha->sexo);
                            $cliente->setEmail($linha->email);
                            $cliente->setTelefone($linha->telefone);
                            $lista[$a] = $cliente;
                            $a++;
                        }
                    }
                }
            } catch (Exception $ex) {
                $msg->setMsg($ex);
            }
            $conn = null;
        } else {
            echo "<script>alert('Banco inoperante!')</script>";
            echo "<META HTTP-EQUIV='REFRESH' CONTENT=\"0;
			 URL='../Projeto-Barbearia/index.php'\">";
        }
        return $lista;
    }

    public function atualizarSenhaFuncionarioDAO(Usuario $funcioanrio)
    {
        $conn = new Conecta();
        $msg = new Mensagem();
        $resp = null;
        $conecta = $conn->conectadb();
        if ($conecta) {

            $senha = $funcioanrio->getSenha();
            $email = $funcioanrio->getEmail();
            $verifica = 'S';
            $token = $funcioanrio->getToken();


            $st = $conecta->prepare("SELECT * FROM usuario where token = ? and email = ?");
            $st->execute([$token, $email]);

            $result = $st->rowCount();
            if ($result > 0) {
                try {

                    $stmt = $conecta->prepare("UPDATE usuario SET senha= md5(?), verifica = ? WHERE token = ?");


                    $stmt->bindParam(1, $senha);

                    $stmt->bindParam(2, $verifica);
                    $stmt->bindParam(3, $token);
                    $stmt->execute();
                    $msg->setMsg("<script>Swal.fire({
                            icon: 'success',
                            title: 'Senha alterada com sucesso',
                            timer: 2000
                          })
                          </script>");
                } catch (PDOException $ex) {
                    $msg->setMsg(var_dump($ex->errorInfo));
                }
            } else {
                $resp = $funcioanrio;
            }
        } else {
            $msg->setMsg("<script>Swal.fire({
                icon: 'error',
                title: 'Erro de conexão',
                text: 'Banco de dados pode estar inoperante',
                timer: 2000
              })</script>");
        }
        $conn = null;
        return $resp;
    }
    //método para buscar os dados de funcionario por id*
    public function pesquisarFuncionarioIdDAO($id)
    {
        $conn = new Conecta();
        $conecta = $conn->conectadb();
        $funcionario = new Usuario();
        $msg = new Mensagem();
        if ($conecta) {
            try {
                $conecta->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $rs = $conecta->prepare("select * from usuario where id = ?");
                $rs->bindParam(1, $id);
                if ($rs->execute()) {
                    if ($rs->rowCount() > 0) {
                        while ($linha = $rs->fetch(PDO::FETCH_OBJ)) {

                            $funcionario = new Usuario();
                            $funcionario->setId($linha->id);
                            $funcionario->setNome($linha->nome);
                            $funcionario->setPerfil($linha->perfil);
                            $funcionario->setSexo($linha->sexo);
                            $funcionario->setEmail($linha->email);
                            $funcionario->setTelefone($linha->telefone);
                        }
                    }
                }
            } catch (PDOException $ex) {
                $msg->setMsg(var_dump($ex->errorInfo));
            }
            $conn = null;
        } else {
            $msg->setMsg("<script>Swal.fire({
                icon: 'error',
                title: 'Erro de conexão',
                text: 'Banco de dados pode estar inoperante',
                timer: 2000
              })</script>");
        }
        return $funcionario;
    }

    public function excluirFuncionarioDAO($id)
    {
        $conn = new Conecta();
        $conecta = $conn->conectadb();
        $msg = new Mensagem();
        if ($conecta) {
            try {

                $conecta->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmt = $conecta->prepare("DELETE FROM servicos_do_funcionario WHERE funcionarios_id = ?");
                $stmt->bindParam(1, $id);
                $stmt->execute();

                $conecta->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmt = $conecta->prepare("delete from usuario "
                    . "where id = ?");
                $stmt->bindParam(1, $id);
                $stmt->execute();
                $msg->setMsg("<p style='color: green;'>" // colocar aqui o sweet alert dps
                    . "Funcionário excluído com sucesso.</p>");
            } catch (PDOException $ex) {
                $msg->setMsg(var_dump($ex->errorInfo));
            }
        } else {
            $msg->setMsg("<p style='color: red;'>'Banco inoperante!'</p>");
        }
        $conn = null;
        return $msg;
    }

    public function editarFuncionarioDAO($id)
    {
        $conn = new Conecta();
        $msg = new Mensagem();
        $conecta = $conn->conectadb();
        if ($conecta) {

            $id = $_POST['id'];
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $perfil = $_POST['perfil'];
            $sexo = $_POST['sexo'];
            $telefone = $_POST['telefone'];

            /*$msg->setMsg("<p style='color: blue;'>"
				. "'$email', '$senha'</p>"); */

            try {

                $conecta->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $rs = $conecta->prepare("select email from usuario where id = ?");
                $rs->bindParam(1, $id);
                $rs->execute();
                while ($linha = $rs->fetch(PDO::FETCH_OBJ)) {

                    $funcionario = new Usuario();

                    $funcionario->setEmail($linha->email);
                    $email_igual = $funcionario->getEmail();
                    $msg->setMsg("Não");
                    if ($email_igual == $email) {

                        $conecta->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        $stmt = $conecta->prepare("UPDATE usuario SET nome = ?, email = ?, perfil = ?, sexo = ?, telefone = ? WHERE id = ?");
                        $stmt->bindParam(1, $nome);
                        $stmt->bindParam(2, $email);
                        $stmt->bindParam(3, $perfil);
                        $stmt->bindParam(4, $sexo);
                        $stmt->bindParam(5, $telefone);
                        $stmt->bindParam(6, $id);
                        $stmt->execute();
                        $msg->setMsg("OK");
                    } else {
                        $conecta->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        $rs = $conecta->prepare("select * from usuario where email = ?");
                        $rs->bindParam(1, $email);
                        $rs->execute();
                        if ($rs->rowCount() > 0) {
                            while ($linha = $rs->fetch(PDO::FETCH_OBJ)) {

                                $funcionario = new Usuario();

                                $funcionario->setEmail($linha->email);
                                $msg->setMsg("Não");
                            }
                        } else {
                            $conecta->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $stmt = $conecta->prepare("UPDATE usuario SET nome = ?, email = ?, perfil = ?, sexo = ?, telefone = ? WHERE id = ?");
                            $stmt->bindParam(1, $nome);
                            $stmt->bindParam(2, $email);
                            $stmt->bindParam(3, $perfil);
                            $stmt->bindParam(4, $sexo);
                            $stmt->bindParam(5, $telefone);
                            $stmt->bindParam(6, $id);
                            $stmt->execute();
                            $msg->setMsg("OK");
                        }
                    }
                }
            } catch (PDOException $ex) {
                $msg->setMsg(var_dump($ex->errorInfo));
            }
        } else {
            $msg->setMsg("<script>Swal.fire({
			icon: 'error',
			title: 'Erro de conexão',
			text: 'Banco de dados pode estar inoperante',
			timer: 2000
		  })</script>");
        }
        $conn = null;
        return $msg;
    }

    public function ultimoIdInseridoDAO()
    {
        $conn = new Conecta();
        $conecta = $conn->conectadb();


        $conecta->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $rs = $conecta->prepare("SELECT MAX(id) AS sim FROM usuario");
        $rs->execute();
        while ($linha = $rs->fetch(PDO::FETCH_OBJ)) {

            $funcionario = new Usuario();
            $funcionario->setId($linha->sim);
        }

        return $funcionario;
    }

    public function inserirFuncionarioAssociativaDAO(Servicos_has_funcionarios $funcionario)
    {

        $conn = new Conecta();
        $msg = new Mensagem();
        $conecta = $conn->conectadb();

        if ($conecta) {

            $resp = null;

            $idServicos = $funcionario->getServicos_id();
            $idFuncionario = $funcionario->getFuncionarios_id();
            foreach ($idServicos as $valor_pergunta) {
                $valor_do_meu_checkbox = $valor_pergunta;
            }

            $qtd = count($valor_do_meu_checkbox);


            try {
                for ($i = 0; $i < $qtd; $i++) {
                    $stmt = $conecta->prepare("insert into servicos_do_funcionario (funcionarios_id, servicos_id) values "
                        . "(?,?)");

                    $stmt->bindParam(1, $idFuncionario);
                    $stmt->bindParam(2, $valor_do_meu_checkbox[$i]);

                    $stmt->execute();
                }
                $resp = "<p style='color: green;'>"
                    . "Dados Cadastrados com sucesso</p>";
            } catch (Exception $ex) {
                $resp = $ex;
            }
        } else {
            $resp = "<p style='color: red;'>"
                . "Erro na conexão com o banco de dados.</p>";
        }
        $conn = null;
        return $resp;
    }


    /*public function token(){
        $msg = new Mensagem();
        $conn = new Conecta();
        $conecta = $conn->conectadb();
        if ($conecta) {
            try {
                $rs = $conecta->query("select token from usuario where perfil = 'Funcionario'");
                if ($rs->execute()) {
                    if ($rs->rowCount() > 0) {
                        while ($linha = $rs->fetch(PDO::FETCH_OBJ)) {
                            $token = new Usuario();
                            $token->setToken($linha->token);
                        }
                    }
                }
            } catch (Exception $ex) {
                $msg->setMsg($ex);
            }
            $conn = null;
        } else {
            echo "<script>alert('Banco inoperante!')</script>";
        }
        return $token;
    }*/
}
