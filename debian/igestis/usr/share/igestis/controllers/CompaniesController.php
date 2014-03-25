<?php

/**
 * Companies management
 *
 * @author Gilles Hemmerlé
 */
class CompaniesController extends IgestisController {
    /**
     * Show the list of customers
     */
    public function indexAction() {
        $companies = $this->context->entityManager->getRepository("CoreCompanies")->getCompaniesList();     
        $this->context->render("pages/companiesList.twig", array(
            'table_data' => $companies)
        );
    }
    
    /**
     * Get a form to edit or validate it if the form is received
     */
    public function editAction($Id) {
        
        // If the form has been received, manage the form...
        if($this->request->IsPost()) {            
            // Check the form validity
            if($this->request->getPost("email") != "" && !is_email($this->request->getPost("email"))) $this->context->invalid_form("Invalid email format");
            if(!$this->request->getPost("name")) $this->context->invalid_form(_("The company name is a required field" ));
            
            $this->context->entityManager->beginTransaction();
            
            // Get the original company
            $company = $this->context->entityManager->getRepository("CoreCompanies")->find($Id);
            
            // Set the new datas to the company
            $parser = new IgestisFormParser();
            $company = $parser->FillEntityFromForm($company, $_POST);
            // Supression du fichier si demandé
            try {
                if($this->request->getPost("deleteLogo") == 1) {
                    if(is_file($company->getLogoFolder() . "/" . $company->getLogoFileName())) {
                        unlink($company->getLogoFolder() . "/" . $company->getLogoFileName());
                    }
                    
                    $company->setLogoFileName(null);
                }
            }
            catch(\Exception $e) {
                IgestisErrors::createWizz($e);
                $this->redirect(ConfigControllers::createUrl("companies_list"));
            }
            
            $files = $this->request->getUploadedFiles("logo")->getFirst();
            if($files != null) {
                try {
                    $fileTarget = $company->getLogoFolder() . "/" . $company->getId() . $files->getExtension();
                    //if(is_file($fileTarget)) unlink ($fileTarget);
                    move_uploaded_file($files->getTmpName(), $fileTarget);
                    $company->setLogoFileName($company->getId() . $files->getExtension());
                }
                catch(\Exception $e) {
                    IgestisErrors::createWizz($e);
                    $this->redirect(ConfigControllers::createUrl("companies_list"));
                }
            }
            
            // Save the company into the database   
            try {
                // Save the company into the database
                $this->context->entityManager->persist($company);
                $company->removeRights();
                
                $this->context->entityManager->flush();   
                
                // Save rights into the db
                @reset($_POST);
                foreach ($_POST as $key => $value) {
                    if(preg_match("/^right_/", $key)) {
                        
                        $right = new CoreCompanyRights;
                        $right->setModuleName(strtoupper(str_replace("right_", "", $key)))
                              ->setRightCode($value);                        
                        $company->addDefaultRight($right);
                    }
                }
                
                $this->context->entityManager->persist($company);
                $this->context->entityManager->flush();                
                $this->context->entityManager->commit();
            } catch (Exception $e) {
                // Show wizz to confirm the employee update
                $this->context->entityManager->rollback();
                IgestisErrors::createWizz($e, IgestisErrors::TYPE_ANY);
                $this->redirect(ConfigControllers::createUrl("companies_list"));
            }

            // Show wizz to confirm the company update
            new wizz(_("The company data have been successfully saved"), WIZZ::$WIZZ_SUCCESS);

            // Redirect to the company list
            $this->redirect(ConfigControllers::createUrl("companies_list"));
        }

        // If no form received, show the form
        $company = $this->context->entityManager->getRepository("CoreCompanies")->find($Id);
        $this->context->render("pages/companiesEdit.twig", array(
            'form_data' => $company,
            'all_modules_rights' => $this->context->security->getDefaultCompanyRights($Id)
          )
        );
    }
    
    /**
     * Delete the company
     */
    public function deleteAction($Id) {
        $company = $this->context->entityManager->getRepository("CoreCompanies")->find($Id);
        // Delete the company from the database
        try {
            $this->context->entityManager->remove($company);
            $this->context->entityManager->flush();
        } catch (Exception $e) {
            // Show wizz to alert user that the company deletion has not realy been deleted
            IgestisErrors::createWizz($e);
            $this->redirect(ConfigControllers::createUrl("companies_list"));
        }

        // Show wizz to confirm the employee update
        new wizz(_("The company has been successfully deleted"), WIZZ::$WIZZ_SUCCESS);

        // Redirect to the employee list
        $this->redirect(ConfigControllers::createUrl("companies_list"));
    }
    
    /**
     * Add a company
     */
    public function newAction() {
        if($this->request->IsPost()) {            
            // Check the form validity
            $this->context->entityManager->beginTransaction();
            
            if(!$this->request->getPost("name")) $this->context->invalid_form(_("The company name is a required field"));
            
            // Get the original company
            $company = new CoreCompanies();
            
            // Set the new datas to the company
            $parser = new IgestisFormParser();
            $company = $parser->FillEntityFromForm($company, $_POST);
            
            $files = $this->request->getUploadedFiles("logo")->getFirst();

            if($files != null) {
                try {
                    $fileTarget = $company->getLogoFolder() . "/" . $company->getId() . $files->getExtension();
                    //if(is_file($fileTarget)) unlink ($fileTarget);
                    move_uploaded_file($files->getTmpName(), $fileTarget);
                    $company->setLogoFileName($company->getId() . $files->getExtension());
                }
                catch(\Exception $e) {
                    IgestisErrors::createWizz($e);
                    $this->redirect(ConfigControllers::createUrl("companies_list"));
                }
            }
            
            // Save the company into the database   
            try {
                // Save the company into the database
                $this->context->entityManager->persist($company);
                $this->context->entityManager->flush();   
                
                // Save rights into the db
                @reset($_POST);
                foreach ($_POST as $key => $value) {
                    if(preg_match("/^right_/", $key)) {
                        
                        $right = new CoreCompanyRights;
                        $right->setModuleName(strtoupper(str_replace("right_", "", $key)))
                              ->setRightCode($value);                        
                        $company->addDefaultRight($right);
                    }
                }
                
                $this->context->entityManager->persist($company);
                $this->context->entityManager->flush();                
                $this->context->entityManager->commit();
            } catch (Exception $e) {
                // Show wizz to confirm the employee update
                $this->context->entityManager->rollback();
                IgestisErrors::createWizz($e, IgestisErrors::TYPE_ANY);
                $this->redirect(ConfigControllers::createUrl("companies_list"));
            }
            

            // Show wizz to confirm the company update
            new wizz(_("The company data have been successfully saved"), WIZZ::$WIZZ_SUCCESS);

            // Redirect to the company list
            $this->redirect(ConfigControllers::createUrl("companies_list"));
        }
        
        $this->context->render("pages/companiesNew.twig", array(
            'all_modules_rights' => $this->context->security->getDefaultCompanyRights()
        ));
    }
    
    /**
     * Show a customer in read only
     */
    public function showAction($Id) {
        $company = $this->context->entityManager->getRepository("CoreCompanies")->find($Id);
        $this->context->render("pages/companiesShow.twig", array(
            'show_data' => $company)
        );
    }
}
