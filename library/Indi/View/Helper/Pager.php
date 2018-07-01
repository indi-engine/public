<?php
class Indi_View_Helper_Pager {

    /**
     * Current page
     *
     * @var int
     */
    public $page = 1;

    /**
     * Build pager, for page-by-page listing navigation
     *
     * @param int $display
     * @return string
     */
    public function pager($display = 5) {

        // If there was no 'limit' param among $_GET params - return
        if (!Indi::get('limit')) return;

        // Setup current page
        $this->page = Indi::get()->page;

        // Setup rows-per-page count
        $limit = Indi::get()->limit;

        // Setup total found rows
        $found = Indi::view()->rowset->found();

        // Start output buffering
        ob_start();

        // Start building pager html
        ?><div class="pager"><?

        // Get the total pages count
        $pages = ceil($found/$limit);

        // If pages count
        if ($pages > 1) {

            // If current page is not first page - prepend pages listing with special 'previous' link
            if ($this->page > 1) {?><a data-page="<?=$this->page - 1?>" class="previous-link" href="<?=$this->href($this->page - 1)?>">Предыдущая</a><?}

            // If $display arg is non-zero
            if ($display) {

                // Start building pages list html
                ?><span class="pages"><?

                // 1-st page
                $pageA[] = $this->page(1);

                // Setup the maximum quantity of clickable pages list within span.pages
                $display = $display > $pages ? $pages : $display;

                // Get count of pages, that should be displayed in center of pager
                $center = $display - 5;

                // Get start page index for center
                $start = $this->page - ceil($center/2);

                // Shift $start if too small
                if ($start < 3) $start = 3;

                // Get end page index for center
                $end = $start + $center;

                // Shift $end if too big
                if ($end > $pages - 2) {
                    $end = $pages - 2;
                    $start = $end - $center;
                }

                // Another 'previous' page - now it is not outside span.pages, and titled as '...'
                if ($pages > 2)
                    $pageA[] = $this->page($start - 1, ($start - 2 == 1 ? $start - 1 : '...'));

                // Center pages
                if ($pages > 4) {
                    for ($i = $start; $i <= $end; $i++) {
                        $pageA[] = $this->page($i);
                    }
                }

                // Next page (mean next after the last of center)
                if ($pages > 3)
                    $pageA[] = $this->page($end + 1, ($end + 2 == $pages ? $end + 1 : '...'));

                // Last page
                $pageA[] = $this->page($pages);

                // Implode pages html elements
                echo implode(' ', $pageA);

                // Close the span.pages element
                ?></span><?
            }

            // Another 'next' element, which will be outside span.pages element, but within div.pager element
            if ($this->page < $pages) {
                ?><a data-page="<?=$this->page + 1?>" class="next-link" href="<?=$this->href($this->page + 1)?>">Следующая</a><?
            }
        }

        // Close the div.pager element
        ?></div><?

        // Return all built html
        return ob_get_clean();
    }

    /**
     * Build a href for html 'a' tag, representing a certain page number clickable element
     *
     * @param int $page
     * @return mixed|string
     */
    public function href($page =  1) {

        // If 'page' param is already exist within the request uri, we just replace it's value
        if (preg_match('/\bpage=' . Indi::get('page') . '\b/', $GLOBALS['INITIAL_URI'])) {
            return preg_replace('/\bpage=' . Indi::get('page') . '\b/', 'page=' . $page, $GLOBALS['INITIAL_URI']);

        // Else we append it
        } else return rtrim($GLOBALS['INITIAL_URI'], '?&') . ($_SERVER['QUERY_STRING'] ? '&' : '?') . 'page=' . $page;
    }

    /**
     * Build and return the html span/a tag to represent a certain page number within pages list
     *
     * @param int $page
     * @param string $text
     * @return string
     */
    public function page($page, $text = '') {

        // If $text argument is given - use it as a element title, rather than $page argument
        $title = $text ? $text : $page;

        // Build and return page element
        return $page == $this->page ? '<span class="current">' . $title . '</span>' : '<a data-page="' . $page . '" href="' . $this->href($page) . '">' . $title . '</a>';
    }
}