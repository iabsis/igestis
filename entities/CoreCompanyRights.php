<?php

/**
 * CoreCompanyRights
 *
 * @Table(name="CORE_COMPANY_RIGHTS")
 * @Entity
 */
class CoreCompanyRights
{
    /**
     * @var string $rightCode
     *
     * @Column(name="right_code", type="string", length=12)
     */
    private $rightCode;

    /**
     * @var integer $companyId
     *
     * @Column(name="company_id", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    private $companyId;

    /**
     * @var string $moduleName
     *
     * @Column(name="module_name", type="string")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    private $moduleName;

    /**
     * @var CoreCompanies
     *
     * @ManyToOne(targetEntity="CoreCompanies")
     * @JoinColumns({
     *   @JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    private $company;


    /**
     * Set rightCode
     *
     * @param string $rightCode
     * @return CoreCompanyRights
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
     * Set companyId
     *
     * @param integer $companyId
     * @return CoreCompanyRights
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
        return $this;
    }

    /**
     * Get companyId
     *
     * @return integer 
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * Set moduleName
     *
     * @param string $moduleName
     * @return CoreCompanyRights
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
     * Set company
     *
     * @param CoreCompanies $company
     * @return CoreCompanyRights
     */
    public function setCompany(\CoreCompanies $company = null)
    {
        $this->company = $company;
        $this->companyId = $company->getId();
        return $this;
    }

    /**
     * Get company
     *
     * @return CoreCompanies 
     */
    public function getCompany()
    {
        return $this->company;
    }
}