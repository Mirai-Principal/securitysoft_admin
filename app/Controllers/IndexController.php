<?php
namespace App\Controllers;

use App\Models\usuarios;

use Illuminate\Database\Capsule\Manager as Capsule;      //? conexion con la base de datos usando Query Builder
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;


class IndexController extends CoreController{
    public function indexAction(){
        if( isset($_SESSION['ruc']) )
            return new RedirectResponse('/dashboard');
        else
            return $this->renderHTML('index.twig');
    }

    public function postLoginAction(ServerRequest $request){
        $responseMessage = null;    //var para recuperar los mesajes q suceda durante la ejecucion

        if ($request->getMethod() == "POST") {
            $postData = $request->getParsedBody();
            $usuariosValidator = v::key('ruc', v::stringType()->noWhitespace()->notEmpty())
            ->key('password', v::stringType()->notEmpty()->noWhitespace());

            try {
                $usuariosValidator->assert($postData);   //? validando

                $usuario = new usuarios();
                $existeusuario = $usuario
                                ->where("ruc", $postData['ruc'])
                                ->first();
                if( $existeusuario )
                    if ( password_verify( $postData['password'], $existeusuario->password) ){
                        $_SESSION['nombre_empresa'] = $existeusuario->nombre_proveedor;
                        $_SESSION['ruc'] = $existeusuario->ruc;
                        return new RedirectResponse('/dashboard');
                    }else{
                        $responseMessage = 'Credenciales incorrectas o el usuario no existe';
                    }
                else
                    $responseMessage = 'Credenciales incorrectas o el usuario no existe';
                            
            } catch (\Exception $e) {
                $responseMessage = 'Credenciales incorrectas o el usuario no existe';
                // $responseMessage = $e->getMessage();
            }
        }

        //mensaje de retorno a la misma vista con un alert
        return $this->renderHTML('index.twig', [
            'responseMessage' => assetsControler::sweetAlert($responseMessage, 'error')
        ]);
    }

    public function getFormSignupAction(){
        if( isset($_SESSION['ruc']) )
            return new RedirectResponse('/dashboard');
        else
            return $this->renderHTML('signup.twig');
    }

    public function postSignupAction(ServerRequest $request){
        $responseMessage = null;    //var para recuperar los mesajes q suceda durante la ejecucion

        if ($request->getMethod() == "POST") {
            $postData = $request->getParsedBody();

            $validator = v::key('password_nuevo', v::stringType()->noWhitespace()->notEmpty())
            ->key('telefono', v::stringType()->notEmpty()->noWhitespace())
            ->key('ruc', v::stringType()->notEmpty()->noWhitespace())
            ->key('nombre_empresa', v::stringType()->notEmpty());
            
            try {
                $validator->assert($postData);

                Capsule::beginTransaction();
                
                $usuario = new usuarios();
                $usuario->ruc = $postData['ruc'];
                $usuario->nombre_proveedor = $postData['nombre_empresa'];
                $usuario->telefono = $postData['telefono'];
                $postData['password_nuevo'] = password_hash($postData['password_nuevo'], PASSWORD_DEFAULT );
                $usuario->password = $postData['password_nuevo'];
                
                $usuario->save();
                Capsule::commit();

                $lastId = $usuario->id;
                $responseMessage = 'Se ha guardado con éxito';

                $_SESSION['nombre_empresa'] = $usuario->nombre_empresa;
                $_SESSION['ruc'] = $usuario->ruc;

                return new RedirectResponse('/dashboard');
            } catch (\Exception $e) {
                // $responseMessage = $e->getMessage();
                Capsule::rollback();
                $responseMessage = $e->getCode();
                if ($responseMessage === '23000') 
                    $responseMessage = 'El usuario ya esta registrado';
                else
                    $responseMessage = 'Ha ocurrido un error! Informe a soporte';

            }
        }

        return $this->renderHTML('signup.twig', [
            'responseMessage' => assetsControler::sweetAlert($responseMessage, 'error')
        ]);
    }

    public function getLogoutAction(){
        unset($_SESSION['user_name']);
        session_destroy();
        return new RedirectResponse('/');
    }

    public function getTerminosYCondiciones(){
        return $this->renderHTML('terminos.twig', [
            'session' => $_SESSION
        ]);
    }

    public function getSobreNosotros(){
        return $this->renderHTML('sobreNosotros.twig', [
            'session' => $_SESSION
        ]);
    }
}

