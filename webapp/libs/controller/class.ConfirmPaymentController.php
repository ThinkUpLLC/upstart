<?php
class ConfirmPaymentController extends Controller {
    /**
     * TODO: Detect Amazon Payment state, charge, and thank user
     * @return str HTML page
     */
    public function control() {
        $this->setViewTemplate('confirm-payment.tpl');
        $this->addToView('placeholder', 'DESIGN GOES HERE');
        return $this->generateView();
    }
}
