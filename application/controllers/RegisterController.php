<?php
/**
 * This file is part of oTranCe http://www.oTranCe.de
 *
 * @package         oTranCe
 * @subpackage      Controllers
 * @version         SVN: $Rev$
 * @author          $Author$
 */
/**
 * Register Controller
 *
 * @package         oTranCe
 * @subpackage      Controllers
 */
class RegisterController extends Zend_Controller_Action
{
    /**
     * Register a user
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->isLogin = true;
        $request = $this->getRequest();
        if ($request->isPost()) {
            $userModel = new Application_Model_User();
            $userData = $request->getParam('user');
            $userData['id'] = 0;
            $userData['active'] = 0;

            if ($userModel->validateData($userData, $this->view->lang->getTranslator())) {
                $userModel->saveAccount($userData);
                $this->view->registerSuccess = true;
            } else {
                $this->view->errors = $userModel->getValidateMessages();
            }
        }
    }
}