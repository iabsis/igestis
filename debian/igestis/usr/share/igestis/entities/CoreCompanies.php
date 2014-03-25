<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity (repositoryClass="CoreCompaniesRepository")
 * @Table(name="CORE_COMPANIES")
 */
class CoreCompanies
{
    /**
     * @var decimal $tvaRating
     * @Column(type="decimal", precision=8, scale=3, name="tva_rating")
     */
    private $tvaRating;

    /**
     * @var string $name
     * @Column(type="string")
     */
    private $name;

    /**
     * @var string $address1
     * @Column(type="string")
     */
    private $address1;

    /**
     * @var string $address2
     * @Column(type="string")
     */
    private $address2;

    /**
     * @var string $postalCode
     * @Column(type="string", name="postal_code")
     */
    private $postalCode;

    /**
     * @var string $city
     * @Column(type="string")
     */
    private $city;

    /**
     * @var string $phone1
     * @Column(type="string")
     */
    private $phone1;

    /**
     * @var string $phone2
     * @Column(type="string")
     */
    private $phone2;

    /**
     * @var string $mobile
     * @Column(type="string")
     */
    private $mobile;

    /**
     * @var string $fax
     * @Column(type="string")
     */
    private $fax;

    /**
     * @var string $email
     * @Column(type="string")
     */
    private $email;

    /**
     * @var string $siteWeb
     * @Column(type="string", name="site_web")
     */
    private $siteWeb;

    /**
     * @var string $tvaNumber
     * @Column(type="string", name="tva_number")
     */
    private $tvaNumber;

    /**
     * @var text $banque
     * @Column(type="string")
     */
    private $banque;

    /**
     * @var string $iban
     * @Column(type="string")
     */
    private $iban;

    /**
     * @var string $rib
     * @Column(type="string")
     */
    private $rib;

    /**
     * @var string $rcs
     * @Column(type="string")
     */
    private $rcs;

    /**
     * @var string $symbolMoney
     * @Column(type="string", name="symbol_money")
     */
    private $symbolMoney;

    /**
     * @Id @Column(type="integer") @GeneratedValue
     */
    private $id;
    
    /**
     * @Column(type="string", name="logo_file_name")
     * @var string The logo file name
     */
    private $logoFileName;
    
    /**
     * @OneToMany(targetEntity="CoreCompanyRights", mappedBy="company", cascade={"persist", "remove"}, indexBy="company", orphanRemoval=true)
     */
    private $defaultRightsList;
    

    /**
     * Constructor
     */
    public function __construct() {
        $this->defaultRightsList = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Return the list of the company's default rights
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDefaultRightsList() {
        return $this->defaultRightsList;
    }

    /**
     * Add a right to the default right list
     * @param \CoreCompanyRights $right
     * @return \CoreCompanies
     */
    public function addDefaultRight(\CoreCompanyRights  $right) {
        $right->setCompany($this);
        $this->defaultRightsList->add($right);
        return $this;
    }
    
    public function removeRights(\CoreCompanyRights $right = null) {
        if($right === null) $this->defaultRightsList->clear();
        else {
            $this->defaultRightsList->removeElement($right);
        }
    }

    /**
     * Set tvaRating
     *
     * @param decimal $tvaRating
     * @return CoreCompanies
     */
    public function setTvaRating($tvaRating)
    {
        $this->tvaRating = $tvaRating;
        return $this;
    }

    /**
     * Get tvaRating
     *
     * @return decimal 
     */
    public function getTvaRating()
    {
        return $this->tvaRating;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return CoreCompanies
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set address1
     *
     * @param string $address1
     * @return CoreCompanies
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
        return $this;
    }

    /**
     * Get address1
     *
     * @return string 
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address2
     *
     * @param string $address2
     * @return CoreCompanies
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
        return $this;
    }

    /**
     * Get address2
     *
     * @return string 
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     * @return CoreCompanies
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string 
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return CoreCompanies
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set phone1
     *
     * @param string $phone1
     * @return CoreCompanies
     */
    public function setPhone1($phone1)
    {
        $this->phone1 = $phone1;
        return $this;
    }

    /**
     * Get phone1
     *
     * @return string 
     */
    public function getPhone1()
    {
        return $this->phone1;
    }

    /**
     * Set phone2
     *
     * @param string $phone2
     * @return CoreCompanies
     */
    public function setPhone2($phone2)
    {
        $this->phone2 = $phone2;
        return $this;
    }

    /**
     * Get phone2
     *
     * @return string 
     */
    public function getPhone2()
    {
        return $this->phone2;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     * @return CoreCompanies
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
        return $this;
    }

    /**
     * Get mobile
     *
     * @return string 
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set fax
     *
     * @param string $fax
     * @return CoreCompanies
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
        return $this;
    }

    /**
     * Get fax
     *
     * @return string 
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return CoreCompanies
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set siteWeb
     *
     * @param string $siteWeb
     * @return CoreCompanies
     */
    public function setSiteWeb($siteWeb)
    {
        $this->siteWeb = $siteWeb;
        return $this;
    }

    /**
     * Get siteWeb
     *
     * @return string 
     */
    public function getSiteWeb()
    {
        return $this->siteWeb;
    }

    /**
     * Set tvaNumber
     *
     * @param string $tvaNumber
     * @return CoreCompanies
     */
    public function setTvaNumber($tvaNumber)
    {
        $this->tvaNumber = $tvaNumber;
        return $this;
    }

    /**
     * Get tvaNumber
     *
     * @return string 
     */
    public function getTvaNumber()
    {
        return $this->tvaNumber;
    }

    /**
     * Set banque
     *
     * @param text $banque
     * @return CoreCompanies
     */
    public function setBanque($banque)
    {
        $this->banque = $banque;
        return $this;
    }

    /**
     * Get banque
     *
     * @return text 
     */
    public function getBanque()
    {
        return $this->banque;
    }

    /**
     * Set iban
     *
     * @param string $iban
     * @return CoreCompanies
     */
    public function setIban($iban)
    {
        $this->iban = $iban;
        return $this;
    }

    /**
     * Get iban
     *
     * @return string 
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * Set rib
     *
     * @param string $rib
     * @return CoreCompanies
     */
    public function setRib($rib)
    {
        $this->rib = $rib;
        return $this;
    }

    /**
     * Get rib
     *
     * @return string 
     */
    public function getRib()
    {
        return $this->rib;
    }

    /**
     * Set rcs
     *
     * @param string $rcs
     * @return CoreCompanies
     */
    public function setRcs($rcs)
    {
        $this->rcs = $rcs;
        return $this;
    }

    /**
     * Get rcs
     *
     * @return string 
     */
    public function getRcs()
    {
        return $this->rcs;
    }

    /**
     * Set symbolMoney
     *
     * @param string $symbolMoney
     * @return CoreCompanies
     */
    public function setSymbolMoney($symbolMoney)
    {
        $this->symbolMoney = $symbolMoney;
        return $this;
    }

    /**
     * Get symbolMoney
     *
     * @return string 
     */
    public function getSymbolMoney()
    {
        return $this->symbolMoney;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set the logo target
     * @param string $logoFileName Target of the logo file
     */
    public function setLogoFileName($logoFileName) {
        $this->logoFileName = $logoFileName;
    }
    
    /**
     * Return the logo file target
     * @return string Target of the logo
     */
    public function getLogoFileName() {
        return $this->logoFileName;
    }


    /**
     * Return the logo folder (and try to create it if ot exists.
     * @return Folder target
     * @throws Exception If not able to create the folder
     */
    public function getLogoFolder() {
        $folder = \ConfigIgestisGlobalVars::dataFolder() . "/companies_logo/";
        
        try {
            if (!is_dir($folder))
                mkdir($folder);
        } catch (\Exception $exc) {
            throw $exc;
        }

        return $folder;
    }
    
    public function getLogo() {
        if($this->logoFileName == null) return null;
        return array(
            "id" => $this->id,
            "downloadLink" => IgestisConfigController::createUrl("dl_file", array("Type" => "company_logo", "Extra" => $this->id, "ForceDl" => 0)),
            "deleteLink" => "#",
            "imgWidth" => "30",
            "imagHeight" => "30"
        );
    }
}

// -----------------------------------------------------------------------------------------------------------
class CoreCompaniesRepository extends Doctrine\ORM\EntityRepository {

    public function getCompaniesList ($arrayFormat = true) {
                 
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c')
            ->from('CoreCompanies', 'c');

        if($arrayFormat) return $qb->getQuery()->getArrayResult();
        else {
            return $qb->getQuery()->getResult();
        }
    }    
    
    public function getFirst() {
                 
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c')
            ->from('CoreCompanies', 'c')
            ->orderBy("c.id")
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }    

}