<?php

/**
 * Account management
 *
 * @author Gilles Hemmerlé
 */
class MyAccountController extends IgestisController {

    public function switchCompany($companyId) {
        $user = $this->context->entityManager->getRepository("CoreUsers")->find($this->context->security->user->getId());
        $company = $this->context->entityManager->getRepository("CoreCompanies")->find($companyId);

        $switchDone = false;
        foreach ($user->getCompaniesMember() as $currentAvailableCompany) {
            // If the company requested by the user is in his available company, then switch him
            if ($currentAvailableCompany->getId() == $company->getId()) {
                $user->setCompany($company);
                $this->context->entityManager->persist($user);
                $this->context->entityManager->flush();
                $switchDone = true;
                break;
            }
        }
        if ($switchDone) {
            new \wizz("Company switch done", \wizz::$WIZZ_SUCCESS);
        } else {
            new \wizz("Unable to switch company", \wizz::$WIZZ_ERROR);
        }


        if($_SERVER['HTTP_REFERER']) {
            $this->redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->redirect(ConfigControllers::createUrl("home_page"));
        }
    }

    /**
     * Get a form to edit or validate it if the form is received
     */
    public function editAction() {

        if($this->request->IsPost()) {
            $user = $this->context->entityManager->getRepository("CoreUsers")->find($this->context->security->user->getId());
            $contact = $this->context->entityManager->getRepository("CoreContacts")->find($this->context->security->contact->getId());


            // Set the new datas of the person
            $parser = new IgestisFormParser();

            $user = $parser->FillEntityFromForm($user, $_POST);
            $contact = $parser->FillEntityFromForm($contact, $_POST);

            $user->AddOrEditContact($contact);



            try {
                $this->context->entityManager->persist($user);
                $this->context->entityManager->flush();
                new wizz(_("Data have been successfully saved"), WIZZ::$WIZZ_SUCCESS);
                $this->redirect(ConfigControllers::createUrl("my_account"));
            }
            catch (Exception $e) {
                IgestisErrors::createWizz($e);
                $this->redirect(ConfigControllers::createUrl("my_account"));
            }
        }



        $account = $this->context->security->contact;//$this->context->entityManager->getRepository("CoreUsers")->find($this->context->security->user->getId());
        $countries_list = $this->context->entityManager->getRepository("CoreCountries")->findAll();
        $languages_list = $this->context->entityManager->getRepository("CoreLanguages")->findAll();
        $civilities_list = $this->context->entityManager->getRepository("CoreCivilities")->findAll();


        $this->context->render("pages/myAccount.twig", array(
            'form_data' => $account,
            'countries_list' => $countries_list,
            'languages_list' => $languages_list,
            'civilities_list' => $civilities_list)
        );
    }

}
