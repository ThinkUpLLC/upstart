<?php
class ListClaimCodesController extends Controller {
    public function control() {
        $this->disableCaching();
        $this->setViewTemplate('bundle.admin-index.tpl');

        $page = (isset($_GET['p']))?(integer)$_GET['p']:1;
        if ($page < 1) {
            $page =1;
        }
        $claim_code_dao = new ClaimCodeMySQLDAO();
        $claim_codes = $claim_code_dao->getClaimCodeList($page, 51);
        $this->addToView('claim_codes', $claim_codes);

        $this->addToView('page', $page);
        if (sizeof($claim_codes) == 51) {
            array_pop($claim_codes);
            $this->addToView('next_page', $page+1);
        }
        if ($page > 1) {
            $this->addToView('prev_page', $page-1);
        }
        return $this->generateView();
    }
}
