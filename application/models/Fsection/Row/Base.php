<?php
class Fsection_Row_Base extends Indi_Db_Table_Row {

    /**
     * Get the array of entity ids, that used in current section and all parent sections up to the top
     *
     * @param bool $imploded
     * @return array|string
     */
    public function entityRoute($imploded = false) {

        // Start fulfil entity id stack
        $routeA = array($this->entityId);

        // Setup initial id of parent section
        $parent = array('fsectionId' => $this->fsectionId);

        // Navigate through parent sections up to the root
        while ($parent = Indi::db()->query('
                SELECT `fsectionId`,`entityId` FROM `fsection` WHERE `id` = "' . $parent['fsectionId'] . '" LIMIT 1
            ')->fetch()) {

            // Else push new item in $routeA stack
            $routeA[] = $parent['entityId'];
        }

        // Return array of entity ids (as array, or comma-imploded string)
        return $imploded ? implode(',', $routeA) : $routeA;
    }
}