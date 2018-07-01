<?php
class User_Row_Base extends Indi_Db_Table_Row {

    /**
     * General purpose of this function is to make user logout in
     * an easy-shortcut usage style, e.g Indi::user()->logout();
     */
    public function logout() {

        // Update last visit's timestamp
        $this->lastVisit = date('Y-m-d H:i:s');
        $this->save();

        // Remove 'user' key from $_SESSION
        unset($_SESSION['user']);

        // Remove the same one from registry
        Indi::registry('user', null);
    }

    /**
     * Alias for logout()
     */
    public function signout() {
        $this->logout();
    }
}
