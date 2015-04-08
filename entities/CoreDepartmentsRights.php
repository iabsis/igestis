<?php

/**
 * @Entity (repositoryClass="CoreDepartmentsRightsRepository")
 * @Table(name="CORE_ASSOC_DEPARTMENTS_RIGHTS")
 */
class CoreDepartmentsRights
{
    /**
     * @var string $rightCode
     * @Column(type="string", name="right_code")     
     */
    private $rightCode;

    /**
     * @var integer $departmentId
     * @Column(name="department_id")
     * @Id
     */
    private $departmentId;
    

    /**
     * @var string $moduleName
     * @Column(name="module_name")
     * @Id
     */
    private $moduleName;

    /**
     * @var CoreUsers
     * @JoinColumn(name="department_id")
     * @ManyToOne(targetEntity="CoreDepartments", inversedBy="id")
     */
    private $department;
    
    public  function __construct($departmentId, $moduleName, $rightCode) {
        $this->departmentId = $departmentId;
        $this->moduleName = $moduleName;
        $this->rightCode = $rightCode;
    }

    /**
     * Set rightCode
     *
     * @param string $rightCode
     * @return CoreUsersRights
     */
    public function setRightCode($rightCode)
    {
        $this->rightCode = $rightCode;
        return $this;
    }

    /**
     * Get rightCode
     *
     * @return string 
     */
    public function getRightCode()
    {
        return $this->rightCode;
    }

    /**
     * Set departmentId
     *
     * @param integer $departmentId
     * @return CoreUsersRights
     */
    public function setUserId($departmentId)
    {
        $this->departmentId = $departmentId;
        return $this;
    }

    /**
     * Get departmentId
     *
     * @return integer 
     */
    public function getDepartmentId()
    {
        return $this->departmentId;
    }

    /**
     * Set moduleName
     *
     * @param string $moduleName
     * @return CoreUsersRights
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
        return $this;
    }

    /**
     * Get moduleName
     *
     * @return string 
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * Set department
     *
     * @param CoreDepartments $department
     * @return CoreDepartmentsRights
     */
    public function setDepartment(\CoreDepartments $department = null)
    {
        $this->department = $department;
        return $this;
    }

    /**
     * Get department
     *
     * @return CoreUsers 
     */
    public function getDepartment()
    {
        return $this->department;
    }
    
    public function unsetDepartment() {
        $this->department = NULL;
    }
}


// -----------------------------------------------------------------------------------------------------------
class CoreDepartmentsRightsRepository extends Doctrine\ORM\EntityRepository {
    function getDepartmentRights($department_id) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('r')
            ->from('CoreDepartmentsRights', 'r')
            ->where('r.departmentId = :departmentId')
            ->setParameter('departmentId', $department_id);

        return $qb->getQuery()->getArrayResult();
    }

}