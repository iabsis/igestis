<?php

/**
 * @var CoreContacts $contact Représentation of the contact to be updated / created
 */

return array(
        "objectClass" => array("top", "sambaSamAccount"),
        "homeDirectory" => "/home/" . $this->contact->getLogin(),
        "sambaAcctFlags" => "[U]",
        "sambaHomeDrive" => ConfigModuleVars::homeDrive,
        "sambaHomePath" => $homePath,
        "sambaKickoffTime" => "2147483647",
        "sambaPrimaryGroupSID" => ConfigModuleVars::sambaSID . "-513",
        "sambaProfilePath" => $profilePath,
        "sambaPwdCanChange" => "0",
        "sambaPwdLastSet" => "2147483647", // Must be set to today
        "sambaPwdMustChange" => "2147483647", // Must be set to today + SAMBA_PASSWORD_EXPIRATION
        "sambaLMPassword" => $sambapassword->lmhash($plainPassword),
        "sambaNTPassword" => $sambapassword->nthash($plainPassword)
);