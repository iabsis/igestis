<?php


/**
 * Description of CustomersAjaxController
 *
 * @author Gilles Hemmerlé (Iabsis) <giloux@gmail.com>
 */
class CustomersAjaxController extends IgestisAjaxController {

    /**
     * Get the form and apply updates to the concerned contact
     * @param $UserId User id we are editing
     * @param null $contactId Contact Id (only if edit, let null if new)
     */
    public function editContact($UserId, $contactId=null) {
        if($this->request->IsPost()) {
            $customer = $this->context->entityManager->getRepository("CoreUsers")->find($UserId);
            if($contactId > 0) {
                $contact = $this->context->entityManager->getRepository("CoreContacts")->find($contactId);
                if(isset($_POST['mainContact'])) {
                    $contact->setMainContact(true);
                }
                else {
                    $contact->setMainContact(false);
                }
            }
            else {
                $contact = new CoreContacts();
            }

            // Set the new datas to the company
            $parser = new IgestisFormParser();
            $contact = $parser->FillEntityFromForm($contact, $_POST);
            $customer->AddOrEditContact($contact);


            // Apply changes and return a return the table of contacts
            try {
                $this->context->entityManager->persist($customer);
                $this->context->entityManager->flush();

                $this->context->entityManager->refresh($customer);

                $customer = $this->context->entityManager->getRepository("CoreUsers")->find($UserId);
                $html = $this->context->render("ajax/tableContactsList.twig", array(
                        'form_data' => $customer
                    ),true
                );

                $this->AjaxRender(array("successful" => $html));
            }
            catch (Exception $e) {
                $this->AjaxRender(array("error" => $e->getMessage()));
            }
        }
        else {
            $this->AjaxRender(array("error" => _("No post datas")));
        }
    }

    /**
     * Renvoie les données serialisées du contact demandé
     * @param $contactId Id du contact à récupérer
     */
    public function getContactDatas($contactId) {
        $contact = $this->context->entityManager->getRepository("CoreContacts")->findById($contactId);
        $client_types = $this->context->entityManager->getRepository("CoreClientType")->findAll();
        $languages_list = $this->context->entityManager->getRepository("CoreLanguages")->findAll();
        $countries_list = $this->context->entityManager->getRepository("CoreCountries")->findAll();
        $civilities_list = $this->context->entityManager->getRepository("CoreCivilities")->findAll();

        $html = $this->context->render("ajax/modalContactsEdit.twig", array(
            "form_data" => $contact,
            'client_type' => $client_types,
            'languages_list' => $languages_list,
            'civilities_list' => $civilities_list,
            'countries_list' => $countries_list
        ), true);

        $this->ajaxRender(array("successful" => $html));


    }

    /**
     * Supprime un contact si il y en a d'autres encore associé au client, ne fait rien sinon
     * @param $contactId Id du contact à supprimer
     */
    public function deleteContact($contactId) {
        $this->context->entityManager->beginTransaction();
        $contact = $this->context->entityManager->getRepository("CoreContacts")->findById($contactId);

        $customer = $contact->getUser();
        $customer->removeContact($contact);


        try {
            $this->context->entityManager->persist($customer);
            $this->context->entityManager->flush();
            $this->context->entityManager->commit();
            $html = $this->context->render("ajax/tableContactsList.twig", array(
                    'form_data' => $customer
                ),true
            );

            $this->AjaxRender(array("successful" => $html));
        }
        catch (Exception $e) {
            //$this->context->entityManager->rollback();
            $this->AjaxRender(array("error" => $e->getMessage()));
        }
    }


    public function startCustomerImportAction() {
        $html = $this->context->render("ajax/tableContactsList.twig", array(),true);

        $this->AjaxRender(array("successful" => $html));
    }
}
