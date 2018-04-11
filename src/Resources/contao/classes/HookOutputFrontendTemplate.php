<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2014 Leo Feyer
 *
 * @package     Trilobit
 * @author      trilobit GmbH <https://github.com/trilobit-gmbh>
 * @license     LGPL-3.0-or-later
 * @copyright   trilobit GmbH
 */

namespace Trilobit\IfthenelseBundle;

use Contao\Controller;
use Contao\Template;

/**
 * Class HookOutputFrontendTemplate
 * @package Trilobit\IfthenelseBundle
 */
class HookOutputFrontendTemplate extends Template
{
    /**
     * @param $strBuffer
     * @param $objTemplate
     * @return mixed|string
     * @throws \Exception
     */
    public function replaceConditionalTags($strBuffer, $objTemplate)
    {
        $strBuffer = str_replace(array('{{', '}}'), array('__BRCL__', '__BRCR__'), $strBuffer);

        $arrTags = preg_split('/(\{[^}]+\})/sim', $strBuffer, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);

        if (!empty($arrTags))
        {
            $blnEval = false;
            $strReturn = '';

            // Replace tags
            foreach ($arrTags as $strTag)
            {
                $strCondition = '';

                if (strncmp($strTag, '{if', 3) === 0)
                {
                    $strCondition = preg_replace('/\{if (.*)\}/i', '<?php if ($1): ?>', $strTag);
                    $blnEval = true;
                }
                elseif (strncmp($strTag, '{elseif', 7) === 0)
                {
                    $strCondition = preg_replace('/\{elseif (.*)\}/i', '<?php elseif ($1): ?>', $strTag);
                    $blnEval = true;
                }
                elseif (strncmp($strTag, '{else', 5) === 0)
                {
                    $strCondition = '<?php else: ?>';
                    $blnEval = true;
                }
                elseif (strncmp($strTag, '{endif', 6) === 0)
                {
                    $strCondition = '<?php endif; ?>';
                    $blnEval = true;
                }

                if ($strCondition !== '')
                {
                    $strReturn .= Controller::replaceInsertTags(str_replace(array('__BRCL__', '__BRCR__', '[gt]', '&gt;', '&#62;', '[lt]', '&lt;', '&#60;', '[&]'), array('{{', '}}', '>', '>', '>', '<', '<', '<', '&'), $strCondition), false);
                }
                else
                {
                    $strReturn .= $strTag;
                }
            }

            $strBuffer = $strReturn;

            if ($blnEval)
            {
                //var_dump($strReturn);
                //die();

                ob_start();
                $blnEval = eval('?>' . $strBuffer);
                $strBuffer = ob_get_contents();
                ob_end_clean();

                if ($blnEval ===  false)
                {
                    throw new \Exception("Error eval() in Trilobit\IfthenelseBundle\HookOutputFrontendTemplate::replaceConditionalTags()");
                }
            }

        }

        $strBuffer = str_replace(array('__BRCL__', '__BRCR__'), array('{{', '}}'), $strBuffer);

        return $strBuffer;
    }
}
