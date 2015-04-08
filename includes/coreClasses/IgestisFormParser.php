<?php

/**
 * Description of IgestisFormParser
 *
 * @author Gilles Hemmerlé
 */
class IgestisFormParser {
    private $authorizedFieldsName;
    private $entityManager;
    /**
     * Allow to specifiy a list of authorized fields, if nothing's passed, all fields are allowed
     * 
     * @example  
     * <pre>
     * // Allow only field1 and field2 to be automatically managed
     * $parser = new IgestisFormParser(array("field1", "field2"));
     * 
     * // Allow all fields to be automatically managed
     * $parser2 = new IgestisFormParser();
     * </pre>     * 
     * 
     * @param mixed null|array $authorizedFieldsName List of authorized fields
     */
    public function __construct($authorizedFieldsName = null) {
        if(is_array($authorizedFieldsName)) {
            $this->authorizedFieldsName = $authorizedFieldsName;            
        }
        
        $this->entityManager = Application::getEntityManager();
        
    }
    
    /**
     * Set entity values from the form values
     * 
     * @param Entity $entity
     * @param Array $form
     * @return Entity $entity 
     */    
    public function FillEntityFromForm($entity, $form) {
        if(!is_array($form)) return $entity;
        while(list($key, $value) = each($form)) {
            // Manage only authorized fields if specified
            if(is_array($this->authorizedFieldsName) && !in_array($key, $this->authorizedFieldsName)) continue;
            $action = "set" . $key;
            if(method_exists($entity, $action)) {
                if($value == "") $value = null;
                
                $reflector = new ReflectionClass(get_class($entity));
                if($reflector) {
                    $parameters = $reflector->getMethod($action)->getParameters();
                    if(count($parameters) == 1) {
                        $firstParameterClassName = $parameters[0]->getClass();
                        if($firstParameterClassName) {
                            $parameterType = $firstParameterClassName->name;
                            try {
                                $repository = $this->entityManager->getRepository($parameterType);
                                if($repository) {
                                    $entityValue = $repository->find($value);
                                    $value = $entityValue;
                                }
                                
                            } catch (\Exception $ex) {
                                // $value remain the previous one.
                            }
                        }
                        
                    }
                    
                }
                
                $entity->$action($value);
            }
        }
        return $entity;
    }
}