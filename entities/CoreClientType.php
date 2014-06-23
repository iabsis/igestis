<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity
 * @Table(name="CORE_CLIENT_TYPE")
 */
class CoreClientType
{
    /**
     * @Id @var string $code
     * @Column(type="string")
     */
    private $code;

    /**
     * @var string $label
     * @Column(type="string")
     */
    private $label;
    

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }
    
    /**
     * Get Label
     * @return String label 
     */
    public function getLabel() {
        return $this->label;
    }
    
    /**
     * Set label
     * @param String $label 
     */
    public function setLabel($label) {
        $this->label = $label;
    }
}