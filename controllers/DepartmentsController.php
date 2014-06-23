<?php

/**
 * Departments management
 *
 * @author Gilles Hemmerlé
 */
class DepartmentsController extends IgestisController {

    /**
     * Show the list of departments
     */
    public function indexAction() {
        $departments = $this->context->entityManager->getRepository("CoreDepartments")->getDepartmentsList(); 
        $this->context->render("pages/departmentsList.twig", array(
            'table_data' => $departments)
        );
    }

    /**
     * Get a form to edit or validate it if the form is received
     */
    public function editAction($Id) {

        // If the form has been received, manage the form...
        if ($this->request->IsPost()) {
            // Check the form validity
            if (!$this->request->getPost("label")) $this->context->invalid_form(_("The department name is a required field"));

            try {
                // Start transaction
                $this->context->entityManager->beginTransaction();
                // Get the original department
                $department = $this->context->entityManager->getRepository("CoreDepartments")->find($Id);
                // Set the new datas to the department
                $parser = new IgestisFormParser();
                $department = $parser->FillEntityFromForm($department, $_POST);

                // First remove old rights
                $department->removeAllRights();
                $this->context->entityManager->persist($department);
                $this->context->entityManager->flush();


                // Adding new rights
                foreach ($_POST as $key => $value) {
                    if (preg_match("/^right_/", $key)) {
                        $right = new CoreDepartmentsRights($Id, strtoupper(str_replace("right_", "", $key)), $value);

                        $department->addRightsList($right);
                    }
                }
                
                // Save the department into the database
                $this->context->entityManager->persist($department);
                $this->context->entityManager->flush();
                $this->context->entityManager->commit();
            } catch (\Exception $e) {
                \IgestisErrors::createWizz($e, \IgestisErrors::TYPE_ANY);
                $this->redirect(ConfigControllers::createUrl("departments_list"));
            }

            // Show wizz to confirm the department update
            new wizz(_("The department data has been successfully saved"), WIZZ::$WIZZ_SUCCESS);

            // Redirect to the department list
            $this->redirect(ConfigControllers::createUrl("departments_list"));
        }

        // If no form received, show the form
        $department = $this->context->entityManager->getRepository("CoreDepartments")->find($Id);
        $this->context->render("pages/departmentsEdit.twig", array(
            'form_data' => $department,
            'all_modules_rights' => $this->context->security->getAllModulesGroupRights($Id)
                )
        );
    }

    /**
     * Delete the department
     */
    public function deleteAction($Id) {
        $department = $this->context->entityManager->getRepository("CoreDepartments")->find($Id);
        // Delete the department from the database
        try {
            $this->context->entityManager->remove($department);
            $this->context->entityManager->flush();
        } catch (Exception $e) {
            // Show wizz to alert user that the department deletion has not realy been deleted
            IgestisErrors::createWizz($e);
            $this->redirect(ConfigControllers::createUrl("departments_list"));
        }

        // Show wizz to confirm the employee update
        new wizz(_("The department has been successfully deleted"), WIZZ::$WIZZ_SUCCESS);

        // Redirect to the employee list
        $this->redirect(ConfigControllers::createUrl("departments_list"));
    }

    /**
     * Add a department
     */
    public function newAction() {
        if ($this->request->IsPost()) {
            // Check the form validity
            if (!$this->request->getPost("label"))
                $this->context->invalid_form(_("The department name is a required field"));

            // Get the original department
            $department = new CoreDepartments();

            // Set the new datas to the department
            $parser = new IgestisFormParser();
            $department = $parser->FillEntityFromForm($department, $_POST);

            // Save the department into the database
            $this->context->entityManager->persist($department);
            $this->context->entityManager->flush();

            // Show wizz to confirm the department update
            new wizz(_("The department data has been successfully saved"), WIZZ::$WIZZ_SUCCESS);

            // Redirect to the department list
            $this->redirect(ConfigControllers::createUrl("departments_list"));
        }

        $this->context->render("pages/departmentsNew.twig", array());
    }

    /**
     * Show a customer in read only
     */
    public function showAction($Id) {
        $department = $this->context->entityManager->getRepository("CoreDepartments")->find($Id);
        $this->context->render("pages/departmentsShow.twig", array(
            'show_data' => $department)
        );
    }

}
