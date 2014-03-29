<?php

/**
 * Objet permettant de représenter la configuration des routes
 *
 * @author Gilles Hemmerlé
 */
class ConfigControllers extends IgestisConfigController {
    //put your code here    

    public static function get() {
        if(count(self::$routes)) return self::$routes;
        
        $routes = array(
            
            array(
                "id" => "igestis_update_db",
                "Parameters" => array(
                    "Page" => "install-check",
                    "Action" => "updatedb"
                ),
                "Controller" => "InstallController",
                "Action" => "updateDbAction",
                "Access" => array("EVERYONE")
            ),
            
            array(
                "id" => "igestis_install",
                "Parameters" => array(
                    "Page" => "install-check"
                ),
                "Controller" => "InstallController",
                "Action" => "checkAction",
                "Access" => array("EVERYONE")
            ),
            
            
            /************* Script ajax **********************************/
            array(
                "id" => "ajax_is_login_exist",
                "Parameters" => array(
                    "Page" => "core_ajax",
                    "Action" => "login_exists",
                    "Login" => "{VAR}[\W\w]+"
                ),
                "Controller" => "CoreAjaxController",
                "Action" => "LoginExists",
                "Access" => array("CORE:ADMIN", "CORE:TECH")
            ),
            
            array(
                "id" => "ajax_progress_file_upload",
                "Parameters" => array(
                    "Page" => "core_ajax",
                    "Action" => "upload_progress"
                ),
                "Controller" => "CoreAjaxController",
                "Action" => "uploadProgress",
                "Access" => array("EVERYONE")
            ),
            
            array(
                "id" => "ajax_hide_quicktour",
                "Parameters" => array(
                    "Page" => "core_ajax",
                    "Action" => "hide_quicktour",
                ),
                "Controller" => "CoreAjaxController",
                "Action" => "hideQuicktour",
                "Access" => array("AUTHENTICATED")
            ),
            
            array(
                "id" => "ajax_edit_contact",
                "Parameters" => array(
                    "Page" => "core_ajax",
                    "Action" => "new_contact",
                    "UserId" => "{VAR}[0-9]+",
                    "ContactId" => "{VAR}[0-9]+"
                ),
                "Controller" => "CustomersAjaxController",
                "Action" => "editContact",
                "Access" => array("CORE:ADMIN", "CORE:TECH")
            ),        
            

            array(
                "id" => "start_customer_import",
                "Parameters" => array(
                    "Page" => "customer_import",
                    "Action" => "start",
                ),
                "Controller" => "CustomersAjaxController",
                "Action" => "startCustomerImportAction",
                "Access" => array("CORE:ADMIN", "CORE:TECH")
            ),
            
            array(
                "id" => "get_scanners_list",
                "Parameters" => array(
                    "Page" => "core_ajax",
                    "Action" => "scanners_list",
                ),
                "Controller" => "CoreAjaxController",
                "Action" => "scannersList",
                "Access" => array("AUTHENTICATED")
            ),
            
            array(
                "id" => "customer_import_step2",
                "Parameters" => array(
                    "Page" => "customer_import",
                    "Action" => "step2",
                ),
                "Controller" => "CustomersController",
                "Action" => "showImportResult",
                "Access" => array("CORE:ADMIN", "CORE:TECH")
            ),
            
            array(
                "id" => "customer_import_step3",
                "Parameters" => array(
                    "Page" => "customer_import",
                    "Action" => "step3",
                ),
                "Controller" => "CustomersController",
                "Action" => "validImport",
                "Access" => array("CORE:ADMIN", "CORE:TECH")
            ),

            array(
                "id" => "ajax_delete_contact",
                "Parameters" => array(
                    "Page" => "core_ajax",
                    "Action" => "delete_contact",
                    "ContactId" => "{VAR}[0-9]+"
                ),
                "Controller" => "CustomersAjaxController",
                "Action" => "deleteContact",
                "Access" => array("CORE:ADMIN", "CORE:TECH"),
                "CsrfProtection" => true
            ),

            array(
                "id" => "ajax_get_contact",
                "Parameters" => array(
                    "Page" => "core_ajax",
                    "Action" => "get_contact_datas",
                    "ContactId" => "{VAR}[0-9]+"
                ),
                "Controller" => "CustomersAjaxController",
                "Action" => "getContactDatas",
                "Access" => array("CORE:ADMIN", "CORE:TECH")
            ),            
            
            /************* Scripts traditionnels ************************/
            
            /** About page **/
            array(
                "id" => "about",
                "Parameters" => array(
                    "Action" => "about"
                ),
                "Controller" => "HomePageController",
                "Action" => "about",
                "Access" => array("EVERYONE")
            ),
            
            /** Demo page **/
            array(
                "id" => "demo",
                "Parameters" => array(
                    "Action" => "demo"
                ),
                "Controller" => "HomePageController",
                "Action" => "demo",
                "Access" => array("EVERYONE")
            ),
            
            /** Error 404 **/
            array(
                "id" => "error404",
                "Parameters" => array(
                    "Action" => "error404"
                ),
                "Controller" => "HomePageController",
                "Action" => "error404",
                "Access" => array("EVERYONE")
            ),
            
            /** Error 404 **/
            array(
                "id" => "error403",
                "Parameters" => array(
                    "Action" => "error403"
                ),
                "Controller" => "HomePageController",
                "Action" => "error403",
                "Access" => array("EVERYONE")
            ),
            
            /** Bug report page **/
            array(
                "id" => "bugReport",
                "Parameters" => array(
                    "Action" => "bugReport"
                ),
                "Controller" => "HomePageController",
                "Action" => "bugReport",
                "Access" => array("AUTHENTICATED")
            ),
            /** New feature report page **/
            array(
                "id" => "newFeatureReport",
                "Parameters" => array(
                    "Action" => "newFeatureReport"
                ),
                "Controller" => "HomePageController",
                "Action" => "newFeatureReport",
                "Access" => array("AUTHENTICATED")
            ),
            /** Help page **/
            array(
                "id" => "help",
                "Parameters" => array(
                    "Action" => "help"
                ),
                "Controller" => "HomePageController",
                "Action" => "help",
                "Access" => array("EVERYONE")
            ),
            /** Password reset **/
            array(
                "id" => "reset_password",
                "Parameters" => array(
                    "Action" => "ResetPassword"
                ),
                "Controller" => "HomePageController",
                "Action" => "passwordAction",
                "Access" => array("ANONYMOUS")
            ),
            array(
                "id" => "reset_password_confirm",
                "Parameters" => array(
                    "Action" => "changePasswordRequestId",
                    "Key" => "{VAR}[a-zA-Z0-9]{50}"
                ),
                "Controller" => "HomePageController",
                "Action" => "confirmPasswordAction",
                "Access" => array("ANONYMOUS")
            ),
            /** Home page **/
            array(
                "id" => "login_form",
                "Parameters" => array(
                    "Action" => "login",
                    "Force" => "{VAR}(0|1)"
                ),
                "Controller" => "HomePageController",
                "Action" => "loginAction",
                "Access" => array("EVERYONE")
            ),
            /** Quick Tour **/
            array(
                "id" => "quick_tour",
                "Parameters" => array(
                    "Action" => "quick_tour"
                ),
                "Controller" => "HomePageController",
                "Action" => "quickTour",
                "Access" => array("AUTHENTICATED")
            ),
            /** Home page **/
            array(
                "id" => "home_page",
                "Parameters" => array(),
                "Controller" => "HomePageController",
                "Action" => "indexAction",
                "Access" => array("AUTHENTICATED")
            ),
            
            
            /** Page = restore_home_page **/
            array(
                "id" => "restore_home_page",
                "Parameters" => array(
                    "Page" => "restore_home_page"
                ),
                "Controller" => "HomePageController",
                "Action" => "restoreAction",
                "Access" => array("CORE:ADMIN")
            ),
            
            /** Page = save_home_page **/
            array(
                "id" => "save_home_page",
                "Parameters" => array(
                    "Page" => "save_home_page"
                ),
                "Controller" => "HomePageController",
                "Action" => "saveAction",
                "Access" => array("CORE:ADMIN")
            ),
            
            
            // ------------------------------------------------------
            /** Page = companies_show **/
            array(
                "id" => "companies_show",
                "Parameters" => array(
                    "Page" => "companies",
                    "Action" => "show",
                    "Id" => "{VAR}[0-9]+"
                ),
                "Controller" => "CompaniesController",
                "Action" => "showAction",
                "Access" => array("CORE:ADMIN", "CORE:TECH", "CORE:NONE")
            ),
            /** Page = companies_edit **/
            array(
                "id" => "companies_edit",
                "Parameters" => array(
                    "Page" => "companies",
                    "Action" => "edit",
                    "Id" => "{VAR}[0-9]+"
                ),
                "Controller" => "CompaniesController",
                "Action" => "editAction",
                "Access" => array("CORE:ADMIN")
            ),

            /** Page = companies_delete **/
            array(
                "id" => "companies_delete",
                "Parameters" => array(
                    "Page" => "companies",
                    "Action" => "delete",
                    "Id" => "{VAR}[0-9]+"
                ),
                "Controller" => "CompaniesController",
                "Action" => "deleteAction",
                "Access" => array("CORE:ADMIN"),
                "CsrfProtection" => true
            ),

            /** Page = companies_new **/
            array(
                "id" => "companies_new",
                "Parameters" => array(
                    "Page" => "companies",
                    "Action" => "new"
                ),
                "Controller" => "CompaniesController",
                "Action" => "newAction",
                "Access" => array("CORE:ADMIN")
            ),
            /** Page = companies **/
            array(
                "id" => "companies_list",
                "Parameters" => array(
                    "Page" => "companies"
                ),
                "Controller" => "CompaniesController",
                "Action" => "indexAction",
                "Access" => array("CORE:ADMIN")
            ),
            // ------------------------------------------------------

            /** Page = departments_show **/
            array(
                "id" => "departments_show",
                "Parameters" => array(
                    "Page" => "departments",
                    "Action" => "show",
                    "Id" => "{VAR}[0-9]+"
                ),
                "Controller" => "DepartmentsController",
                "Action" => "showAction",
                "Access" => array("CORE:ADMIN", "CORE:TECH")
            ),
            /** Page = departments_edit **/
            array(
                "id" => "departments_edit",
                "Parameters" => array(
                    "Page" => "departments",
                    "Action" => "edit",
                    "Id" => "{VAR}[0-9]+"
                ),
                "Controller" => "DepartmentsController",
                "Action" => "editAction",
                "Access" => array("CORE:ADMIN")
            ),

            /** Page = departments_delete **/
            array(
                "id" => "departments_delete",
                "Parameters" => array(
                    "Page" => "departments",
                    "Action" => "delete",
                    "Id" => "{VAR}[0-9]+"
                ),
                "Controller" => "DepartmentsController",
                "Action" => "deleteAction",
                "Access" => array("CORE:ADMIN"),
                "CsrfProtection" => true
            ),

            /** Page = departments_new **/
            array(
                "id" => "departments_new",
                "Parameters" => array(
                    "Page" => "departments",
                    "Action" => "new"
                ),
                "Controller" => "DepartmentsController",
                "Action" => "newAction",
                "Access" => array("CORE:ADMIN")
            ),
            /** Page = departments **/
            array(
                "id" => "departments_list",
                "Parameters" => array(
                    "Page" => "departments"
                ),
                "Controller" => "DepartmentsController",
                "Action" => "indexAction",
                "Access" => array("CORE:ADMIN", "CORE:TECH")
            ),
            // ------------------------------------------------------


            array(
                "id" => "modules_list",
                "Parameters" => array(
                    "Page" => "modules"
                ),
                "Controller" => "ModulesController",
                "Action" => "indexAction",
                "Access" => array("CORE:ADMIN")
            ),
            
            
            /** Page = employees_show **/
            array(
                "id" => "employees_show",
                "Parameters" => array(
                    "Page" => "employees",
                    "Action" => "show",
                    "Id" => "{VAR}[0-9]+"
                ),
                "Controller" => "EmployeesController",
                "Action" => "showAction",
                "Access" => array("CORE:ADMIN", "CORE:TECH")
            ),
            /** Page = employees_edit **/
            array(
                "id" => "employees_edit",
                "Parameters" => array(
                    "Page" => "employees",
                    "Action" => "edit",
                    "Id" => "{VAR}[0-9]+"
                ),
                "Controller" => "EmployeesController",
                "Action" => "editAction",
                "Access" => array("CORE:ADMIN")
            ),
            /** Page = employees_delete **/
            array(
                "id" => "employees_delete",
                "Parameters" => array(
                    "Page" => "employees",
                    "Action" => "delete",
                    "Id" => "{VAR}[0-9]+"
                ),
                "Controller" => "EmployeesController",
                "Action" => "deleteAction",
                "Access" => array("CORE:ADMIN")
                ,
                "CsrfProtection" => true
            ),
            /** Page = employees_new **/
            array(
                "id" => "employees_new",
                "Parameters" => array(
                    "Page" => "employees",
                    "Action" => "new"
                ),
                "Controller" => "EmployeesController",
                "Action" => "newAction",
                "Access" => array("CORE:ADMIN")
            ),
            /** Page = employees **/
            array(
                "id" => "employees_list",
                "Parameters" => array(
                    "Page" => "employees"
                ),
                "Controller" => "EmployeesController",
                "Action" => "indexAction",
                "Access" => array("CORE:ADMIN", "CORE:TECH")
            ),

            /** Page = customers_show **/
            array(
                "id" => "customers_show",
                "Parameters" => array(
                    "Page" => "customers",
                    "Action" => "show",
                    "Id" => "{VAR}[0-9]+"
                ),
                "Controller" => "CustomersController",
                "Action" => "showAction",
                "Access" => array("CORE:ADMIN", "CORE:TECH")
            ),
            /** Page = customers_edit **/
            array(
                "id" => "customers_edit",
                "Parameters" => array(
                    "Page" => "customers",
                    "Action" => "edit",
                    "Id" => "{VAR}[0-9]+"
                ),
                "Controller" => "CustomersController",
                "Action" => "editAction",
                "Access" => array("CORE:ADMIN", "CORE:TECH")
            ),
            /** Page = customers_new **/
            array(
                "id" => "customers_new",
                "Parameters" => array(
                    "Page" => "customers",
                    "Action" => "new"
                ),
                "Controller" => "CustomersController",
                "Action" => "newAction",
                "Access" => array("CORE:ADMIN", "CORE:TECH")
            ),

            /** Page = customers_delete **/
            array(
                "id" => "customers_delete",
                "Parameters" => array(
                    "Page" => "customers",
                    "Action" => "delete",
                    "Id" => "{VAR}[0-9]+"
                ),
                "Controller" => "CustomersController",
                "Action" => "deleteAction",
                "Access" => array("CORE:ADMIN", "CORE:TECH"),
                "CsrfProtection" => true
            ),

            /** Page = customers **/
            array(
                "id" => "customers_list",
                "Parameters" => array(
                    "Page" => "customers"
                ),
                "Controller" => "CustomersController",
                "Action" => "indexAction",
                "Access" => array("CORE:ADMIN", "CORE:TECH")
            ),
            // ------------------------------------------------------            
            /** Page = suppliers_show **/
            array(
                "id" => "suppliers_show",
                "Parameters" => array(
                    "Page" => "suppliers",
                    "Action" => "show",
                    "Id" => "{VAR}[0-9]+"
                ),
                "Controller" => "SuppliersController",
                "Action" => "showAction",
                "Access" => array("CORE:ADMIN", "CORE:TECH")
            ),
            /** Page = suppliers_edit **/
            array(
                "id" => "suppliers_edit",
                "Parameters" => array(
                    "Page" => "suppliers",
                    "Action" => "edit",
                    "Id" => "{VAR}[0-9]+"
                ),
                "Controller" => "SuppliersController",
                "Action" => "editAction",
                "Access" => array("CORE:ADMIN", "CORE:TECH")
            ),
            /** Page = suppliers_new **/
            array(
                "id" => "suppliers_new",
                "Parameters" => array(
                    "Page" => "suppliers",
                    "Action" => "new"
                ),
                "Controller" => "SuppliersController",
                "Action" => "newAction",
                "Access" => array("CORE:ADMIN", "CORE:TECH")
            ),

            /** Page = suppliers_delete **/
            array(
                "id" => "suppliers_delete",
                "Parameters" => array(
                    "Page" => "suppliers",
                    "Action" => "delete",
                    "Id" => "{VAR}[0-9]+"
                ),
                "Controller" => "SuppliersController",
                "Action" => "deleteAction",
                "Access" => array("CORE:ADMIN", "CORE:TECH"),
                "CsrfProtection" => true
            ), 

            /** Page = suppliers **/
            array(
                "id" => "suppliers_list",
                "Parameters" => array(
                    "Page" => "suppliers"
                ),
                "Controller" => "SuppliersController",
                "Action" => "indexAction",
                "Access" => array("CORE:ADMIN", "CORE:TECH")
            ),       
            // ------------------------------------------------------
            /** Page = old_module_with_only_admin **/
            array(
                "id" => "old_module_with_only_admin",
                "Parameters" => array(
                    "Page" => "old_module",
                    "Action" => "pages_list",
                    "moduleName" => "{VAR}[A-Za-z\-\_0-9]+"
                ),
                "Controller" => "OldModulesController",
                "Action" => "indexAction",
                "Access" => array("AUTHENTICATED")
            ),   
            // ------------------------------------------------------
            /** Page = my_account **/
            array(
                "id" => "my_account",
                "Parameters" => array(
                    "Page" => "myaccount"
                ),
                "Controller" => "MyAccountController",
                "Action" => "editAction",
                "Access" => array("AUTHENTICATED")
            ),
            // ------------------------------------------------------
            /** Page = dl_file **/
            array(
                "id" => "dl_file",
                "Parameters" => array(
                    "Page" => "dl_file",
                    "Type" => "{VAR}[A-Za-z\-\_0-9]+",
                    "Extra" => "{VAR}[0-9]+",
                    "ForceDl" => "{VAR}(0|1)"
                ),
                "Controller" => "DlFileController",
                "Action" => "downloadAction",
                "Access" => array("EVERYONE")
            ),            
            // ------------------------------------------------------            
            /** Page = admin **/
            array(
                "id" => "admin",
                "Parameters" => array(
                    "Page" => "admin",
                    "Action" => "general"
                ),
                "Controller" => "AdminController",
                "Action" => "generalAction",
                "Access" => array("CORE:ADMIN")
            ),        
            
            
            
        );
        
        self::addParam($routes, array("Module" => "core"));
        
        // Check all modules (which match with igestis v2.0 standards)
        $modulesList = IgestisModulesList::getInstance();
        foreach ($modulesList->get() as $module_name => $module) {
            if($module['igestisVersion'] == 2) {
                // Manage the concerned module
                $moduleConfigController = "\\Igestis\\Modules\\" . $module_name . "\\ConfigControllers";
                
                if(class_exists($moduleConfigController)) {
                    $array = $moduleConfigController::get();
                    self::addParam($array, array("Module" => $module_name));
                    $routes = array_merge($routes, $array);
                }
                
            }
        }
        
        self::$routes = $routes;
        return $routes;
    }   
}
