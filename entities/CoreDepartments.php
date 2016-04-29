<?php

/**
 * CoreDepartments
 *
 * @Table(name="CORE_DEPARTMENTS")
 * @Entity(repositoryClass="DepartmentsRepository")
 * @HasLifecycleCallbacks
 */
class CoreDepartments
{
    /**
     * @var string $label
     *
     * @Column(name="label", type="string", length=45)
     */
    private $label;

    /**
     * @var string $ldapObjectId
     *
     * @Column(name="ldap_object_id", type="string", length=45)
     */
    private $ldapObjectId;

    /**
     * @var text $description
     *
     * @Column(name="description", type="text")
     */
    private $description;

    /**
     * @JoinColumn(name="company_id", referencedColumnName="id")
     * @OneToOne(targetEntity="CoreCompanies")
     * @var CoreCompanies
     */
    private $company;

    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ManyToMany(targetEntity="CoreUsers", mappedBy="departments", orphanRemoval=true)
     */
    private $users;

    /**
     * @OneToMany(targetEntity="CoreDepartmentsRights", mappedBy="department", cascade={"persist", "remove"}, indexBy="moduleName", orphanRemoval=true)
     */
    private $rightsList;

    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->rightsList = new Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set label
     *
     * @param string $label
     * @return CoreDepartments
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set description
     *
     * @param text $description
     * @return CoreDepartments
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return text
     */
    public function getDescription()
    {
        return $this->description;
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
     * Add user
     *
     * @param CoreUsers $user
     * @return CoreDepartments
     */
    public function addUser(\CoreUsers $user)
    {
        $this->users[] = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set company
     *
     * @param CoreCompanies $company
     * @return CoreUsers
     */
    public function setCompany(\CoreCompanies $company = null)
    {
        $this->company = $company;
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

    /**
     * Liste des droits
     * @return Doctrine\Common\Collections\ArrayCollection()
     */
    public function getRightsList()
    {
        return $this->rightsList;
    }

    public function setRightsList()
    {

    }

    /**
     * Add a right to the department
     * @param \CoreDepartmentsRights $right
     * @return \CoreDepartments
     */
    public function addRightsList(\CoreDepartmentsRights $right = null)
    {
        $right->setDepartment($this);
        $this->rightsList->add($right);
        return $this;
    }

    /**
     * Remove all rights of the deparment
     * @return \CoreDepartments
     */
    public function removeAllRights()
    {
        $this->rightsList->clear();
        return $this;
    }

    /**
     * Remove a right to the department
     * @param \CoreDepartmentsRights $right
     */
    public function removeRight(\CoreDepartmentsRights $right)
    {
        $this->rightsList->removeElement($right);
        $right->unsetDepartment();
    }

    /**
     * @Presave
     * @Prepersist
     */
    public function PreSave()
    {
        if ($this->getCompany() == null) {
            $security = IgestisSecurity::init();
            $userCompany = $security->user->getCompany();
            if ($userCompany != null) {
                $this->company = $userCompany;
            }
        }
    }

    /**
     * Gets the value of ldapObjectId.
     *
     * @return string $ldapObjectId
     */
    public function getLdapObjectId()
    {
        return $this->ldapObjectId;
    }

    /**
     * Sets the value of ldapObjectId.
     *
     * @param string $ldapObjectId $ldapObjectId the ldap object id
     *
     * @return self
     */
    public function setLdapObjectId($ldapObjectId)
    {
        $this->ldapObjectId = $ldapObjectId;

        return $this;
    }

    /**
     * @PrePersist
     * @PreUpdate
     */
    public function PrePersist()
    {

        if (\ConfigIgestisGlobalVars::useLdap()) {
            $ldap = \Igestis\Utils\IgestisLdap::getStandardConnexion();
            $rdn = \ConfigIgestisGlobalVars::ldapBase();

            if ($this->ldapObjectId) {
                $search = ldap_search($ldap, $rdn, str_replace('*', $this->ldapObjectId, '(|(objectSID=*)(gidNumber=*))'));
                $info = ldap_get_entries($ldap, $search);
            } else {
                $searchString = str_replace("%g", $this->getLabel(), \ConfigIgestisGlobalVars::ldapGroupFilter());
                $search = ldap_search($ldap, $rdn, $searchString);
                $info = ldap_get_entries($ldap, $search);
            }

            $dn = "";

            if (isset($info[0])) {
                $group = $info[0];
                ldap_modify($ldap, $group['dn'], array(
                    "description" => array($this->description),
                ));
                preg_match('#(([a-z]+)=([\W\w]+?)),[\W\w]+#i', $group['dn'], $matches);

                $newDn = str_replace($matches[0], $matches[2] . "=" . \Igestis\Utils\IgestisLdap::ldapEscapeString($this->label), $group['dn']);

                ldap_rename($ldap, $group['dn'], $newDn, null, true);

                $this->ldapObjectId = \Igestis\Utils\IgestisLdap::sidBinToString($group['objectsid'][0]);
            } else {
                $group = array(
                    "objectclass" => array('top', 'group'),
                    "name" => $this->label,
                    "description" => array($this->description),
                );

                if (!\ConfigIgestisGlobalVars::ldapGroupsNewRdn() || !\ConfigIgestisGlobalVars::ldapGroupsOu()) {
                    throw new \Exception(\Igestis\I18n\Translate::_("Please set the LDAP_GROUPS_OU value in the config.ini file"));
                }

                $rdn = str_replace('%groupname%', \Igestis\Utils\IgestisLdap::ldapEscapeString($this->label), \ConfigIgestisGlobalVars::ldapGroupsNewRdn());

                ldap_add($ldap, $rdn, $group);

                $searchString = str_replace("%g", $this->getLabel(), \ConfigIgestisGlobalVars::ldapGroupFilter());
                $search = ldap_search($ldap, $rdn, $searchString);
                $info = ldap_get_entries($ldap, $search);
                if (!empty($info[0]['objectsid'][0])) {
                    $this->ldapObjectId = \Igestis\Utils\IgestisLdap::sidBinToString($info[0]['objectsid'][0]);
                }

            }

            if (ldap_errno($ldap)) {
                throw new \Exception(sprintf(\Igestis\I18n\Translate::_("Ldap error : '%s'"), ldap_error($ldap)));
            }

        }
    }

}

// ------------------------------------------------------

class DepartmentsRepository extends Doctrine\ORM\EntityRepository
{

    public function getDepartmentsList($arrayMode = true)
    {
        try {
            $userCompany = IgestisSecurity::init()->user->getCompany();
            $qb = $this->_em->createQueryBuilder();
            $qb->select("d")
                ->from("CoreDepartments", "d")
                ->where("d.company = :company")
                ->setParameter("company", $userCompany)
                ->orderBy("d.label", "ASC");
            if ($arrayMode) {
                return $qb->getQuery()->getArrayResult();
            } else {
                return $qb->getQuery()->getResult();
            }

        } catch (Exception $e) {
            \IgestisErrors::createWizz($e, IgestisErrors::TYPE_ANY);
            return null;
        }
    }

    public function getDepartmentRights()
    {

    }

}
