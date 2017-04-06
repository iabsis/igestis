<?php

namespace Igestis\Utils;

/**
 * Description of Scanner
 *
 * @author Gilles Hemmerlé <giloux@gmail.com>
 */
class Scanner {
    /**
     * Return the list of scanners detected by the server
     * @return array List of scanners. array(array("fullName" => "", "displayName" => ""))
     */
    public static function scannersList() {
        $data = null;
        exec("scanimage -L", $data, $returnCode);
        
        if ($data[0] == "" || ereg("No scanners were identified", $data[0])) {
            return array();
        } elseif ($returnCode == "127") {
            throw new \Exception(\Igestis\I18n\Translate::_("Command not found"));
        } else {
            $scannersList = array();

            for ($i = 0; $i < count($data); $i++) {
                // Recherche de la chaine entre les ``
                $c = "";
                $j = 0;
                $scannerAddress = "";
                $scannerLabel = "";
                $fullString = $data[$i];
                // Déplacement jusqu'au début de la chaine definissant le scanner
                while ($c != "`" && $j <= strlen($fullString)) {
                    $c = $fullString[$j];
                    $j++;
                }
                $c = "";
                // Enregistrement de la chaine de l'adresse de scanner et deplacement jusqu'à la fin de la chaine
                while ($c != "'" && $j <= strlen($fullString)) {
                    $c = $fullString[$j];
                    if ($c != "'")
                        $scannerAddress .= $c;
                    $j++;
                }
                $j+=6;
                if (strlen($fullString) > $j)
                    $scannerLabel = substr($fullString, $j);
                else
                    $scannerLabel = _("No label");

                // On recupère le nom du serveur
                $tmp = str_replace("net:", "", $scannerAddress);
                $server = "";
                $i = 0;
                while ($tmp[$i] != ":" && $i < strlen($tmp)) {
                    $server .= $tmp[$i];
                    $i++;
                }
                $scannersList[] = array("fullName" => $scannerAddress, "displayName" => \Igestis\Types\EnhancedString::cutSentencee($scannerLabel, 25) . " (" . $server . ")");
            }
        }
        return $scannersList;
    }
    
    /**
     * Launch scanner and save the document into the $targetFolder folder
     * @param string $scannerAddress Address of a scanner (one from the list returned by the scannersList() method
     * @param string $targetFolder Folder where the file has to be saved.
     * @param string $filename Name of the scanned file
     * @param string $format A4 or check (porion of document to scan)
     * @return boolean True if scann success, false, else
     * @throws Exception If target folder is not writeable or if file already exists and is not deletable.
     */
    public static function launchScanner($scannerAddress, $targetFolder, $filename, $format = "A4") {
        $returnVar = NULL;
        $isSambaTarget = false;
        
        if(!is_writable($targetFolder)) {
            throw new \Exception(sprintf(\Igestis\I18n\Translate::_("The '%s' folder is not writeable. Unable to scan document into this folder"), $targetFolder));
        }
        if(is_file($targetFolder . "/" . $filename) && is_writable($targetFolder . "/" . $filename)) {
            throw new \Exception(sprintf(\Igestis\I18n\Translate::_("The file '%s' already exists and is not deletable. Unable to scan document."), $targetFolder . "/" . $filename));
        }

        if (eregi("^smb://", $targetFolder))
            $isSambaTarget = true;

        // On initialise $format sur A4 si la valeur n'est pas initialisé.
        if ($format == "")
            $format = "A4";

        // Suivant le format, on adapte les coordonnées de scan.
        switch ($format) {
            case "A4":
                $sheet = "-l 0 -t 0 -x 215 -y 297";
                break;
            case "check":
                $sheet = "-l 2 -t 2 -x 175 -y 78";
                break;
            default:
                $sheet = "-l 0 -t 0 -x 215 -y 297";
                break;
        };


        if ($isSambaTarget) {
            $realFolder = $targetFolder;
            $realFilename = $filename;

            $targetFolder = sys_get_temp_dir();

            do {
                $filename = super_randomize(15);
            } while (is_file($targetFolder . $filename));
        }

        $output = null;
        $script = "scanimage --device=" . escapeshellarg($scannerAddress) . " --source \"ADF Front\" --format=pnm --mode=Gray --resolution=100 " . $sheet . "| cjpeg > " . escapeshellarg($targetFolder . "/" . $filename);
        exec($script, $output, $returnVar);
        if ($returnVar) {
            $script = "scanimage --device=" . escapeshellarg($scannerAddress) . " --source \"Flatbed\" --format=pnm --mode=Gray --resolution=100 " . $sheet . "| cjpeg > " . escapeshellarg($targetFolder . "/" . $filename);
            exec($script, $output, $returnVar);
        }

        if ($isSambaTarget) {
            @copy($targetFolder . $filename, $realFolder . "/" . $realFilename);
            @unlink($targetFolder . $filename);
        }

        if (($isSambaTarget && !smb::is_file($realFolder . "/" . $realFilename)) || !is_file($targetFolder . "/" . $filename)) {
            return false;
        }
        else {
            return true;            
        }
    }
}

?>
