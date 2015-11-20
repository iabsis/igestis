<?php
namespace Igestis\Exceptions;

class UploadException extends \Exception {
    private $detailsButtonText;
    private $detailsButtonLink;
    private $detailsButtonTarget;
    
    /**
     * 
     * @return Get the button html code
     */
    public function getDetails() {
        if(!$this->detailsButtonLink) return null;
        return '<a href="' . $this->detailsButtonLink . '" target="' . $this->detailsButtonTarget . '" class="btn btn-default igestis-upload-error-button">' . $this->detailsButtonText . '</a>';
    }

    /**
     * Set the description button for the upload failed wizz
     * @param string $buttonText Text button
     * @param stirng $buttonLink Url to launch when button clicked
     * @param string $target Url target (default _self). Set _blank to open in another page
     */
    public function setDetails($buttonText, $buttonLink, $target='_self') {
        $this->detailsButtonText = $buttonText;
        $this->detailsButtonLink = $buttonLink;
        $this->detailsButtonTarget = $target;
    }


}