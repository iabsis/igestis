<?php

/**
 * Description of AdminController
 *
 * @author Gilles Hemmerlé
 */
class TestController extends IgestisController {
    public function ldapAction() {
        $repository = \Igestis\Ldap\Repository::newInstance();

        try {
            $repository->bind();
        }
        catch(\Igestis\Ldap\BindException $e) {
            echo $e->getMessage();
        }
        
        \Igestis\Utils\Dump::show($repository);
        
    }
}

?>
