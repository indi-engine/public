<?php
class Indi_View_Helper_SiteMetatag {

    /**
     * Store metatag content configuration, groupped by metatag types
     *
     * @var null
     */
    protected static $_rs = null;

    /**
     * @var array
     */
    protected static $_out = array();

    /**
     * Build metatag content for a certain tag type
     *
     * @param $tag
     * @return string
     */
    public function siteMetatag($tag) {

        // Check 'Metatag' model exists
        if (!Indi::model('Metatag', true)) return;

        // If metatag data is not yet fetched
        if (self::$_rs === null) {

            // Fetch it
            $rs = Indi::model('Metatag')->fetchAll('`fsection2factionId` = "' . Indi::trail()->section2action->id . '"', '`move`');

            // Setup foreign data for 'fieldId' property
            $rs->foreign('fieldId');

            // Distribute metatag data to 3 groups - title, description and keywords
            self::$_rs['title'] = $rs->select('title', 'tag');
            self::$_rs['keywords'] = $rs->select('keywords', 'tag');
            self::$_rs['description'] = $rs->select('description', 'tag');

            // Unset $rs
            unset($rs);
        }

        // If metatag contents was already built - use it
        if (array_key_exists($tag, self::$_out)) return self::$_out[$tag];

        // Declare array of metatag content items
        $outA = array();

        // Foreach row within current metadata group, identified by $tag argument
        foreach (self::$_rs[$tag] as $r) {

            // Declare/reset
            $outI = '';
        
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

                    // Else if field, pointed as a place of getting data - is a single-value foreign key field
                    } else if ($r->foreign('fieldId')->storeRelationAbility == 'one') {

                        // Get title, got by that foreign key, to current content item
                        $outI .= Indi::trail($r->up)->row->foreign($r->foreign('fieldId')->alias)->title();
                    
                    // Else if field, pointed as a place of getting data - is a multi-value foreign key field
                    } else if ($r->foreign('fieldId')->storeRelationAbility == 'many') {
                    
                        // Get title, got by that foreign key, to current content item
                        if ($frs = Indi::trail($r->up)->row->foreign($r->foreign('fieldId')->alias)) {
                            
                            // Titles array
                            $titleA = array(); foreach ($frs as $fr) $titleA[] = $fr->title();
                            
                            // Append titles
                            if ($titleA) $outI .= im($titleA, ', ');
                        }
                    }
                }
            }

            // Prepend prefix and append postfix
            if ($outI) {
            
                $outI = $r->prefix . $outI . $r->postfix;

                // Append builded item to items stack
                $outA[] = $outI;
            }
        }

        // Append string, that should be constantly presented within metatag contents
        if ($const = Indi::ini('metatag')->const) $outA[] = $const;

        // Return imploded items
        return self::$_out[$tag] = str_replace('"', '&quot;', implode(Indi::ini('metatag')->delim, $outA));
    }
}