<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * CoreUserprefs
 */
class CoreUserprefs
{
    /**
     * @var string $value
     */
    private $value;

    /**
     * @var integer $contactId
     */
    private $contactId;

    /**
     * @var string $option
     */
    private $option;

    /**
     * @var CoreContacts
     */
    private $contact;


    /**
     * Set value
     *
     * @param string $value
     * @return CoreUserprefs
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set contactId
     *
     * @param integer $contactId
     * @return CoreUserprefs
     */
    public function setContactId($contactId)
    {
        $this->contactId = $contactId;
        return $this;
    }

    /**
     * Get contactId
     *
     * @return integer 
     */
    public function getContactId()
    {
        return $this->contactId;
    }

    /**
     * Set option
     *
     * @param string $option
     * @return CoreUserprefs
     */
    public function setOption($option)
    {
        $this->option = $option;
        return $this;
    }

    /**
     * Get option
     *
     * @return string 
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * Set contact
     *
     * @param CoreContacts $contact
     * @return CoreUserprefs
     */
    public function setContact(\CoreContacts $contact = null)
    {
        $this->contact = $contact;
        return $this;
    }

    /**
     * Get contact
     *
     * @return CoreContacts 
     */
    public function getContact()
    {
        return $this->contact;
    }
}