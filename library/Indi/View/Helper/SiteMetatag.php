<?php
class Indi_View_Helper_SiteMetatag {

    /**
     * Store metatag content configuration, groupped by metatag types
     *
     * @var null
     */
    protected static $_rs = null;

    /**
     * Build metatag content for a certain tag type
     *
     * @param $tag
     * @return string
     */
    public function siteMetatag($tag) {

        // If metatag data is not yet fetched
        if (self::$_rs === null) {

            // Fetch it
            $rs = Indi::model('Metatag')->fetchAll('`fsection2factionId` = "' . Indi::trail()->section2action->id . '"', '`move`');

            // Setup foreign data for 'fieldId' property
            $rs->foreign('fieldId');

            // Distribute metatag data to 3 groups - title, description and keywords
            self::$_rs['title'] = $rs->select('title', 'tag');
            self::$_rs['keywords'] = $rs->select('keywords', 'tag');
            self::$_rs['description'] = $rs->select('keyword', 'tag');

            // Unset $rs
            unset($rs);
        }

        // Declare array of metatag content items
        $outA = array();

        // Foreach row within current metadata group, identified by $tag argument
        foreach (self::$_rs[$tag] as $r) {

            // Init metatag current content item with a prefix
            $outI = $r->prefix;

            // If item is static - append it's contents to current content item. Else
            if ($r->type == 'static') $outI .= $r->content; else {

                // If current content item's source is 'section'
                if ($r->source == 'section') {

                    // Append section's title, got from an appropriate trail level, to the current content item
                    $outI .= Indi::trail($r->up)->section->title();

                // Else if current content item's source is 'action'
                } else if ($r->source == 'action') {

                    // Append action's title, got from an appropriate trail level, to the current content item
                    $outI .= Indi::trail($r->up)->action->title();

                // Else if current content item's source is 'row'
                } else if ($r->source == 'row') {

                    // If field, pointed as a place of getting data - is not a foreign key field
                    if ($r->foreign('fieldId')->storeRelationAbility == 'none') {

                        // Append the value of field, that row, got from an appropriate trail level, have
                        $outI .= Indi::trail($r->up)->row->{$r->foreign('fieldId')->alias};

                    // Else if field, pointed as a place of getting data - is a foreign key field
                    } else {

                        // Get title, got by that foreign key, to current content item
                        $outI .= Indi::trail($r->up)->row->foreign($r->foreign('fieldId')->alias)->title();
                    }
                }
            }

            // Append postfix
            $outI .= $r->postfix;

            // Append builded item to items stack
            $outA[] = $outI;
        }

        // Return imploded items
        return implode('', $outA);
    }
}