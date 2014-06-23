<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity (repositoryClass="CoreHomePageContentRepository")
 * @Table(name="CORE_HOMEPAGE_CONTENT")
 */
class CoreHomePageContent
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     */
    private $id;
    
    /**
     * @Column(type="datetime", name="date_add")
     */
    private $dateAdd;
    
    /**
     * @Column(type="text", name="content")
     * @var string Contenu de la homepage 
     */
    private $content;
    
    public function __construct() {
        $this->dateAdd = new \DateTime('now');
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set Id
     *
     * @return string 
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    
    /**
     * Retourne la date d'ajout
     * @return string
     */
    public function getDateAdd() {
        return $this->dateAdd;
    }
    
    /**
     * Crée la date d'ajout
     * @param string $date
     */
    public function setDateAdd($date) {
        $this->dateAdd = $date;
    }
    
    /**
     * Retourne le contenu de la page
     * @return string
     */
    public function getContent() {
        return $this->content;
    }
    
    /**
     * Enregistre le contenu de la page
     * @param string $content
     */
    public function setContent($content) {
        $this->content = $content;
    }
    
    /** @PrePersist */
    function onPrePersist()
    {
        //using Doctrine DateTime here
        $this->dateAdd = new \DateTime('now');
    }
    
}


// -----------------------------------------------------------------


class CoreHomePageContentRepository extends Doctrine\ORM\EntityRepository {
    public function getHomePageContent() {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('pc')
            ->from('CoreHomePageContent', 'pc')
            ->orderBy('pc.dateAdd', 'DESC')
            ->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }
}