<?php

/**
 * Customers management
 *
 * @author Gilles Hemmerlé
 */
class CustomersController extends IgestisController {
    /**
     * Show the list of customers
     */
    public function indexAction() {
        
        if($this->context->security->contact->getLogin() == \ConfigIgestisGlobalVars::igestisCoreAdmin()) {
            $this->context->throw403error();
        }
        
        $customers = $this->context->entityManager->getRepository("CoreContacts")->getCustomersList();     
        $this->context->render("pages/customersList.twig", array(
            'table_data' => $customers)
        );
    }
    
    /**
     * Get a form to edit or validate it if the form is received
     */
    public function editAction($Id) {
        if($this->context->security->contact->getLogin() == \ConfigIgestisGlobalVars::igestisCoreAdmin()) {
            $this->context->throw403error();
        }
        
        $customer = $this->context->entityManager->getRepository("CoreUsers")->find($Id);
        if(!$customer || $customer->getUserType() != "client" || $customer->getCompany() != $this->context->security->user->getCompany()) {
            new wizz(_("The customer has not been found"), wizz::$WIZZ_ERROR);
            $this->redirect(ConfigControllers::createUrl("customers_list"));
        }
        if($this->request->IsPost()) {
            // Le formulaire a été envoyé
            // Set the new datas of the employee
            
            $customer->setTvaInvoice(false);
            $parser = new IgestisFormParser();
            $customer = $parser->FillEntityFromForm($customer, $_POST);
            $customer->setCompany(null);
            
            // Save the employee into the database  
            try {
                $this->context->entityManager->persist($customer);
                $this->context->entityManager->flush();                
                
            } catch (Exception $e) {
                // Show wizz to confirm the employee update
                new wizz($e->getMessage(), WIZZ_ERROR);
                $this->redirect(ConfigControllers::createUrl("customers_list"));
            }
            
            // Show wizz to confirm the employee update
            new wizz(_("The customer data have been successfully saved"), WIZZ::$WIZZ_SUCCESS);

            // Redirect to the employee list
            $this->redirect(ConfigControllers::createUrl("customers_list"));
            
        }
        
        
        
        $client_types = $this->context->entityManager->getRepository("CoreClientType")->findAll();
        $languages_list = array(); //$this->context->entityManager->getRepository("CoreLanguages")->findAll();
        $countries_list = $this->context->entityManager->getRepository("CoreCountries")->findAll();
        $civilities_list = $this->context->entityManager->getRepository("CoreCivilities")->findAll();
        $this->context->render("pages/customersEdit.twig", array(
            'form_data' => $customer,
            'client_type' => $client_types,
            'languages_list' => $languages_list,
            'civilities_list' => $civilities_list,
            'countries_list' => $countries_list)
        );
    }
    
    /**
     * Delete the customer
     */
    public function deleteAction($Id) {
        
        if($this->context->security->contact->getLogin() == \ConfigIgestisGlobalVars::igestisCoreAdmin()) {
            $this->context->throw403error();
        }
        
        $user = $this->context->entityManager->getRepository("CoreUsers")->find($Id);
        if(!$user || $user->getUserType() != "client" || $user->getCompany() != $this->context->security->user->getCompany()) {
            new wizz(_("The customer has not been found"), wizz::$WIZZ_ERROR);
            $this->redirect(ConfigControllers::createUrl("customers_list"));
        }
        
        $this->context->entityManager->beginTransaction();
        // Delete the customer from the database
        try {
            $user->disable();
            $this->context->entityManager->persist($user);
            //$this->context->entityManager->remove($user);
            $this->context->entityManager->flush();
            $this->context->entityManager->commit();
        } catch (Exception $e) {
            // Show wizz to alert user that the company deletion has not realy been deleted
            IgestisErrors::createWizz($e);
            $this->redirect(ConfigControllers::createUrl("customers_list"));
        }

        // Show wizz to confirm the employee update
        new wizz(_("The customer has been successfully deleted"), WIZZ::$WIZZ_SUCCESS);

        // Redirect to the employee list
        $this->redirect(ConfigControllers::createUrl("customers_list"));
    }
    
    /**
     * Add the customer
     */
    public function newAction() {
        
        if($this->context->security->contact->getLogin() == \ConfigIgestisGlobalVars::igestisCoreAdmin()) {
            $this->context->throw403error();
        }
        
        $customer = CoreUsers::newCustomer();
        
        if($this->request->IsPost()) {
            // Le formulaire a été envoyé
            
            // Set the new datas of the customer
            $parser = new IgestisFormParser();
            $customer->setTvaInvoice(false);
            $customer = $parser->FillEntityFromForm($customer, $_POST);
            $customer->setCompany(null);
            // Create a new contact
            $contact = new CoreContacts();
            $contact = $parser->FillEntityFromForm($contact, $_POST);
            // The first one is neccessary the main one
            $contact->setMainContact(true);
            // Associate the contact to the user
            $customer->AddOrEditContact($contact);

            
            // Save the employee into the database
            try {
                $this->context->entityManager->persist($customer);
                $this->context->entityManager->flush();

            } catch (Exception $e) {
                // Show wizz to confirm the employee update
                IgestisErrors::createWizz($e, IgestisErrors::TYPE_ANY);
                $this->redirect(ConfigControllers::createUrl("customers_list"));
            }

            // Show wizz to confirm the employee update
            new wizz(_("The customer data have been successfully saved"), WIZZ::$WIZZ_SUCCESS);

            // Redirect to the employee list
            $this->redirect(ConfigControllers::createUrl("customers_list"));

        }

        $client_types = $this->context->entityManager->getRepository("CoreClientType")->findAll();
        $languages_list = $this->context->entityManager->getRepository("CoreLanguages")->findAll();
        $countries_list = $this->context->entityManager->getRepository("CoreCountries")->findAll();
        $civilities_list = $this->context->entityManager->getRepository("CoreCivilities")->findAll();
        $this->context->render("pages/customersNew.twig", array(
                'form_data' => $customer,
                'client_type' => $client_types,
                'languages_list' => $languages_list,
                'civilities_list' => $civilities_list,
                'countries_list' => $countries_list)
        );
    }
    
    /**
     * Show a customer in read only
     */
    public function showAction($Id) {
        
        if($this->context->security->contact->getLogin() == \ConfigIgestisGlobalVars::igestisCoreAdmin()) {
            $this->context->throw403error();
        }
        
        $customer = $this->context->entityManager->getRepository("CoreUsers")->find($Id);
        
        if(!$customer || $customer->getUserType() != "client" || $customer->getCompany() != $this->context->security->user->getCompany()) {
            new wizz(_("The customer has not been found"), wizz::$WIZZ_ERROR);
            $this->redirect(ConfigControllers::createUrl("customers_list"));
        }
        
        $client_types = $this->context->entityManager->getRepository("CoreClientType")->findAll();
        $languages_list = array(); //$this->context->entityManager->getRepository("CoreLanguages")->findAll();
        $countries_list = $this->context->entityManager->getRepository("CoreCountries")->findAll();
        $civilities_list = $this->context->entityManager->getRepository("CoreCivilities")->findAll();
        // \Doctrine\Common\Util\Debug::dump($customer, 3);
        $this->context->render("pages/customersShow.twig", array(
            'show_data' => $customer,
            'client_type' => $client_types,
            'languages_list' => $languages_list,
            'civilities_list' => $civilities_list,
            'countries_list' => $countries_list)
        );
    }
    
    /**
     * Get the import form and put the csv file into the temporary database before to show it to the user.
     * @throws Exception
     */
    public function showImportResult() {
        
        if($this->context->security->contact->getLogin() == \ConfigIgestisGlobalVars::igestisCoreAdmin()) {
            $this->context->throw403error();
        }
        
        
        // Check if the form is correctly filled and transfered or show a wizz
        /*
        try {
            if (!$this->request->IsPost()) {
                throw new Exception(_("Thanks to select a file before to start the import process"));
            }

            if (!is_file($_FILES['csvFile']['tmp_name'])) {
                throw new Exception(_("You have not transfered a csv file."));
            }
        } catch (Exception $e) {
            \IgestisErrors::createWizz($e, IgestisErrors::TYPE_ANY);
            $this->redirect(IgestisConfigController::createUrl("customers_list"));
        }*/

        // Start to parse the csv file and import datas to the database. Show a wizz if any errors
        try {
            if ($this->request->IsPost()) {
                $importFile = new \Igestis\Core\ImportCsvCustomers("customerImport", $_FILES['csvFile']['tmp_name'], $_POST['delimiter'], $_POST['enclosure']);

                // Configure the csv authorized columns
                $importFile->addColumn("civilityCode", "CoreContacts");
                $importFile->addColumn("login", "CoreContacts", true);
                $importFile->addColumn("firstname", "CoreContacts", true);
                $importFile->addColumn("lastname", "CoreContacts", true);
                $importFile->addColumn("email", "CoreContacts");
                $importFile->addColumn("languageCode", "CoreContacts");
                $importFile->addColumn("address1", "CoreContacts");
                $importFile->addColumn("address2", "CoreContacts");
                $importFile->addColumn("postalCode", "CoreContacts");
                $importFile->addColumn("city", "CoreContacts");
                $importFile->addColumn("countryCode", "CoreContacts");
                $importFile->addColumn("phone1", "CoreContacts");
                $importFile->addColumn("phone2", "CoreContacts");
                $importFile->addColumn("mobile", "CoreContacts");
                $importFile->addColumn("fax", "CoreContacts");
                $importFile->addColumn("companyId", "CoreUsers");
                $importFile->addColumn("clientTypeCode", "CoreUsers", true);
                $importFile->addColumn("userLabel", "CoreUsers");
                $importFile->addColumn("userType", "CoreUsers");
                $importFile->addColumn("tvaNumber", "CoreUsers");
                $importFile->addColumn("tvaInvoice", "CoreUsers");
                $importFile->addColumn("rib", "CoreUsers");
                $importFile->addColumn("accountCode", "CoreUsers");
                // Launch import
                $importFile->startParsing();
            }            
        } catch (Exception $e) {
            \IgestisErrors::createWizz($e, IgestisErrors::TYPE_ANY, $e->getMessage());
        }
        
        $aImportedDatas = \Igestis\Core\ImportCsvCustomers::datasToArray("customerImport");
        if(!is_array($aImportedDatas) || count($aImportedDatas) < 1) {
            new wizz(_("The file contains no usable datas"), wizz::$WIZZ_ERROR);
            $this->redirect(IgestisConfigController::createUrl("customers_list"));
        }
        
        // Render the page
        $this->context->render("pages/customerImportResult.twig", array(
                'table_data' => $aImportedDatas
            )
        );
    }
    
    // ------------------------------------------------------

    public function validImport() {
        
        if($this->context->security->contact->getLogin() == \ConfigIgestisGlobalVars::igestisCoreAdmin()) {
            $this->context->throw403error();
        }
        
        // Render the page
        
        try {
            // Format the datas received from the datatable
            $datas = array();
            if(!$this->request->IsPost() || !isset($_POST["form_data"])) {
                throw new Exception(_("Thanks to select one or more entries from the table"));
            }
            $list = explode("&", $_POST["form_data"]);
            foreach ($list as $value) {
                list($varName, $varValue) = explode("=", $value);
                if (isset($datas[$varName])) {
                    if (is_array($datas[$varName])) {
                        $datas[$varName][] = $varValue;
                    }                 
                    else {
                        $datas[$varName] = array($datas[$varName]);
                        $datas[$varName][] = $varValue;
                    }
                }
                else {
                    $datas[$varName] = $varValue;
                }
            }
            
            \Igestis\Core\ImportCsvCustomers::valideDatas("customerImport", $datas['import']);
            
        } catch (Exception $e) {
            \IgestisErrors::createWizz($e, IgestisErrors::TYPE_ANY);
            $this->redirect(IgestisConfigController::createUrl("customer_import_step2"));
        }

        new wizz(\Igestis\Core\ImportCsvCustomers::report(), wizz::$WIZZ_SUCCESS);
        $this->redirect(IgestisConfigController::createUrl("customers_list"));

        exit;
    }

}
