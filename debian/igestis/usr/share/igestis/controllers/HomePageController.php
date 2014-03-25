<?php

/**
 * home page presentation
 *
 * @author Gilles Hemmerlé
 */
class HomePageController extends IgestisController {
    /**
     * Show the list of customers
     */
    public function indexAction() {
        Igestis\Utils\Debug::addLog("Start indexAction controller of the Homepage");
        
        $home_page_content = $this->context->entityManager->getRepository("CoreHomePageContent")->getHomePageContent();     
        
        Igestis\Utils\Debug::addLog("Custom home page content retrieved from the database");
        $replace = array();
        
        if($home_page_content != null){
            $content = $home_page_content->getContent();
            if($content != "") $replace['content_homepage'] = $content;
        }

        $replace["hideQuicktour"] = $this->context->security->contact->getQuicktourStatus();
        Igestis\Utils\Debug::addLog("Send data to the renderer ...");
        $this->context->render("pages/homePage.twig", $replace);
    }
    
    public function saveAction() {
        if($this->request->IsPost()) { 
            $page_content = new CoreHomePageContent();
            $page_content->setContent($_POST['content']);
            
            $this->context->entityManager->persist($page_content);
            $this->context->entityManager->flush();
            
            $this->redirect(ConfigControllers::createUrl("home_page"));
        }
    }
    
    public function restoreAction() {
        $page_content = new CoreHomePageContent();
        $page_content->setContent('');

        $this->context->entityManager->persist($page_content);
        $this->context->entityManager->flush();

        $this->redirect(ConfigControllers::createUrl("home_page"));
    }
    
    public function passwordAction() {
        if($this->request->IsPost()) {
            if(!trim($_POST['login']) || !isset($_POST['login'])) {
                new wizz(_("Thanks to complete this form in order to reset your password"), wizz::$WIZZ_ERROR);
                $this->redirect(IgestisConfigController::createUrl("reset_password"));
            }

            if(IgestisMisc::isEmail($_POST['login'])) {
                // Search contact by email
                $contacts = $this->context->entityManager->getRepository("CoreContacts")->findBy(array("email" => $_POST['login']));
            }
            else {
                // Search contact by login
                $contacts = $this->context->entityManager->getRepository("CoreContacts")->findBy(array("login" => $_POST['login']));
            }

            // If no users knwon, redirect him
            if($contacts == null) {
                new wizz(_("User not known"), wizz::$WIZZ_ERROR);
                $this->redirect(IgestisConfigController::createUrl("reset_password"));
            }

            foreach($contacts as $key => $value) {
                $contacts[$key]->resetPassword();
                $this->context->entityManager->persist($contacts[$key]);
                echo $contacts[$key]->getEmail() . " - " . $contacts[$key]->getChangePasswordRequestId() . "<br />\n";
            }

            try {
                $this->context->entityManager->flush();
                $message = IgestisMailer::init();

                $html = $this->context->render("mails/passwordReset.twig", array("contacts_list" => $contacts), true);

                $message
                // Give the message a subject
                    ->setSubject(_("Password reset request"))
                // Set the From address with an associative array
                    ->setFrom(array('info@iabsis.com' => 'Iabsis SàRL'))
                // Set the To addresses with an associative array
                    ->setTo(array($contacts[0]->getEmail() => $contacts[0]->getFirstname() . " " . $contacts[0]->getLastname()))
                // And optionally an alternative body
                    ->addPart($html, 'text/html')
                ;

                IgestisMailer::send($message);


            }
            catch(Exception $e) {
                IgestisErrors::createWizz($e);
                $this->redirect(IgestisConfigController::createUrl("reset_password"));
            }
            new wizz("An email has just been sent to your email address", wizz::$WIZZ_SUCCESS);
            $this->redirect(IgestisConfigController::createUrl("reset_password"));

        }

        $this->context->render("pages/resetPassword.twig", array());
    }
    
    
    public function confirmPasswordAction($key) {

        $contact = $this->context->entityManager->getRepository("CoreContacts")->findOneBy(array("changePasswordRequestId" => $key));
        if(!$contact) {
            new wizz(_("This password reset key code is unknown. Thanks to try the process again"), wizz::$WIZZ_ERROR);
            $this->context->render("pages/confirmPassword.twig", array("isValidCode" => false));
        }

        if($this->request->IsPost()) {
            $contact->setPassword($_POST['password']);
            $contact->setchangePasswordRequestId(null);

            try {
                $this->context->entityManager->persist($contact);
                $this->context->entityManager->flush();
                new wizz(_("The new password has been saved. You can try to login with your new password"), wizz::$WIZZ_SUCCESS);
                $this->redirect(IgestisConfigController::createUrl("home_page"));

            } catch(Exception $e) {
                IgestisErrors::createWizz($e);
            }
        }

        $this->context->render("pages/confirmPassword.twig", array("isValidCode" => true));
    }

    public function loginAction($Force=0) {
        if($Force == 1) {
            $this->context->security->logout();
        }
        $this->context->render("pages/homeLogin.twig", array());
    }
    
    public function quickTour() {
        $this->context->render("pages/quickTour.twig", array()
        );
    }
    
    public function help() {
      $this->context->render("pages/help.twig", array()
      );
    }
    
    public function newFeatureReport() {
      
      $account = $this->context->entityManager->getRepository("CoreUsers")->find($this->context->security->user->getId());
      
      if($this->request->IsPost()) {
        try {
          $this->context->entityManager->flush();
          $message = IgestisMailer::init();
        
          $html = $this->context->render("mails/newFeatureReport.twig",
              array(
                  'email' => $_POST["email"],
                  'requestdo' => $_POST["requestdo"],
                  'requestwhere' => $_POST["requestwhere"]),
              true);

          if($this->context->security->user->getCompany()) {
            $companyEmail = $this->context->security->user->getCompany()->getEmail();
            $companyName = $this->context->security->user->getCompany()->getName();
          } else {
            $companyEmail = "noreply@igestis.org";
            $companyName = "iGestis";
          }
          
          $message
          // Give the message a subject
          ->setSubject(_("New feature request"))
          // Set the From address with an associative array
          ->setFrom(array($companyEmail => $companyName))
          // Set the To addresses with an associative array
          ->setTo(array('info@iabsis.com' => 'Iabsis SàRL'))
          // And optionally an alternative body
          ->addPart($html, 'text/html')
          ;
        
          IgestisMailer::send($message);
        
        
        }
        catch(Exception $e) {
          IgestisErrors::createWizz($e);
          $this->redirect(IgestisConfigController::createUrl("newFeatureReport"));
        }
        new wizz(_("Thanks for your request, an email will be sent to igestis developpers."), wizz::$WIZZ_SUCCESS);
        $this->redirect(IgestisConfigController::createUrl("newFeatureReport"));
        
        }

      $this->context->render("pages/newFeatureReport.twig", array('form_data' => $account));
    }
    
    public function bugReport() {
      
      $account = $this->context->entityManager->getRepository("CoreUsers")->find($this->context->security->user->getId());
      
      if($this->request->IsPost()) {
        try {
          $this->context->entityManager->flush();
          $message = IgestisMailer::init();
      
          $html = $this->context->render("mails/newBugReport.twig",
              array(
                  'email' => $_POST["email"],
                  'bugdoing' => $_POST["bugdoing"],
                  'bugexpected' => $_POST["bugexpected"],
                  'bugappend' => $_POST["bugappend"]
                  ),
              true);
      
          $message
          // Give the message a subject
          ->setSubject(_("New bug report"))
          // Set the From address with an associative array
          ->setFrom(array('bug-report@iabsis.com' => 'Bug report'))
          // Set the To addresses with an associative array
          ->setTo(array('support-igestis@iabsis.com' => 'Form support'))
          // And optionally an alternative body
          ->addPart($html, 'text/plain')
          ;
      
          IgestisMailer::send($message);
      
      
        }
        catch(Exception $e) {
          IgestisErrors::createWizz($e);
          $this->redirect(IgestisConfigController::createUrl("bugReport"));
        }
        new wizz(_("Thanks for your request, an email will be sent to igestis developpers."), wizz::$WIZZ_SUCCESS);
        $this->redirect(IgestisConfigController::createUrl("bugReport"));
      
      }

      $this->context->render("pages/bugReport.twig", array('form_data' => $account));
    }
    
    public function about() {
      $this->context->render("pages/about.twig", array()
      );
    }
    
    public function demo() {
      $this->context->render("pages/demo.twig", array()
      );
    }
    
    public function error404() {
      $this->context->render("pages/error404.twig", array()
      );
    }
    
    public function error403() {
      $this->context->render("pages/error403.twig", array()
      );
    }
}

