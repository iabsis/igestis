<?php

namespace Igestis\DataTables;

/**
 * This class generate a fully complient datatable output
 *
 * @author Gilles Hemmerlé <giloux@gmail.com>
 */
class AjaxDatatableOutput {
    /**
     *
     * @var \Doctrine\ORM\QueryBuilder $queryBuilder Formatted doctrine request
     */
    private $queryBuilder;
    /**
     * @var int Total result without filtering
     */
    private $totalCount;
    /**
     *
     * @var \Symfony\Component\HttpFoundation\Request $request Http request
     */
    private $request;
    
    /**
     *
     * @var array Datatable output
     */
    private $output;
    
    /**
     *
     * @var AjaxDatatableSorting 
     */
    private $sortingManager;
    
    /**
     * 
     * @param \IgestisFormRequest $request
     * @param \Doctrine\ORM\QueryBuilder $queryBuilderWithoutSearch
     * @param int $totalCount
     */
    public function __construct(\IgestisFormRequest $request, \Doctrine\ORM\QueryBuilder $queryBuilderWithoutSearch, $totalCount) {
        $this->queryBuilder = $queryBuilderWithoutSearch;
        $this->totalCount = $totalCount;
        $this->request = $request;
        
        $this->output = array(
            "sEcho" => intval(\IgestisFormRequest::getFromPostOrGet('sEcho')),
            "iTotalRecords" => $this->totalCount,
            "iTotalDisplayRecords" => $this->totalCount,
            "aaData" => array()
        );        
        
    }
    
    /**
     * 
     * @param \Igestis\DataTables\AjaxDatatableSorting $sorting Sorting manager
     */
    public function sortingManager(AjaxDatatableSorting $sorting) {
        $this->sortingManager = $sorting;
        
        foreach ($this->request->getQuery() as $key => $value) {
            $return = null;
            if(!preg_match('/iSortCol_([0-9]+)/', $key, $return)) continue;                     
            $searchId = $return[1];
            
            $fieldId = \IgestisFormRequest::getFromPostOrGet("iSortCol_" . $searchId);
            $fieldOrderDir = \IgestisFormRequest::getFromPostOrGet("sSortDir_" . $searchId);
            if(!$this->sortingManager->getField($fieldId)) continue;
            $this->queryBuilder->addOrderBy($this->sortingManager->getField($fieldId), $fieldOrderDir);
            
        }
        
        $this->queryBuilder
            ->setFirstResult(\IgestisFormRequest::getFromPostOrGet("iDisplayStart"))
            ->setMaxResults(\IgestisFormRequest::getFromPostOrGet("iDisplayLength"));
    }
    
    /**
     * Add a row of data to the output
     * @param array $row
     */
    public function addRow(array $row) {
        $this->output['aaData'][] = $row;
    }
    
    
    /**
     * Return the json encoded output
     * @return string Json encoded output to send to the datatable
     */
    public function output() {
        return json_encode($this->output);        
    }
}