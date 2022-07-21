<?php
class Indi_View_Helper_SiteFavicon {
    public function siteFavicon() {

        // Get the favicon file location
        $rel = view()->blocks['favicon-path'];

        // Get the favicon file absolute path
        $abs = DOC . $rel;

        // If favicon has '.ico' extension and it is a file
        if (preg_match('/\.ico$/', $abs) && file_exists($abs)){

            // Prevent caching
            $nocache = '?' . filemtime($abs);

            // Start output buffering
            ob_start();

            // Build the favicon 'link' tags
			?><link rel="icon" href="<?=$rel . $nocache?>" type="image/x-icon"><?
			?><link rel="shortcut icon" href="<?=$rel . $nocache?>" type="image/x-icon"><?

            // Get the buffere content and return it
            return ob_get_clean();
		} 
    }
}