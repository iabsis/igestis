<?php



/**
 * @Entity (repositoryClass="CoreUsersRightsRepository")
 * @Table(name="CORE_USERS_RIGHTS")
 */
class CoreUsersRights
{
    /**
     * @var string $rightCode
     * @Column(type="string", name="right_code")     
     */
    private $rightCode;

    /**
     * @var integer $userId
     * @Column(name="user_id")
     * @Id
     */
    private $userId;

    /**
     * @var string $moduleName
     * @Column(name="module_name")
     * @Id
     */
    private $moduleName;

    /**
     * @var CoreUsers
     * @JoinColumn(name="user_id")
     * @ManyToOne(targetEntity="CoreUsers", inversedBy="id")
     */
    private $user;
    
    public  function __construct($userId, $moduleName, $rightCode) {
        $this->userId = $userId;
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
     * Set userId
     *
     * @param integer $userId
     * @return CoreUsersRights
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
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
     * Set user
     *
     * @param CoreUsers $user
     * @return CoreUsersRights
     */
    public function setUser(\CoreUsers $user = null)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return CoreUsers 
     */
    public function getUser()
    {
        return $this->user;
    }
    
    public function unsetUser() {
        $this->user = NULL;
    }
}


// -----------------------------------------------------------------------------------------------------------
class CoreUsersRightsRepository extends Doctrine\ORM\EntityRepository {
    function getUserRights($user_id) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('r')
            ->from('CoreUsersRights', 'r')
            ->where('r.userId = :user_id')
            ->setParameter('user_id', $user_id);

        return $qb->getQuery()->getArrayResult();
    }

}