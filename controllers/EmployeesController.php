<?php

use Igestis\Utils\Dump;

/**
 * Employees management
 *
 * @author Gilles Hemmerlé
 */
class EmployeesController extends IgestisController {
    /**
     * Show the list of customers
     */
    public function indexAction() {
        $employees = $this->context->entityManager->getRepository("CoreContacts")->getEmployeesList();     
        $this->context->render("pages/employeesList.twig", array(
            'table_data' => $employees)
        );
    }
    
    /**
     * Get a form to edit or validate it if the form is received
     */
    public function editAction($Id) {
        // If the form has been received, manage the form...
        $employee = $this->context->entityManager->getRepository("CoreUsers")->find($Id);    
        
        if(!$employee || $employee->getUserType() != "employee" || ($this->context->security->contact->getLogin() != \ConfigIgestisGlobalVars::igestisCoreAdmin() && $employee->getCompany() != $this->context->security->user->getCompany())) {
            new wizz(_("The employee has not been found"), wizz::$WIZZ_ERROR);
            $this->redirect(ConfigControllers::createUrl("employees_list"));
        }
        
        if($this->request->IsPost()) {            
            // Check the form validity
            $this->context->entityManager->beginTransaction();
            $aDepartments = $this->request->getPost("departments");
            if(is_array($aDepartments))  {
                $employee->removeDepartments();
                foreach ($aDepartments as $department) {
                    $employee->addDepartment($this->context->entityManager->getRepository("CoreDepartments")->find($department));
                }
            }            

            foreach($employee->getRightsList() as $right) {
                $employee->removeRight($right);
                $this->context->entityManager->remove($right);
            }
            
            

            // Set the new datas of the employee
            $parser = new IgestisFormParser();
            $employee = $parser->FillEntityFromForm($employee, $_POST);
            $company = $this->context->entityManager->getRepository("CoreCompanies")->find($_POST['company']);
            $employee->setCompany($company);
            $contact = $employee->getContacts()->get(0);
            $contact = $parser->FillEntityFromForm($contact, $_POST);

            $employee->setUserLabel($contact->getFirstName() . " " . $contact->getLastName());
            $employee->updateContact($contact);
            
            

            // Save the employee into the database   
            try {
                $this->context->entityManager->persist($employee);
                $this->context->entityManager->flush();
            } catch(Exception $e) {
                IgestisErrors::createWizz($e, IgestisErrors::TYPE_ANY);
                $this->redirect(ConfigControllers::createUrl("employees_list"));
            }
            
            
            try {
                @reset($_POST);
                foreach ($_POST as $key => $value) {
                    if(preg_match("/^right_/", $key)) {
                        $right = new CoreUsersRights($Id, strtoupper(str_replace("right_", "", $key)), $value);
                        $employee->addRightsList($right);
                        $this->context->entityManager->persist($right);
                    }
                }
                $this->context->entityManager->persist($employee);
                $this->context->entityManager->flush();
                $this->context->entityManager->commit();
                
                \Igestis\Utils\Hook::callHook("contactUpdated", new \Igestis\Types\HookParameters(array(
                    "contact" => $contact
                )));
                
            } catch (Exception $e) {
                // Show wizz to confirm the employee update
                new wizz($e->getMessage(), WIZZ_ERROR);
                $this->redirect(ConfigControllers::createUrl("employees_list"));
            }
            
            // Show wizz to confirm the employee update
            new wizz(_("The employee data have been successfully saved"), WIZZ::$WIZZ_SUCCESS);

            // Redirect to the employee list
            $this->redirect(ConfigControllers::createUrl("employees_list"));
        }
        

        
        $countries_list = $this->context->entityManager->getRepository("CoreCountries")->findAll();
        $civilities_list = $this->context->entityManager->getRepository("CoreCivilities")->findAll();
        $languages_list = $this->context->entityManager->getRepository("CoreLanguages")->findAll();
        $companies_list = $this->context->entityManager->getRepository("CoreCompanies")->findAll();
        
        $this->context->render("pages/employeesEdit.twig", array(
            'form_data' => $employee,
            'countries_list' => $countries_list,
            'civilities_list' => $civilities_list,
            'companies_list' => $companies_list,
            'languages_list' => $languages_list,
            'departments_list'=>  $this->context->entityManager->getRepository("CoreDepartments")->getDepartmentsList(false),
            'all_modules_rights' => $this->context->security->getAllModulesRights($Id))
        );
    }
    
    /**
     * Delete the employee
     */
    public function deleteAction($Id) {
        $user = $this->context->entityManager->getRepository("CoreUsers")->find($Id);
        if(!$user || $user->getUserType() != "employee" || ($this->context->security->contact->getLogin() != \ConfigIgestisGlobalVars::igestisCoreAdmin() && $user->getCompany() != $this->context->security->user->getCompany())) {
            new wizz(_("The employee has not been found"), wizz::$WIZZ_ERROR);
            $this->redirect(ConfigControllers::createUrl("employees_list"));
        }
        
        $this->context->entityManager->beginTransaction();
        
        // Delete the employee from the database
        try {
            $user->disable();
            $this->context->entityManager->persist($user);
            \Igestis\Utils\Debug::addDump($user, "user");
            $this->context->entityManager->flush();
            $this->context->entityManager->commit();
            
            \Igestis\Utils\Hook::callHook("contactUpdated", new \Igestis\Types\HookParameters(array(
                "contact" => $user->getMainContact()
            )));
            
        } catch (Exception $e) {
            // Show wizz to alert user that the company deletion has not realy been deleted
            IgestisErrors::createWizz($e, IgestisErrors::TYPE_ANY);
            $this->redirect(ConfigControllers::createUrl("employees_list"));
        }

        // Show wizz to confirm the employee update
        new wizz(_("The employee has been successfully deleted"), WIZZ::$WIZZ_SUCCESS);

        // Redirect to the employee list
        $this->redirect(ConfigControllers::createUrl("employees_list"));
    }
    
    /**
     * Add the customer
     */
    public function newAction() {
        
        if($this->request->IsPost()) {
            $this->context->entityManager->beginTransaction();
            $parser = new IgestisFormParser();
            // Création d'un nouveau user
            $employee = CoreUsers::newEmployee();
            $employee = $parser->FillEntityFromForm($employee, $_POST);
            // Recherche de la compatny et assignation
            $company = $this->context->entityManager->getRepository("CoreCompanies")->find($_POST['company']);
            $employee->setCompany($company);
            // Création du contact principal et assignation des valeurs
            $contact = new CoreContacts();
            $contact->setMainContact(true);
            $contact = $parser->FillEntityFromForm($contact, $_POST);
            $employee->setUserLabel($contact->getFirstName() . " " . $contact->getLastName());
            //$employee->setContact($contact);
            $employee->AddOrEditContact($contact);   
            
            $aDepartments = $this->request->getPost("departments");
            if(is_array($aDepartments))  {
                $employee->removeDepartments();
                foreach ($aDepartments as $department) {
                    $employee->addDepartment($this->context->entityManager->getRepository("CoreDepartments")->find($department));
                }
            }            

            
            // Save the employee into the database   
            try {
                $this->context->entityManager->persist($employee);
                $this->context->entityManager->flush();
            } catch(Exception $e) {
                IgestisErrors::createWizz($e, IgestisErrors::TYPE_ANY);
                $this->redirect(ConfigControllers::createUrl("employees_list"));
            }

                
            // Save the employee into the database   
            try {
                @reset($_POST);
                foreach ($_POST as $key => $value) {
                    if(preg_match("/^right_/", $key)) {
                        $right = new CoreUsersRights($employee->getId(), strtoupper(str_replace("right_", "", $key)), $value);
                        $employee->addRightsList($right);
                        $this->context->entityManager->persist($right);
                    }
                }
                $this->context->entityManager->persist($employee);
                $this->context->entityManager->flush();
                
                \Igestis\Utils\Hook::callHook("contactUpdated", new \Igestis\Types\HookParameters(array(
                    "contact" => $employee->getMainContact()
                )));
            
                
                $this->context->entityManager->commit();
                
            } catch (Exception $e) {
                // Show wizz to confirm the employee update
                IgestisErrors::createWizz($e, IgestisErrors::TYPE_ANY);
                $this->redirect(ConfigControllers::createUrl("employees_list"));
            }
            
            // Show wizz to confirm the employee update
            new wizz(_("The employee data have been successfully saved"), WIZZ::$WIZZ_SUCCESS);

            // Redirect to the employee list
            $this->redirect(ConfigControllers::createUrl("employees_list"));   
            
         }// End of post treatment...
         
         
        $countries_list = $this->context->entityManager->getRepository("CoreCountries")->findAll();
        $civilities_list = $this->context->entityManager->getRepository("CoreCivilities")->findAll();
        $companies_list = $this->context->entityManager->getRepository("CoreCompanies")->findAll();
        $languages_list = $this->context->entityManager->getRepository("CoreLanguages")->findAll();
        $this->context->render("pages/employeesNew.twig", array(
            'countries_list' => $countries_list,
            'civilities_list' => $civilities_list,
            'companies_list' => $companies_list,
            'languages_list' => $languages_list,
            'departments_list'=>  $this->context->entityManager->getRepository("CoreDepartments")->getDepartmentsList(false),
            'all_modules_rights' => $this->context->security->getAllModulesRights())
        );
    }
    
    /**
     * Show a customer in read only
     */
    public function showAction($Id) {
        $employee = $this->context->entityManager->getRepository("CoreUsers")->find($Id);
        if(!$employee || $employee->getUserType() != "employee" ||  ($this->context->security->contact->getLogin() != \ConfigIgestisGlobalVars::igestisCoreAdmin() && $employee->getCompany() != $this->context->security->user->getCompany())) {
            new wizz(_("The Employee has not been found"), wizz::$WIZZ_ERROR);
            $this->redirect(ConfigControllers::createUrl("employees_list"));
        }
        
        $this->context->render("pages/employeesShow.twig", array(
            'show_data' => $employee)
        );
    }
}
