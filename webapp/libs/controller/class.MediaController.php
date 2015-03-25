<?php
/**
 * Create various pricing level interfaces to Twitter and Facebook.
 */
class MediaController extends SignUpHelperController {
    public function control() {
        $this->setViewTemplate('media.tpl');

        $this->setPageTitle('For Media &amp; Publishers');

        return $this->generateView();
    }
}
