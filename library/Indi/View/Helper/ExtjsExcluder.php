<?php
class Indi_View_Helper_ExtjsExcluder {
    public function extjsExcluder ($rel) {
        return $rel;
        // Find css-file absolute path
        foreach (ar('www,coref,core') as $rep) if (is_file($abs = DOC . '/' . $rep .  $rel)) break;

        $info = pathinfo($abs);
        $abs_safe = $info['dirname'] . '/' . $info['filename'] . '-extjs-excluded' . '.' . $info['extension'];
        $rel_safe = str_replace(DOC . '/' . $rep, '', $abs_safe);

        // Detect whether we should refresh contents of file with extjs-safe css
        if (!(!is_file($abs_safe) || filemtime($abs) > filemtime($abs_safe))) return $rel_safe;

        // Get raw contents of css-file
        $raw = file_get_contents($abs);

        // Strip comments
        $raw = preg_replace('!/\*.*?\*/!s', '', $raw);
        $raw = preg_replace('/\n\s*\n/', "\n", $raw);

        // Init parser and parse
        $parserO = new Sabberworm\CSS\Parser($raw); $rawO = $parserO->parse();

        // Setup css pseudo-selector to prepend all existing selectors
        $prepend = ':not(.extjs) > *|';

        foreach($rawO->getAllDeclarationBlocks() as $blockO)
            foreach($blockO->getSelectors() as $selectorO)
                $selectorO->setSelector($prepend . ' ' . $selectorO->getSelector());

        // Get raw css contents having every selector prepended with $prepend
        $raw = $rawO->render();

        // Write safe css into a file
        file_put_contents($abs_safe, $raw);

        // Return
        return $rel_safe;
    }
}