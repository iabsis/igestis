<?php

/**
 * Suppliers management
 *
 * @author Gilles Hemmerlé
 */
class SuppliersController extends IgestisController {
    /**
     * Show the list of suppliers
     */
    public function indexAction() {
        
        if($this->context->security->contact->getLogin() == CORE_ADMIN) {
            $this->context->throw403error();
        }
        
        
        $suppliers = $this->context->entityManager->getRepository("CoreContacts")->getSuppliersList();     
        $this->context->render("pages/suppliersList.twig", array(
            'table_data' => $suppliers)
        );
    }
    
    /**
     * Get a form to edit or validate it if the form is received
     */
    public function editAction($Id) {
        if($this->context->security->contact->getLogin() == CORE_ADMIN) {
            $this->context->throw403error();
        }
        
        
        $supplier = $this->context->entityManager->getRepository("CoreUsers")->find($Id);
        if(!$supplier || $supplier->getUserType() != "supplier" || $supplier->getCompany() != $this->context->security->user->getCompany()) {
            new wizz(_("The supplier has not been found"), wizz::$WIZZ_ERROR);
            $this->redirect(ConfigControllers::createUrl("suppliers_list"));
        }
        if($this->request->IsPost()) {
            // Le formulaire a été envoyé
            if($supplier)
            // Set the new datas of the supplier
            $parser = new IgestisFormParser();
            $supplier->setTvaInvoice(false);
            $supplier = $parser->FillEntityFromForm($supplier, $_POST);
            $supplier->setCompany(null);
            
            // Save the supplier into the database  
            try {
                $this->context->entityManager->persist($supplier);
                $this->context->entityManager->flush();                
                
            } catch (Exception $e) {
                // Show wizz to confirm the employee update
                new wizz($e->getMessage(), WIZZ_ERROR);
                $this->redirect(ConfigControllers::createUrl("suppliers_list"));
            }
            
            // Show wizz to confirm the employee update
            new wizz(_("The supplier data have been successfully saved"), WIZZ::$WIZZ_SUCCESS);

            // Redirect to the employee list
            $this->redirect(ConfigControllers::createUrl("suppliers_list"));
            
        }
        

        $languages_list =$this->context->entityManager->getRepository("CoreLanguages")->findAll();
        $countries_list = $this->context->entityManager->getRepository("CoreCountries")->findAll();
        //$civilities_list = $this->context->entityManager->getRepository("CoreCivilities")->findAll();
        $this->context->render("pages/suppliersEdit.twig", array(
            'form_data' => $supplier,
            'languages_list' => $languages_list,
            //'civilities_list' => $civilities_list,
            'countries_list' => $countries_list)
        );
    }
    
    /**
     * Delete the supplier
     */
    public function deleteAction($Id) {
        
        if($this->context->security->contact->getLogin() == CORE_ADMIN) {
            $this->context->throw403error();
        }
        
        $supplier = $this->context->entityManager->getRepository("CoreUsers")->find($Id);
        if(!$supplier || $supplier->getUserType() != "supplier" || $supplier->getCompany() != $this->context->security->user->getCompany()) {
            new wizz(_("The supplier has not been found"), wizz::$WIZZ_ERROR);
            $this->redirect(ConfigControllers::createUrl("suppliers_list"));
        }
        
        $this->context->entityManager->beginTransaction();
        // Delete the supplier from the database
        try {
            $supplier->disable();
            $this->context->entityManager->persist($supplier);
            //$this->context->entityManager->remove($user);
            $this->context->entityManager->flush();
            $this->context->entityManager->commit();
        } catch (Exception $e) {
            // Show wizz to alert user that the supplier deletion has not realy been deleted
            IgestisErrors::createWizz($e);
            $this->redirect(ConfigControllers::createUrl("suppliers_list"));
        }

        // Show wizz to confirm the supplier update
        new wizz(_("The supplier has been successfully deleted"), WIZZ::$WIZZ_SUCCESS);

        // Redirect to the supplier list
        $this->redirect(ConfigControllers::createUrl("suppliers_list"));
    }
    
    /**
     * Add the supplier
     */
    public function newAction() {
        
        if($this->context->security->contact->getLogin() == CORE_ADMIN) {
            $this->context->throw403error();
        }
        
        $supplier = CoreUsers::newSupplier();
        if($this->request->IsPost()) {
            // Le formulaire a été envoyé
            
            // Set the new datas of the supplier
            $parser = new IgestisFormParser();
            $supplier->setTvaInvoice(false);
            $supplier = $parser->FillEntityFromForm($supplier, $_POST);
            $supplier->setCompany(null);

            // Create a new contact
            $contact = new CoreContacts();
            $contact = $parser->FillEntityFromForm($contact, $_POST);
            // The first one is neccessary the main one
            $contact->setMainContact(true);
            // Associate the contact to the user
            $supplier->AddOrEditContact($contact);

            // Save the supplier into the database
            try {
                $this->context->entityManager->persist($supplier);
                $this->context->entityManager->flush();

            } catch (Exception $e) {
                // Show wizz to confirm the supplier update
                IgestisErrors::createWizz($e, IgestisErrors::TYPE_ANY);
                $this->redirect(ConfigControllers::createUrl("suppliers_list"));
            }

            // Show wizz to confirm the supplier update
            new wizz(_("The supplier data have been successfully saved"), WIZZ::$WIZZ_SUCCESS);

            // Redirect to the supplier list
            $this->redirect(ConfigControllers::createUrl("suppliers_list"));

        }

        //$client_types = $this->context->entityManager->getRepository("CoreClientType")->findAll();
        $languages_list = $this->context->entityManager->getRepository("CoreLanguages")->findAll();
        $countries_list = $this->context->entityManager->getRepository("CoreCountries")->findAll();
        $civilities_list = $this->context->entityManager->getRepository("CoreCivilities")->findAll();
        $this->context->render("pages/suppliersNew.twig", array(
                'form_data' => $supplier,
                //'client_type' => $client_types,
                'languages_list' => $languages_list,
                'civilities_list' => $civilities_list,
                'countries_list' => $countries_list)
        );
    }
    
    /**
     * Show a supplier in read only
     */
    public function showAction($Id) {
        
        if($this->context->security->contact->getLogin() == CORE_ADMIN) {
            $this->context->throw403error();
        }
        
        $supplier = $this->context->entityManager->getRepository("CoreUsers")->find($Id);
        
        if(!$supplier || $supplier->getUserType() != "supplier" || $supplier->getCompany() != $this->context->security->user->getCompany()) {
            new wizz(_("The supplier has not been found"), wizz::$WIZZ_ERROR);
            $this->redirect(ConfigControllers::createUrl("suppliers_list"));
        }
        
        //$client_types = $this->context->entityManager->getRepository("CoreClientType")->findAll();
        $languages_list = $this->context->entityManager->getRepository("CoreLanguages")->findAll();
        $countries_list = $this->context->entityManager->getRepository("CoreCountries")->findAll();
        $civilities_list = $this->context->entityManager->getRepository("CoreCivilities")->findAll();
        
        $this->context->render("pages/suppliersShow.twig", array(
            'show_data' => $supplier,
            //'client_type' => $client_types,
            'languages_list' => $languages_list,
            'civilities_list' => $civilities_list,
            'countries_list' => $countries_list)
        );
    }

}
